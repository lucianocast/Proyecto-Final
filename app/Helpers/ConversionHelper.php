<?php

namespace App\Helpers;

use App\Enums\UnidadMedida;

class ConversionHelper
{
    /**
     * Convierte una cantidad de una unidad de compra a la unidad base del insumo.
     * 
     * @param float $cantidad Cantidad a convertir
     * @param UnidadMedida $unidadCompra Unidad en que se compra
     * @param UnidadMedida $unidadBase Unidad base del insumo
     * @return float Cantidad convertida a la unidad base
     * @throws \Exception Si la conversión no es compatible
     */
    public static function convertirABase(
        float $cantidad,
        UnidadMedida $unidadCompra,
        UnidadMedida $unidadBase
    ): float {
        // Si son la misma unidad, no hay que convertir
        if ($unidadCompra === $unidadBase) {
            return $cantidad;
        }

        // Validar que las unidades sean del mismo tipo (peso con peso, volumen con volumen)
        if (!self::sonCompatibles($unidadCompra, $unidadBase)) {
            throw new \Exception(
                "Conversión incompatible: no se puede convertir {$unidadCompra->getLabel()} a {$unidadBase->getLabel()}. " .
                "Las unidades deben ser del mismo tipo (peso con peso, volumen con volumen)."
            );
        }

        // Conversiones de PESO
        if ($unidadCompra === UnidadMedida::KILOGRAMO && $unidadBase === UnidadMedida::GRAMO) {
            return $cantidad * 1000;
        }
        if ($unidadCompra === UnidadMedida::GRAMO && $unidadBase === UnidadMedida::KILOGRAMO) {
            return $cantidad / 1000;
        }

        // Conversiones de VOLUMEN
        if ($unidadCompra === UnidadMedida::LITRO && $unidadBase === UnidadMedida::MILILITRO) {
            return $cantidad * 1000;
        }
        if ($unidadCompra === UnidadMedida::MILILITRO && $unidadBase === UnidadMedida::LITRO) {
            return $cantidad / 1000;
        }

        // Si son unidades, devolver la misma cantidad
        if ($unidadCompra === UnidadMedida::UNIDAD && $unidadBase === UnidadMedida::UNIDAD) {
            return $cantidad;
        }

        // Si llegamos aquí, la conversión no está soportada
        throw new \Exception(
            "Conversión no soportada: {$unidadCompra->getLabel()} a {$unidadBase->getLabel()}"
        );
    }

    /**
     * Valida que dos unidades sean compatibles para conversión.
     * Solo se puede convertir peso con peso, volumen con volumen, o unidades entre sí.
     * 
     * @param UnidadMedida $unidad1
     * @param UnidadMedida $unidad2
     * @return bool
     */
    public static function sonCompatibles(UnidadMedida $unidad1, UnidadMedida $unidad2): bool
    {
        // Misma unidad siempre es compatible
        if ($unidad1 === $unidad2) {
            return true;
        }

        $unidadesPeso = [UnidadMedida::GRAMO, UnidadMedida::KILOGRAMO];
        $unidadesVolumen = [UnidadMedida::MILILITRO, UnidadMedida::LITRO];

        // Ambas son de peso
        if (in_array($unidad1, $unidadesPeso) && in_array($unidad2, $unidadesPeso)) {
            return true;
        }

        // Ambas son de volumen
        if (in_array($unidad1, $unidadesVolumen) && in_array($unidad2, $unidadesVolumen)) {
            return true;
        }

        // Ambas son unidades
        if ($unidad1 === UnidadMedida::UNIDAD && $unidad2 === UnidadMedida::UNIDAD) {
            return true;
        }

        return false;
    }

    /**
     * Calcula el factor de conversión automático basado en las unidades.
     * 
     * @param UnidadMedida $unidadCompra
     * @param UnidadMedida $unidadBase
     * @return float Factor de conversión
     */
    public static function calcularFactorConversion(
        UnidadMedida $unidadCompra,
        UnidadMedida $unidadBase
    ): float {
        return self::convertirABase(1, $unidadCompra, $unidadBase);
    }

    /**
     * Obtiene el tipo de unidad (peso, volumen, unidad).
     * 
     * @param UnidadMedida $unidad
     * @return string
     */
    public static function getTipoUnidad(UnidadMedida $unidad): string
    {
        return match ($unidad) {
            UnidadMedida::GRAMO, UnidadMedida::KILOGRAMO => 'peso',
            UnidadMedida::MILILITRO, UnidadMedida::LITRO => 'volumen',
            UnidadMedida::UNIDAD => 'unidad',
        };
    }
}
