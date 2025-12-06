<?php

namespace App\Filament\Admin\Resources\ProveedorResource\Pages;

use App\Filament\Admin\Resources\ProveedorResource;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewProveedor extends ViewRecord
{
    protected static string $resource = ProveedorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información del Proveedor')
                    ->schema([
                        TextEntry::make('nombre_empresa')
                            ->label('Razón Social')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        TextEntry::make('cuit')
                            ->label('CUIT')
                            ->icon('heroicon-o-identification')
                            ->copyable(),
                        TextEntry::make('nombre_contacto')
                            ->label('Contacto')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('email_pedidos')
                            ->label('Email')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),
                        TextEntry::make('telefono')
                            ->label('Teléfono')
                            ->icon('heroicon-o-phone')
                            ->copyable(),
                        TextEntry::make('direccion')
                            ->label('Dirección')
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull(),
                        TextEntry::make('notas')
                            ->label('Condiciones Comerciales')
                            ->columnSpanFull(),
                        IconEntry::make('activo')
                            ->label('Estado')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        TextEntry::make('created_at')
                            ->label('Fecha de Registro')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
                Section::make('Estadísticas de Compras (UC-48)')
                    ->schema([
                        TextEntry::make('total_ordenes')
                            ->label('Total de Órdenes de Compra')
                            ->state(function ($record) {
                                return $record->ordenesDeCompra()->count();
                            })
                            ->badge()
                            ->color('info'),
                        TextEntry::make('ordenes_pendientes')
                            ->label('Órdenes Pendientes')
                            ->state(function ($record) {
                                return $record->ordenesDeCompra()
                                    ->where('status', 'Pendiente')
                                    ->count();
                            })
                            ->badge()
                            ->color('warning'),
                        TextEntry::make('total_gastado')
                            ->label('Total Gastado')
                            ->state(function ($record) {
                                return $record->ordenesDeCompra()
                                    ->where('status', 'Recibida')
                                    ->sum('total_calculado');
                            })
                            ->money('ARS')
                            ->badge()
                            ->color('success'),
                        TextEntry::make('insumos_que_provee')
                            ->label('Cantidad de Insumos')
                            ->state(function ($record) {
                                return $record->insumos()->count();
                            })
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('ultima_compra')
                            ->label('Última Compra')
                            ->state(function ($record) {
                                $ultimaOC = $record->ordenesDeCompra()
                                    ->orderBy('fecha_emision', 'desc')
                                    ->first();
                                return $ultimaOC ? $ultimaOC->fecha_emision->format('d/m/Y') : 'N/A';
                            })
                            ->badge(),
                    ])
                    ->columns(3),
            ]);
    }
}
