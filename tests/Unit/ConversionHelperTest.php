<?php

namespace Tests\Unit;

use App\Enums\UnidadMedida;
use App\Helpers\ConversionHelper;
use PHPUnit\Framework\TestCase;

class ConversionHelperTest extends TestCase
{
    /** @test */
    public function puede_convertir_kilogramos_a_gramos()
    {
        $resultado = ConversionHelper::convertirABase(
            cantidad: 10,
            unidadCompra: UnidadMedida::KILOGRAMO,
            unidadBase: UnidadMedida::GRAMO
        );

        $this->assertEquals(10000, $resultado);
    }

    /** @test */
    public function puede_convertir_gramos_a_kilogramos()
    {
        $resultado = ConversionHelper::convertirABase(
            cantidad: 5000,
            unidadCompra: UnidadMedida::GRAMO,
            unidadBase: UnidadMedida::KILOGRAMO
        );

        $this->assertEquals(5, $resultado);
    }

    /** @test */
    public function puede_convertir_litros_a_mililitros()
    {
        $resultado = ConversionHelper::convertirABase(
            cantidad: 2.5,
            unidadCompra: UnidadMedida::LITRO,
            unidadBase: UnidadMedida::MILILITRO
        );

        $this->assertEquals(2500, $resultado);
    }

    /** @test */
    public function puede_convertir_mililitros_a_litros()
    {
        $resultado = ConversionHelper::convertirABase(
            cantidad: 1500,
            unidadCompra: UnidadMedida::MILILITRO,
            unidadBase: UnidadMedida::LITRO
        );

        $this->assertEquals(1.5, $resultado);
    }

    /** @test */
    public function no_convierte_si_son_la_misma_unidad()
    {
        $resultado = ConversionHelper::convertirABase(
            cantidad: 100,
            unidadCompra: UnidadMedida::GRAMO,
            unidadBase: UnidadMedida::GRAMO
        );

        $this->assertEquals(100, $resultado);
    }

    /** @test */
    public function lanza_excepcion_si_intenta_convertir_peso_a_volumen()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Conversión incompatible');

        ConversionHelper::convertirABase(
            cantidad: 1,
            unidadCompra: UnidadMedida::KILOGRAMO,
            unidadBase: UnidadMedida::LITRO
        );
    }

    /** @test */
    public function lanza_excepcion_si_intenta_convertir_volumen_a_peso()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Conversión incompatible');

        ConversionHelper::convertirABase(
            cantidad: 1,
            unidadCompra: UnidadMedida::LITRO,
            unidadBase: UnidadMedida::GRAMO
        );
    }

    /** @test */
    public function valida_que_unidades_de_peso_son_compatibles()
    {
        $compatible = ConversionHelper::sonCompatibles(
            UnidadMedida::KILOGRAMO,
            UnidadMedida::GRAMO
        );

        $this->assertTrue($compatible);
    }

    /** @test */
    public function valida_que_unidades_de_volumen_son_compatibles()
    {
        $compatible = ConversionHelper::sonCompatibles(
            UnidadMedida::LITRO,
            UnidadMedida::MILILITRO
        );

        $this->assertTrue($compatible);
    }

    /** @test */
    public function valida_que_peso_y_volumen_no_son_compatibles()
    {
        $compatible = ConversionHelper::sonCompatibles(
            UnidadMedida::KILOGRAMO,
            UnidadMedida::LITRO
        );

        $this->assertFalse($compatible);
    }

    /** @test */
    public function calcula_factor_de_conversion_correctamente()
    {
        // 1 kilogramo = 1000 gramos
        $factor = ConversionHelper::calcularFactorConversion(
            UnidadMedida::KILOGRAMO,
            UnidadMedida::GRAMO
        );

        $this->assertEquals(1000, $factor);
    }

    /** @test */
    public function identifica_tipo_de_unidad_peso()
    {
        $tipo = ConversionHelper::getTipoUnidad(UnidadMedida::KILOGRAMO);
        $this->assertEquals('peso', $tipo);

        $tipo = ConversionHelper::getTipoUnidad(UnidadMedida::GRAMO);
        $this->assertEquals('peso', $tipo);
    }

    /** @test */
    public function identifica_tipo_de_unidad_volumen()
    {
        $tipo = ConversionHelper::getTipoUnidad(UnidadMedida::LITRO);
        $this->assertEquals('volumen', $tipo);

        $tipo = ConversionHelper::getTipoUnidad(UnidadMedida::MILILITRO);
        $this->assertEquals('volumen', $tipo);
    }

    /** @test */
    public function identifica_tipo_de_unidad_unidad()
    {
        $tipo = ConversionHelper::getTipoUnidad(UnidadMedida::UNIDAD);
        $this->assertEquals('unidad', $tipo);
    }
}
