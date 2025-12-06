<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Pedido;

class UpdatePedidoRequest extends FormRequest
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
        $pedidoId = $this->route('pedido') ?? $this->route('record');
        $pedido = Pedido::find($pedidoId);

        return [
            'cliente_id' => ['sometimes', 'exists:clientes,id'],
            'status' => [
                'sometimes',
                Rule::in(['pendiente', 'en_produccion', 'listo', 'entregado', 'cancelado']),
                function ($attribute, $value, $fail) use ($pedido) {
                    if (!$pedido) return;

                    // Validar transiciones de estado permitidas
                    $validTransitions = [
                        'pendiente' => ['en_produccion', 'cancelado'],
                        'en_produccion' => ['listo', 'cancelado'],
                        'listo' => ['entregado', 'cancelado'],
                        'entregado' => [],
                        'cancelado' => [],
                    ];

                    $currentStatus = $pedido->status;
                    $allowedTransitions = $validTransitions[$currentStatus] ?? [];

                    if ($value !== $currentStatus && !in_array($value, $allowedTransitions)) {
                        $fail("No se puede cambiar el estado de '{$currentStatus}' a '{$value}'. Estados permitidos: " . implode(', ', $allowedTransitions));
                    }
                },
            ],
            'fecha_entrega' => [
                'sometimes',
                'date',
                function ($attribute, $value, $fail) use ($pedido) {
                    $fechaEntrega = Carbon::parse($value);

                    // Si el pedido ya está entregado, no permitir cambio de fecha
                    if ($pedido && $pedido->status === 'entregado') {
                        $fail('No se puede modificar la fecha de un pedido ya entregado.');
                        return;
                    }

                    // Si el pedido está pendiente o en producción, validar tiempo mínimo
                    if ($pedido && in_array($pedido->status, ['pendiente', 'en_produccion'])) {
                        if ($fechaEntrega->lessThan(now()->addHours(12))) {
                            $fail('La fecha de entrega debe ser al menos 12 horas en el futuro.');
                        }
                    }

                    // Validar que no sea domingo
                    if ($fechaEntrega->isSunday()) {
                        $fail('No se pueden programar entregas los domingos.');
                    }
                },
            ],
            'forma_entrega' => ['sometimes', Rule::in(['retiro', 'envio'])],
            'direccion_envio' => ['required_if:forma_entrega,envio', 'nullable', 'string', 'max:500'],
            'metodo_pago' => ['sometimes', Rule::in(['total', 'seña'])],
            'monto_abonado' => [
                'sometimes',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $totalCalculado = $this->input('total_calculado', 0);
                    $metodoPago = $this->input('metodo_pago');

                    if ($metodoPago === 'total' && $value < $totalCalculado) {
                        $fail('El monto abonado debe ser igual al total cuando el método de pago es "Pago Total".');
                    }

                    if ($metodoPago === 'seña') {
                        $minimoSeña = $totalCalculado * 0.3;
                        if ($value < $minimoSeña) {
                            $fail("La seña debe ser al menos el 30% del total (\$" . number_format($minimoSeña, 2) . ").");
                        }
                    }

                    if ($value > $totalCalculado) {
                        $fail('El monto abonado no puede ser mayor al total del pedido.');
                    }
                },
            ],
            'total_calculado' => ['sometimes', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'El cliente seleccionado no existe.',
            'fecha_entrega.after' => 'La fecha de entrega debe ser posterior a la fecha actual.',
            'direccion_envio.required_if' => 'La dirección de envío es obligatoria cuando se selecciona envío a domicilio.',
            'monto_abonado.numeric' => 'El monto abonado debe ser un número.',
            'monto_abonado.min' => 'El monto abonado no puede ser negativo.',
        ];
    }
}
