<?php

namespace App\Filament\Widgets;

use App\Models\PadreEspiritual;
use App\Models\Visita;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisitasOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $hoy = Visita::whereDate('created_at', today())->count();
        $total = Visita::count();
        $sinSeguimiento = Visita::where('estatus', 'nuevo')->count();
        $padres = PadreEspiritual::count();

        return [
            Stat::make('Visitas hoy', $hoy)
                ->description('Registradas el día de hoy')
                ->color('success')
                ->icon('heroicon-o-user-plus'),

            Stat::make('Total de visitas', $total)
                ->description('Histórico general')
                ->color('primary')
                ->icon('heroicon-o-users'),

            Stat::make('Pendientes de seguimiento', $sinSeguimiento)
                ->description('Estatus: Nuevo')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Padres Espirituales', $padres)
                ->description('Registrados en el sistema')
                ->color('info')
                ->icon('heroicon-o-heart'),
        ];
    }
}
