<?php

namespace App\Filament\Admin\Resources\ClienteResource\Pages;

use App\Filament\Admin\Resources\ClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;

class ViewCliente extends ViewRecord
{
    protected static string $resource = ClienteResource::class;

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
                Section::make('Información del Cliente')
                    ->schema([
                        TextEntry::make('nombre')
                            ->label('Nombre Completo')
                            ->size('lg')
                            ->weight('bold'),
                        TextEntry::make('email')
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
                Section::make('Estadísticas de Fidelización')
                    ->schema([
                        TextEntry::make('pedidos_count')
                            ->label('Total de Pedidos')
                            ->state(fn ($record) => $record->pedidos()->count())
                            ->badge()
                            ->color('info'),
                        TextEntry::make('total_gastado')
                            ->label('Total Gastado')
                            ->state(fn ($record) => $record->pedidos()
                                ->whereIn('status', ['entregado', 'completado'])
                                ->sum('total_calculado'))
                            ->money('ARS')
                            ->badge()
                            ->color('success'),
                        TextEntry::make('pedidos_pendientes')
                            ->label('Pedidos Pendientes')
                            ->state(fn ($record) => $record->pedidos()
                                ->whereIn('status', ['pendiente', 'confirmado'])
                                ->count())
                            ->badge()
                            ->color('warning'),
                        TextEntry::make('producto_favorito')
                            ->label('Producto Más Comprado')
                            ->state(function ($record) {
                                $producto = \DB::table('pedido_items')
                                    ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
                                    ->join('producto_variantes', 'pedido_items.producto_variante_id', '=', 'producto_variantes.id')
                                    ->join('productos', 'producto_variantes.producto_id', '=', 'productos.id')
                                    ->where('pedidos.cliente_id', $record->id)
                                    ->select('productos.nombre', \DB::raw('COUNT(*) as total'))
                                    ->groupBy('productos.id', 'productos.nombre')
                                    ->orderBy('total', 'desc')
                                    ->first();
                                
                                return $producto ? $producto->nombre . " ({$producto->total})" : 'N/A';
                            })
                            ->badge()
                            ->color('primary'),
                    ])
                    ->columns(4),
            ]);
    }
}
