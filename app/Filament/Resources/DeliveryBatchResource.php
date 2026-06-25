<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryBatchResource\Pages;
use App\Filament\Resources\DeliveryBatchResource\RelationManagers;
use App\Models\DeliveryBatch;
use App\Models\Order;
use App\Models\CustomProductRequest;
use App\Models\DeliveryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;

class DeliveryBatchResource extends Resource
{
    protected static ?string $model = DeliveryBatch::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Batch Pengiriman';
    protected static ?string $navigationGroup = 'Pengiriman';
    protected static ?string $pluralModelLabel = 'Batch Pengiriman';
    protected static ?string $modelLabel = 'Batch Pengiriman';
    protected static ?int $navigationSort = 1;

    // Konstanta: jumlah pesanan maksimal dalam satu batch
    const MAX_ITEMS_PER_BATCH = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Step::make('Informasi Dasar')
                        ->description('Masukkan informasi dasar batch pengiriman')
                        ->schema([
                            Forms\Components\DatePicker::make('scheduled_date')
                                ->label('Tanggal Pengiriman')
                                ->required()
                                ->minDate(now())
                                ->placeholder('Pilih tanggal pengiriman')
                                ->helperText('Minimal tanggal hari ini'),
                            Forms\Components\Select::make('delivery_area_id')
                                ->label('Area Pengiriman')
                                ->relationship('area', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->placeholder('Pilih area pengiriman'),
                            Forms\Components\TextInput::make('driver_name')
                                ->label('Nama Pengirim/Driver')
                                ->maxLength(255)
                                ->placeholder('Contoh: Ahmad Susanto'),
                        ])
                        ->columns(2),

                    Step::make('Pengaturan & Catatan')
                        ->description('Status dan catatan tambahan untuk batch')
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'DIJADWALKAN' => 'Dijadwalkan',
                                    'DALAM_PERJALANAN' => 'Dalam Perjalanan',
                                    'SELESAI' => 'Selesai',
                                ])
                                ->default('DIJADWALKAN')
                                ->required()
                                ->helperText('Status batch pengiriman'),
                            Forms\Components\Textarea::make('notes')
                                ->label('Catatan')
                                ->rows(3)
                                ->maxLength(65535)
                                ->placeholder('Catatan tambahan untuk batch pengiriman (opsional)'),
                        ])
                        ->columns(1),
                ])
                ->skippable()
                ->persistStepInQueryString()
                ->columnSpanFull()
                ->extraAttributes(['class' => 'mb-4'])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID Batch')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('scheduled_date')
                    ->label('Tanggal Pengiriman')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('area.name')
                    ->label('Area Pengiriman')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('driver_name')
                    ->label('Pengirim/Driver')
                    ->searchable()
                    ->placeholder('Belum ditentukan')
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Jumlah Pesanan')
                    ->counts('items')
                    ->badge()
                    ->color(fn ($state) => $state >= self::MAX_ITEMS_PER_BATCH ? 'danger' : 'success')
                    ->description(fn ($state) => $state >= self::MAX_ITEMS_PER_BATCH ? 'Penuh' :
                        ($state == self::MAX_ITEMS_PER_BATCH - 1 ? 'Sisa 1 slot' : 'Sisa ' . (self::MAX_ITEMS_PER_BATCH - $state) . ' slot')),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DIJADWALKAN' => 'warning',
                        'DALAM_PERJALANAN' => 'primary',
                        'SELESAI' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'DIJADWALKAN' => 'Dijadwalkan',
                        'DALAM_PERJALANAN' => 'Dalam Perjalanan',
                        'SELESAI' => 'Selesai',
                    ]),
                SelectFilter::make('delivery_area_id')
                    ->label('Filter Area Pengiriman')
                    ->relationship('area', 'name')
                    ->preload()
                    ->searchable(),
                Tables\Filters\Filter::make('scheduled_date')
                    ->label('Filter Tanggal Pengiriman')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_date', '>=', $date)
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_date', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Action untuk menambah pesanan dengan modal
                    Action::make('addOrders')
                        ->label('Tambah Pesanan')
                        ->icon('heroicon-o-plus')
                        ->color('warning')
                        ->visible(function (DeliveryBatch $record) {
                            return $record->status === 'DIJADWALKAN' &&
                                   $record->items()->count() < self::MAX_ITEMS_PER_BATCH;
                        })
                        ->form([
                            Forms\Components\Select::make('order_type')
                                ->label('Jenis Pesanan')
                                ->options([
                                    'regular' => 'Pesanan Reguler',
                                    'custom' => 'Produk Kustom',
                                ])
                                ->default('regular')
                                ->required()
                                ->live(),

                            Forms\Components\CheckboxList::make('selected_orders')
                                ->label('Pilih Pesanan')
                                ->options(function (Forms\Get $get, DeliveryBatch $record) {
                                    $orderType = $get('order_type') ?? 'regular';
                                    $cityIds = $record->area->cities()->pluck('cities.id')->toArray();
                                    $currentCount = $record->items()->count();
                                    $remainingSlots = self::MAX_ITEMS_PER_BATCH - $currentCount;

                                    if ($orderType === 'regular') {
                                        return Order::where('status', 'SIAP DIKIRIM')
                                            ->whereIn('city_id', $cityIds)
                                            ->whereDoesntHave('deliveryItems')
                                            ->limit($remainingSlots)
                                            ->get()
                                            ->mapWithKeys(function ($order) {
                                                return [
                                                    $order->id => "#{$order->payment_code} - {$order->full_name} - {$order->city->name} - Rp " . number_format($order->total)
                                                ];
                                            });
                                    } else {
                                        return CustomProductRequest::where('status', 'SIAP_DIKIRIM')
                                            ->whereHas('shipment', function ($query) use ($cityIds) {
                                                $query->whereIn('city_id', $cityIds);
                                            })
                                            ->whereDoesntHave('deliveryItems')
                                            ->limit($remainingSlots)
                                            ->get()
                                            ->mapWithKeys(function ($request) {
                                                return [
                                                    $request->id => "{$request->title} - {$request->user->name} - {$request->shipment->city->name} - Rp " . number_format($request->quoted_price)
                                                ];
                                            });
                                    }
                                })
                                ->columns(1)
                                ->searchable()
                                ->bulkToggleable()
                                ->required(),
                        ])
                        ->action(function (array $data, DeliveryBatch $record) {
                            $orderType = $data['order_type'];
                            $selectedOrders = $data['selected_orders'] ?? [];

                            if (empty($selectedOrders)) {
                                Notification::make()
                                    ->title('Pilih minimal satu pesanan')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $currentCount = $record->items()->count();
                            $remainingSlots = self::MAX_ITEMS_PER_BATCH - $currentCount;

                            if (count($selectedOrders) > $remainingSlots) {
                                Notification::make()
                                    ->title("Batch hanya dapat menampung {$remainingSlots} pesanan lagi")
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $count = 0;
                            foreach ($selectedOrders as $orderId) {
                                $exists = DeliveryItem::where('delivery_batch_id', $record->id)
                                    ->where('deliverable_type', $orderType === 'regular' ? Order::class : CustomProductRequest::class)
                                    ->where('deliverable_id', $orderId)
                                    ->exists();

                                if (!$exists) {
                                    DeliveryItem::create([
                                        'delivery_batch_id' => $record->id,
                                        'deliverable_type' => $orderType === 'regular' ? Order::class : CustomProductRequest::class,
                                        'deliverable_id' => $orderId,
                                        'status' => 'BELUM_DIKIRIM',
                                    ]);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title("{$count} pesanan berhasil ditambahkan ke batch")
                                ->success()
                                ->send();
                        })
                        ->modalWidth('7xl')
                        ->modalHeading('Tambah Pesanan ke Batch')
                        ->modalSubmitActionLabel('Tambahkan Pesanan'),

                    Tables\Actions\Action::make('printManifest')
                        ->label('Cetak Manifest')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->url(fn (DeliveryBatch $record) => route('delivery-manifest.generate', ['batchId' => $record->id]))
                        ->openUrlInNewTab()
                        ->visible(fn (DeliveryBatch $record) => in_array($record->status, ['DIJADWALKAN'])),

                    Tables\Actions\Action::make('startDelivery')
                        ->label('Mulai Pengiriman')
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Mulai Pengiriman')
                        ->modalDescription('Tindakan ini akan mengubah status batch menjadi "Dalam Perjalanan" dan semua pesanan dalam batch ini akan berubah status menjadi "DIKIRIM".')
                        ->modalSubmitActionLabel('Ya, Mulai Pengiriman')
                        ->visible(fn (DeliveryBatch $record) => $record->status === 'DIJADWALKAN')
                        ->action(function (DeliveryBatch $record) {
                            $itemCount = $record->items()->count();
                            if ($itemCount === 0) {
                                Notification::make()
                                    ->title('Batch pengiriman kosong')
                                    ->body('Batch pengiriman harus memiliki minimal 1 pesanan sebelum dapat dimulai pengirimannya.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $record->status = 'DALAM_PERJALANAN';
                            $record->save();

                            Notification::make()
                                ->title('Batch pengiriman sudah dalam perjalanan')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('completeDelivery')
                        ->label('Selesaikan Pengiriman')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Selesaikan Pengiriman')
                        ->modalDescription('Tindakan ini akan menandai batch pengiriman sebagai "Selesai".')
                        ->modalSubmitActionLabel('Ya, Selesaikan')
                        ->visible(fn (DeliveryBatch $record) => $record->status === 'DALAM_PERJALANAN')
                        ->action(function (DeliveryBatch $record) {
                            $record->status = 'SELESAI';
                            $record->save();

                            Notification::make()
                                ->title('Batch pengiriman telah selesai')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn (DeliveryBatch $record) => $record->items()->count() === 0),
                ])->tooltip('Aksi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (?Collection $records = null) => $records ? $records->every(fn ($record) => $record->items()->count() === 0) : true),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveryBatches::route('/'),
            'create' => Pages\CreateDeliveryBatch::route('/create'),
            'edit' => Pages\EditDeliveryBatch::route('/{record}/edit'),
        ];
    }
}
