<?php

namespace App\Livewire\Frontend;

use App\Models\Producto;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AddToCartForm extends Component
{
    public Producto $producto;
    public Collection $variantes;

    public ?string $selectedVariantId = null; // ID de la variante seleccionada
    public int $quantity = 1;                // Cantidad

    /**
     * Inicializa las propiedades del componente
     */
    public function mount(Producto $producto)
    {
        $this->producto = $producto;
        $this->variantes = $producto->variantes;

        // Pre-seleccionar la primera variante si existe
        $this->selectedVariantId = $this->variantes->first()?->id;
    }

    /**
     * Propiedad computada para obtener la variante seleccionada
     */
    #[Computed]
    public function selectedVariant()
    {
        // Busca en la colección que ya tenemos (muy rápido)
        return $this->variantes->firstWhere('id', $this->selectedVariantId);
    }

    /**
     * Añade el producto al carrito
     */
    public function addToCart()
    {
        // 1. Validación
        $this->validate([
            'selectedVariantId' => 'required',
            'quantity' => 'required|numeric|min:1',
        ]);

        // 2. Obtener la variante (usando nuestra propiedad computada)
        $variant = $this->selectedVariant;
        if (!$variant) {
            // Manejar error, aunque la validación debería atraparlo
            session()->flash('error', 'Por favor, selecciona una variante válida.');
            return;
        }

        // 3. Añadir al carrito usando el paquete
        \Cart::add([
            'id' => $variant->id, // ID único de la variante
            'name' => $this->producto->nombre,
            'price' => $variant->precio,
            'quantity' => $this->quantity,
            'attributes' => [
                'variant_name' => $variant->descripcion, // ej: "10 Porciones"
                'producto_id' => $this->producto->id,
                'image' => $this->producto->imagen_url ?? 'default.jpg'
            ]
        ]);

        // 4. Despachar evento para el contador del carrito (Paso 2.C)
        $this->dispatch('cartUpdated');

        // 5. Notificar al usuario
        session()->flash('message', '¡Producto añadido al carrito!');
    }

    public function render()
    {
        return view('livewire.frontend.add-to-cart-form');
    }
}
