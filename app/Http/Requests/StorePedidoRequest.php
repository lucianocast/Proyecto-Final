<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StorePedidoRequest extends FormRequest
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
            'cliente_id' => ['required', 'exists:clientes,id'],
            'status' => ['required', Rule::in(['pendiente', 'en_produccion', 'listo', 'entregado', 'cancelado'])],
            'fecha_entrega' => [
                'required',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    // Validar que la fecha de entrega sea al menos 24 horas en el futuro
                    $fechaEntrega = Carbon::parse($value);
                    $horasMinimas = 24;
                    
                    if ($fechaEntrega->lessThan(now()->addHours($horasMinimas))) {
                        $fail("La fecha de entrega debe ser al menos {$horasMinimas} horas en el futuro.");
                    }

                    // Validar que no sea domingo (día de descanso)
                    if ($fechaEntrega->isSunday()) {
                        $fail('No se pueden programar entregas los domingos.');
                    }
                },
            ],
            'forma_entrega' => ['required', Rule::in(['retiro', 'envio'])],
            'direccion_envio' => ['required_if:forma_entrega,envio', 'nullable', 'string', 'max:500'],
            'metodo_pago' => ['required', Rule::in(['total', 'seña'])],
            'monto_abonado' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $totalCalculado = $this->input('total_calculado', 0);
                    $metodoPago = $this->input('metodo_pago');

                    // Si es pago total, debe ser igual al total
                    if ($metodoPago === 'total' && $value < $totalCalculado) {
                        $fail('El monto abonado debe ser igual al total del pedido cuando el método de pago es "Pago Total".');
                    }

                    // Si es seña, debe ser al menos 30% del total
                    if ($metodoPago === 'seña') {
                        $minimoSeña = $totalCalculado * 0.3;
                        if ($value < $minimoSeña) {
                            $fail("La seña debe ser al menos el 30% del total (\$" . number_format($minimoSeña, 2) . ").");
                        }
                    }

                    // No puede ser mayor al total
                    if ($value > $totalCalculado) {
                        $fail('El monto abonado no puede ser mayor al total del pedido.');
                    }
                },
            ],
            'total_calculado' => ['required', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',
            'fecha_entrega.required' => 'La fecha de entrega es obligatoria.',
            'fecha_entrega.after' => 'La fecha de entrega debe ser posterior a la fecha actual.',
            'forma_entrega.required' => 'Debe seleccionar una forma de entrega.',
            'direccion_envio.required_if' => 'La dirección de envío es obligatoria cuando se selecciona envío a domicilio.',
            'monto_abonado.required' => 'El monto abonado es obligatorio.',
            'monto_abonado.numeric' => 'El monto abonado debe ser un número.',
            'monto_abonado.min' => 'El monto abonado no puede ser negativo.',
        ];
    }
}
