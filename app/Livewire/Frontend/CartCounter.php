<?php

namespace App\Livewire\Frontend;

use Livewire\Attributes\On;
use Livewire\Component;

class CartCounter extends Component
{
    /**
     * Este método se registrará como un "listener"
     * para el evento 'cartUpdated'.
     *
     * Cuando se reciba el evento, Livewire
     * automáticamente re-renderizará este componente.
     */
    #[On('cartUpdated')]
    public function refreshCart()
    {
        // No se necesita código aquí.
        // La simple recepción del evento es suficiente
        // para que Livewire ejecute de nuevo el método render().
    }

    /**
     * Renderiza el componente.
     * Se ejecuta al cargar la página y
     * cada vez que se recibe el evento 'cartUpdated'.
     */
    public function render()
    {
        // Obtener la cantidad total de items del carrito
        $count = \Cart::getTotalQuantity() ?? 0;

        return view('livewire.frontend.cart-counter', [
            'count' => $count
        ]);
    }
}
