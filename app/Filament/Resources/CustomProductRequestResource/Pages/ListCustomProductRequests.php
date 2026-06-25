<?php

namespace App\Filament\Resources\CustomProductRequestResource\Pages;

use App\Filament\Resources\CustomProductRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCustomProductRequests extends ListRecords
{
    protected static string $resource = CustomProductRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CustomProductRequestResource\Widgets\CustomProductOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(CustomProductRequestResource::getEloquentQuery()->count()),

            'menunggu_review' => Tab::make('Menunggu Review')
                ->badge(CustomProductRequestResource::getEloquentQuery()->where('status', 'MENUNGGU_REVIEW')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'MENUNGGU_REVIEW')),

            'penawaran_diberikan' => Tab::make('Penawaran Diberikan')
                ->badge(CustomProductRequestResource::getEloquentQuery()->where('status', 'PENAWARAN_DIBERIKAN')->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'PENAWARAN_DIBERIKAN')),

            'menunggu_dp' => Tab::make('Menunggu DP')
                ->badge(CustomProductRequestResource::getEloquentQuery()->where('status', 'MENUNGGU_DP')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'MENUNGGU_DP')),

            'dalam_pengerjaan' => Tab::make('Dalam Pengerjaan')
                ->badge(CustomProductRequestResource::getEloquentQuery()->where('status', 'DALAM_PENGERJAAN')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'DALAM_PENGERJAAN')),

            'menunggu_pelunasan' => Tab::make('Menunggu Pelunasan')
                ->badge(CustomProductRequestResource::getEloquentQuery()->where('status', 'MENUNGGU_PELUNASAN')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'MENUNGGU_PELUNASAN')),

            'siap_dikirim' => Tab::make('Siap Dikirim')
                ->badge(CustomProductRequestResource::getEloquentQuery()->where('status', 'SIAP_DIKIRIM')->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'SIAP_DIKIRIM')),

            'dikirim' => Tab::make('Dikirim')
                ->badge(CustomProductRequestResource::getEloquentQuery()->where('status', 'DIKIRIM')->count())
                ->badgeColor('purple')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'DIKIRIM')),

            'selesai' => Tab::make('Selesai')
                ->badge(CustomProductRequestResource::getEloquentQuery()->where('status', 'SELESAI')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'SELESAI')),

            'dibatalkan' => Tab::make('Dibatalkan')
                ->badge(CustomProductRequestResource::getEloquentQuery()->where('status', 'DIBATALKAN')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'DIBATALKAN')),
        ];
    }
}
