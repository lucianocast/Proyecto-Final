<x-filament-panels::page>
    {{-- Usamos wire:submit.prevent para llamar al método 'registrarProduccion' --}}
    <form wire:submit.prevent="registrarProduccion">
        
        {{-- Esto renderiza el formulario que definimos en el PHP --}}
        {{ $this->form }}

        <div class="mt-6">
            {{-- Esto renderiza el botón "Registrar" que definimos en el PHP --}}
            {{ $this->registrarProduccionAction }}
        </div>
    </form>
</x-filament-panels::page>