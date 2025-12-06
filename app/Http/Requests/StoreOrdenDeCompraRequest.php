<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StoreOrdenDeCompraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'proveedor_id' => ['required', 'exists:proveedors,id'],
            'status' => ['required', Rule::in(['pendiente', 'aprobada', 'recibida', 'cancelada'])],
            'fecha_emision' => ['required', 'date', 'before_or_equal:today'],
            'fecha_entrega_esperada' => [
                'required',
                'date',
                'after:fecha_emision',
                function ($attribute, $value, $fail) {
                    $fechaEntrega = Carbon::parse($value);
                    $fechaEmision = Carbon::parse($this->input('fecha_emision'));

                    // La entrega debe ser al menos 2 días después de la emisión
                    if ($fechaEntrega->lessThan($fechaEmision->addDays(2))) {
                        $fail('La fecha de entrega esperada debe ser al menos 2 días después de la fecha de emisión.');
                    }

                    // No programar entregas en domingo
                    if ($fechaEntrega->isSunday()) {
                        $fail('No se pueden programar entregas los domingos.');
                    }
                },
            ],
            'total_calculado' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.insumo_id' => ['required', 'exists:insumos,id'],
            'items.*.cantidad' => ['required', 'numeric', 'min:0.01'],
            'items.*.precio_unitario' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'proveedor_id.required' => 'Debe seleccionar un proveedor.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.before_or_equal' => 'La fecha de emisión no puede ser futura.',
            'fecha_entrega_esperada.required' => 'La fecha de entrega esperada es obligatoria.',
            'fecha_entrega_esperada.after' => 'La fecha de entrega debe ser posterior a la fecha de emisión.',
            'items.required' => 'Debe agregar al menos un insumo a la orden.',
            'items.min' => 'Debe agregar al menos un insumo a la orden.',
            'items.*.insumo_id.required' => 'Debe seleccionar un insumo.',
            'items.*.insumo_id.exists' => 'El insumo seleccionado no existe.',
            'items.*.cantidad.required' => 'La cantidad es obligatoria.',
            'items.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'items.*.precio_unitario.required' => 'El precio unitario es obligatorio.',
            'items.*.precio_unitario.min' => 'El precio unitario no puede ser negativo.',
        ];
    }
}
