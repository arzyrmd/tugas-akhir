<?php

namespace App\Filament\Resources\DeliveryBatchResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Order;
use App\Models\CustomProductRequest;
use App\Models\DeliveryItem;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Pesanan dalam Batch';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('deliverable_type')
                    ->label('Jenis')
                    ->options([
                        Order::class => 'Pesanan Reguler',
                        CustomProductRequest::class => 'Produk Kustom',
                    ])
                    ->required()
                    ->live(),

                Forms\Components\Select::make('deliverable_id')
                    ->label('Pesanan')
                    ->options(function (Forms\Get $get) {
                        $type = $get('deliverable_type');
                        $batch = $this->getOwnerRecord();
                        $cityIds = $batch->area->cities()->pluck('cities.id')->toArray();

                        if ($type === Order::class) {
                            return Order::where('status', 'SIAP DIKIRIM')
                                ->whereIn('city_id', $cityIds)
                                ->whereDoesntHave('deliveryItems')
                                ->get()
                                ->mapWithKeys(function ($order) {
                                    return [
                                        $order->id => "#{$order->payment_code} - {$order->full_name} - {$order->city->name}"
                                    ];
                                });
                        } elseif ($type === CustomProductRequest::class) {
                            return CustomProductRequest::where('status', 'SIAP_DIKIRIM')
                                ->whereHas('shipment', function ($query) use ($cityIds) {
                                    $query->whereIn('city_id', $cityIds);
                                })
                                ->whereDoesntHave('deliveryItems')
                                ->get()
                                ->mapWithKeys(function ($request) {
                                    return [
                                        $request->id => "{$request->title} - {$request->user->name} - {$request->shipment->city->name}"
                                    ];
                                });
                        }

                        return [];
                    })
                    ->required()
                    ->searchable(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'BELUM_DIKIRIM' => 'Belum Dikirim',
                        'DIKIRIM' => 'Dikirim',
                        'SELESAI' => 'Selesai',
                    ])
                    ->default('BELUM_DIKIRIM')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID Item')
                    ->sortable(),

                Tables\Columns\TextColumn::make('deliverable_type')
                    ->label('Jenis')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Order::class => 'Pesanan Reguler',
                        CustomProductRequest::class => 'Produk Kustom',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Order::class => 'primary',
                        CustomProductRequest::class => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('order_info')
                    ->label('Info Pesanan')
                    ->getStateUsing(function (DeliveryItem $record) {
                        if ($record->deliverable_type === Order::class) {
                            $order = $record->deliverable;
                            return "#{$order->payment_code} - {$order->full_name}";
                        } elseif ($record->deliverable_type === CustomProductRequest::class) {
                            $request = $record->deliverable;
                            return "{$request->title} - {$request->user->name}";
                        }
                        return '-';
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_address')
                    ->label('Alamat')
                    ->getStateUsing(function (DeliveryItem $record) {
                        if ($record->deliverable_type === Order::class) {
                            $order = $record->deliverable;
                            return "{$order->city->name} - " . substr($order->address, 0, 50) . '...';
                        } elseif ($record->deliverable_type === CustomProductRequest::class) {
                            $request = $record->deliverable;
                            return "{$request->shipment->city->name} - " . substr($request->shipment->address, 0, 50) . '...';
                        }
                        return '-';
                    })
                    ->limit(50),

                Tables\Columns\TextColumn::make('order_total')
                    ->label('Total')
                    ->getStateUsing(function (DeliveryItem $record) {
                        if ($record->deliverable_type === Order::class) {
                            return $record->deliverable->total;
                        } elseif ($record->deliverable_type === CustomProductRequest::class) {
                            return $record->deliverable->quoted_price;
                        }
                        return 0;
                    })
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'BELUM_DIKIRIM' => 'warning',
                        'DIKIRIM' => 'primary',
                        'SELESAI' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('deliverable_type')
                    ->label('Jenis Pesanan')
                    ->options([
                        Order::class => 'Pesanan Reguler',
                        CustomProductRequest::class => 'Produk Kustom',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'BELUM_DIKIRIM' => 'Belum Dikirim',
                        'DIKIRIM' => 'Dikirim',
                        'SELESAI' => 'Selesai',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Pesanan')
                    ->visible(function () {
                        $batch = $this->getOwnerRecord();
                        return $batch->status === 'DIJADWALKAN' &&
                               $batch->items()->count() < 10; // MAX_ITEMS_PER_BATCH
                    }),
            ])
            ->actions([
                Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status Baru')
                            ->options([
                                'BELUM_DIKIRIM' => 'Belum Dikirim',
                                'DIKIRIM' => 'Dikirim',
                                'SELESAI' => 'Selesai',
                            ])
                            ->required()
                            ->default(fn (DeliveryItem $record) => $record->status),
                    ])
                    ->action(function (array $data, DeliveryItem $record) {
                        $record->update(['status' => $data['status']]);

                        Notification::make()
                            ->title('Status pesanan berhasil diperbarui')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(function () {
                        $batch = $this->getOwnerRecord();
                        return $batch->status === 'DIJADWALKAN';
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label('Update Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status Baru')
                                ->options([
                                    'BELUM_DIKIRIM' => 'Belum Dikirim',
                                    'DIKIRIM' => 'Dikirim',
                                    'SELESAI' => 'Selesai',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->update(['status' => $data['status']]);
                            }

                            Notification::make()
                                ->title(count($records) . ' pesanan berhasil diperbarui')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(function () {
                            $batch = $this->getOwnerRecord();
                            return $batch->status === 'DIJADWALKAN';
                        }),
                ]),
            ])
            ->emptyStateHeading('Belum ada pesanan dalam batch ini')
            ->emptyStateDescription('Tambahkan pesanan menggunakan tombol "Tambah Pesanan" di atas.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}
