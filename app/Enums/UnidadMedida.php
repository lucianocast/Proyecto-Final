<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UnidadMedida: string implements HasLabel
{
    case GRAMO = 'g';
    case KILOGRAMO = 'kg';
    case MILILITRO = 'ml';
    case LITRO = 'l';
    case UNIDAD = 'u';

    public function getLabel(): string
    {
        return match($this) {
            self::GRAMO => 'Gramos (g)',
            self::KILOGRAMO => 'Kilogramos (kg)',
            self::MILILITRO => 'Mililitros (ml)',
            self::LITRO => 'Litros (l)',
            self::UNIDAD => 'Unidades (u)',
        };
    }
}
