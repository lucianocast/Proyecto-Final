<?php

namespace App\Livewire\Frontend;

use Livewire\Attributes\Layout;
use Livewire\Component;

class CartView extends Component
{
    // Propiedades públicas para almacenar los datos del carrito
    public $cartItems;
    public $subtotal;
    public $total;

    /**
     * Método mount: se ejecuta al cargar el componente.
     * Carga el estado inicial del carrito.
     */
    public function mount()
    {
        $this->loadCartData();
    }

    /**
     * Carga los datos del carrito en las propiedades públicas.
     */
    public function loadCartData()
    {
        $this->cartItems = \Cart::getContent()->sortBy('name');
        $this->subtotal = \Cart::getSubTotal();
        $this->total = \Cart::getTotal();
    }

    /**
     * Acción: Aumenta la cantidad de un ítem.
     */
    public function increaseQuantity(string $itemId)
    {
        \Cart::update($itemId, [
            'quantity' => +1 // El '+' indica que es un cambio relativo
        ]);

        $this->refreshComponentData();
    }

    /**
     * Acción: Disminuye la cantidad de un ítem.
     */
    public function decreaseQuantity(string $itemId)
    {
        // Opcional: Lógica para no dejar bajar de 1
        $item = \Cart::get($itemId);
        if ($item->quantity > 1) {
            \Cart::update($itemId, [
                'quantity' => -1 // El '-' indica que es un cambio relativo
            ]);
        }

        $this->refreshComponentData();
    }

    /**
     * Acción: Elimina un ítem del carrito.
     */
    public function removeItem(string $itemId)
    {
        \Cart::remove($itemId);

        $this->refreshComponentData();
    }

    /**
     * Helper: Refresca los datos del componente Y
     * notifica al CartCounter.
     */
    private function refreshComponentData()
    {
        $this->loadCartData();
        $this->dispatch('cartUpdated'); // ¡Muy importante!
    }

    /**
     * Renderiza la vista y le dice que use un layout de Breeze.
     */
    #[Layout('layouts.guest')] // Le dice a Livewire que use el layout de invitado
    public function render()
    {
        return view('livewire.frontend.cart-view');
    }
}
