<?php

namespace App\Notifications;

use App\Models\OrdenDeCompra;
use App\Models\Insumo;
use App\Models\Proveedor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrdenCompraAutomaticaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $orden;
    public $insumo;
    public $proveedor;

    public function __construct(OrdenDeCompra $orden, Insumo $insumo, Proveedor $proveedor)
    {
        $this->orden = $orden;
        $this->insumo = $insumo;
        $this->proveedor = $proveedor;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🤖 Orden de Compra Generada Automáticamente - OC #' . $this->orden->id)
            ->greeting('¡Orden de Compra Automática!')
            ->line('El sistema inteligente de planificación de compras ha detectado un nivel crítico de stock y ha generado automáticamente una orden de compra.')
            ->line('')
            ->line('**Detalles de la Orden:**')
            ->line('• **ID de Orden:** #' . $this->orden->id)
            ->line('• **Insumo:** ' . $this->insumo->nombre)
            ->line('• **Cantidad:** ' . $this->orden->items->first()->cantidad . ' ' . $this->insumo->unidad_medida)
            ->line('• **Proveedor Seleccionado:** ' . $this->proveedor->nombre_empresa)
            ->line('• **CUIT:** ' . $this->proveedor->cuit)
            ->line('• **Email:** ' . $this->proveedor->email_pedidos)
            ->line('• **Teléfono:** ' . $this->proveedor->telefono)
            ->line('')
            ->line('**Detalles Comerciales:**')
            ->line('• **Precio Unitario:** $' . number_format($this->orden->items->first()->precio_unitario, 2))
            ->line('• **Total:** $' . number_format($this->orden->total_calculado, 2))
            ->line('• **Fecha de Entrega Esperada:** ' . $this->orden->fecha_entrega_esperada->format('d/m/Y'))
            ->line('')
            ->line('**Estado del Insumo:**')
            ->line('• **Stock Actual:** ' . $this->insumo->cantidad_disponible . ' ' . $this->insumo->unidad_medida)
            ->line('• **Stock Mínimo:** ' . $this->insumo->stock_minimo . ' ' . $this->insumo->unidad_medida)
            ->line('')
            ->action('Ver Orden de Compra', url('/admin/orden-de-compras/' . $this->orden->id))
            ->line('El sistema ha evaluado múltiples criterios (precio, historial de cumplimiento y tiempo de entrega) para seleccionar el proveedor más conveniente.')
            ->line('')
            ->line('**Acciones Requeridas:**')
            ->line('1. Revisar los detalles de la orden en el panel de administración')
            ->line('2. Confirmar la orden con el proveedor')
            ->line('3. Actualizar el estado cuando se reciba la mercadería')
            ->line('')
            ->salutation('Saludos, Sistema Inteligente de Gestión de Pastelería');
    }

    public function toArray($notifiable): array
    {
        return [
            'tipo' => 'orden_compra_automatica',
            'orden_id' => $this->orden->id,
            'insumo_id' => $this->insumo->id,
            'insumo_nombre' => $this->insumo->nombre,
            'proveedor_id' => $this->proveedor->id,
            'proveedor_nombre' => $this->proveedor->nombre_empresa,
            'cantidad' => (float) $this->orden->items->first()->cantidad,
            'total' => (float) $this->orden->total_calculado,
            'fecha_entrega' => $this->orden->fecha_entrega_esperada->format('Y-m-d'),
            'mensaje' => sprintf(
                'Se genero automaticamente la OC #%d para %s al proveedor %s',
                $this->orden->id,
                $this->insumo->nombre,
                $this->proveedor->nombre_empresa
            ),
        ];
    }
}
