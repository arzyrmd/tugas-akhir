<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use ZipArchive;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $modelLabel = 'Pesanan';
    protected static ?string $navigationLabel = 'Pesanan';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'payment_code';

    public static function getNavigationBadge(): ?string
    {
        $model = static::getModel();
        $menungguPembayaran = $model::where('status', 'MENUNGGU PEMBAYARAN')->count();
        $pembayaranBerhasil = $model::where('status', 'PEMBAYARAN BERHASIL')->count();
        $siapDikirim = $model::where('status', 'SIAP DIKIRIM')->count();

        $total = $menungguPembayaran + $pembayaranBerhasil + $siapDikirim;

        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $model = static::getModel();

        if ($model::where('status', 'MENUNGGU PEMBAYARAN')->count() > 0) {
            return 'warning';
        }

        if ($model::where('status', 'PEMBAYARAN BERHASIL')->count() > 0) {
            return 'success';
        }

        if ($model::where('status', 'SIAP DIKIRIM')->count() > 0) {
            return 'info';
        }

        return 'primary';
    }

    /**
     * Mendapatkan status yang valid untuk transisi
     */
    protected static function getValidStatusTransitions(string $currentStatus): array
    {
        return match($currentStatus) {
            'MENUNGGU PEMBAYARAN' => [
                'MENUNGGU PEMBAYARAN' => 'MENUNGGU PEMBAYARAN',
                'PEMBAYARAN BERHASIL' => 'PEMBAYARAN BERHASIL',
                'DIBATALKAN' => 'DIBATALKAN',
            ],
            'PEMBAYARAN BERHASIL' => [
                'PEMBAYARAN BERHASIL' => 'PEMBAYARAN BERHASIL',
                'DIKEMAS' => 'DIKEMAS',
                'DIBATALKAN' => 'DIBATALKAN',
            ],
            'DIKEMAS' => [
                'DIKEMAS' => 'DIKEMAS',
                'SIAP DIKIRIM' => 'SIAP DIKIRIM',
            ],
            'SIAP DIKIRIM' => [
                'SIAP DIKIRIM' => 'SIAP DIKIRIM',
                'DIKIRIM' => 'DIKIRIM',
            ],
            'DIKIRIM' => [
                'DIKIRIM' => 'DIKIRIM',
                'SELESAI' => 'SELESAI',
            ],
            default => [
                $currentStatus => $currentStatus,
            ]
        };
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Informasi Pesanan')
                        ->description('Kelola status dan informasi utama pesanan')
                        ->icon('heroicon-o-shopping-bag')
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->label('Status Pesanan')
                                ->options(function (?Order $record) {
                                    if (!$record) {
                                        return [
                                            'MENUNGGU PEMBAYARAN' => 'MENUNGGU PEMBAYARAN',
                                        ];
                                    }

                                    return static::getValidStatusTransitions($record->status);
                                })
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    // Auto-set tanggal berdasarkan status
                                    if ($state === 'DIKEMAS' && !$get('packing_date')) {
                                        $set('packing_date', now());
                                    }
                                    if ($state === 'DIKIRIM' && !$get('delivery_date')) {
                                        $set('delivery_date', now());
                                    }
                                    if ($state === 'SELESAI' && !$get('completed_date')) {
                                        $set('completed_date', now());
                                    }
                                }),
                            Forms\Components\TextInput::make('payment_code')
                                ->label('Kode Pembayaran')
                                ->disabled()
                                ->dehydrated(false),
                            Forms\Components\TextInput::make('payment_method')
                                ->label('Metode Pembayaran')
                                ->disabled()
                                ->dehydrated(false),
                            Forms\Components\DateTimePicker::make('order_created_at')
                                ->label('Tanggal Pemesanan')
                                ->disabled()
                                ->dehydrated(false),
                            Forms\Components\DateTimePicker::make('payment_date')
                                ->label('Tanggal Pembayaran')
                                ->disabled()
                                ->dehydrated(false),
                            Forms\Components\DateTimePicker::make('packing_date')
                                ->label('Tanggal Pengemasan')
                                ->visible(fn ($get) => in_array($get('status'), ['DIKEMAS', 'SIAP DIKIRIM', 'DIKIRIM', 'SELESAI'])),
                            Forms\Components\DateTimePicker::make('delivery_date')
                                ->label('Tanggal Pengiriman')
                                ->visible(fn ($get) => in_array($get('status'), ['DIKIRIM', 'SELESAI'])),
                            Forms\Components\DateTimePicker::make('completed_date')
                                ->label('Tanggal Selesai')
                                ->visible(fn ($get) => $get('status') === 'SELESAI'),
                            Forms\Components\Textarea::make('notes')
                                ->label('Catatan')
                                ->columnSpanFull(),
                        ])->columns(2),

                    Step::make('Informasi Pelanggan')
                        ->description('Data lengkap pelanggan pemesan')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\TextInput::make('full_name')
                                ->label('Nama Lengkap')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('phone')
                                ->label('No. Telepon')
                                ->tel()
                                ->required()
                                ->maxLength(20),
                        ])->columns(3),

                    Step::make('Informasi Alamat')
                        ->description('Alamat pengiriman lengkap')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Forms\Components\TextInput::make('address')
                                ->label('Alamat Lengkap')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),
                            Forms\Components\Select::make('province_id')
                                ->label('Provinsi')
                                ->relationship('province', 'name')
                                ->required()
                                ->searchable(),
                            Forms\Components\Select::make('city_id')
                                ->label('Kota')
                                ->relationship('city', 'name')
                                ->required()
                                ->searchable(),
                            Forms\Components\TextInput::make('postal_code')
                                ->label('Kode Pos')
                                ->required()
                                ->maxLength(10),
                        ])->columns(3),

                    Step::make('Informasi Harga')
                        ->description('Detail perhitungan biaya pesanan')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            Forms\Components\TextInput::make('subtotal')
                                ->label('Subtotal')
                                ->formatStateUsing(fn ($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : '')
                                ->disabled()
                                ->dehydrated(false),
                            Forms\Components\TextInput::make('shipping_cost')
                                ->label('Biaya Pengiriman')
                                ->formatStateUsing(fn ($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : '')
                                ->disabled()
                                ->dehydrated(false),
                            Forms\Components\TextInput::make('total')
                                ->label('Total')
                                ->formatStateUsing(fn ($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : '')
                                ->disabled()
                                ->dehydrated(false),
                        ])->columns(3),
                ])
                ->columnSpanFull()
                ->skippable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_code')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode pembayaran berhasil disalin')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'MENUNGGU PEMBAYARAN' => 'warning',
                        'PEMBAYARAN BERHASIL' => 'success',
                        'DIKEMAS' => 'info',
                        'SIAP DIKIRIM' => 'primary',
                        'DIKIRIM' => 'purple',
                        'SELESAI' => 'success',
                         'PENDING' => 'gray',
                        'DIBATALKAN' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_created_at')
                    ->label('Tanggal Pesanan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('packing_date')
                    ->label('Tanggal Dikemas')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Tanggal Pengiriman')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('completed_date')
                    ->label('Tanggal Selesai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'MENUNGGU PEMBAYARAN' => 'MENUNGGU PEMBAYARAN',
                        'PEMBAYARAN BERHASIL' => 'PEMBAYARAN BERHASIL',
                        'DIKEMAS' => 'DIKEMAS',
                        'SIAP DIKIRIM' => 'SIAP DIKIRIM',
                        'DIKIRIM' => 'DIKIRIM',
                        'SELESAI' => 'SELESAI',
                        'DIBATALKAN' => 'DIBATALKAN',

                    ])
                    ->multiple(),

                SelectFilter::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options(function () {
                        return Order::distinct()
                            ->whereNotNull('payment_method')
                            ->pluck('payment_method', 'payment_method')
                            ->toArray();
                    })
                    ->multiple(),

                Filter::make('total_range')
                    ->label('Rentang Total')
                    ->form([
                        Forms\Components\TextInput::make('total_min')
                            ->label('Total Minimal')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('total_max')
                            ->label('Total Maksimal')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['total_min'],
                                fn (Builder $query, $amount): Builder => $query->where('total', '>=', $amount),
                            )
                            ->when(
                                $data['total_max'],
                                fn (Builder $query, $amount): Builder => $query->where('total', '<=', $amount),
                            );
                    }),

                Filter::make('order_date')
                    ->label('Tanggal Pesanan')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_created_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_created_at', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('order_created_at', 'desc')
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('cetak_packing_slip')
                        ->label('Cetak Packing Slip')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->visible(fn (Order $record) => in_array($record->status, ['PEMBAYARAN BERHASIL', 'DIKEMAS']))
                        ->action(function (Order $record) {
                            return response()->streamDownload(function () use ($record) {
                                $pdf = app()->make('dompdf.wrapper');
                                $pdf->loadView('pdf.packing-slip', ['order' => $record->load('orderItems.product', 'province', 'city')]);
                                echo $pdf->stream();
                            }, 'packing-slip-' . $record->payment_code . '.pdf');
                        })
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('kemas_pesanan')
                        ->label('Kemas')
                        ->icon('heroicon-o-archive-box')
                        ->color('info')
                        ->visible(fn (Order $record) => $record->status === 'PEMBAYARAN BERHASIL')
                        ->requiresConfirmation()
                        ->modalDescription('Apakah Anda yakin ingin menandai pesanan ini sebagai dikemas?')
                        ->action(function (Order $record) {
                            $record->update([
                                'status' => 'DIKEMAS',
                                'packing_date' => now()
                            ]);

                            Notification::make()
                                ->title('Pesanan berhasil dikemas')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('siap_kirim')
                        ->label('Siap Kirim')
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->visible(fn (Order $record) => $record->status === 'DIKEMAS')
                        ->requiresConfirmation()
                        ->modalDescription('Apakah Anda yakin pesanan ini siap untuk dikirim?')
                        ->action(function (Order $record) {
                            $record->update([
                                'status' => 'SIAP DIKIRIM',
                            ]);

                            Notification::make()
                                ->title('Pesanan siap untuk dikirim')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('kirim_pesanan')
                        ->label('Kirim')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('purple')
                        ->visible(fn (Order $record) => $record->status === 'SIAP DIKIRIM')
                        ->requiresConfirmation()
                        ->modalDescription('Apakah Anda yakin ingin menandai pesanan ini sebagai dikirim?')
                        ->action(function (Order $record) {
                            $record->update([
                                'status' => 'DIKIRIM',
                                'delivery_date' => now()
                            ]);

                            Notification::make()
                                ->title('Pesanan berhasil dikirim')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('selesaikan_pesanan')
                        ->label('Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Order $record) => $record->status === 'DIKIRIM')
                        ->requiresConfirmation()
                        ->modalDescription('Apakah Anda yakin ingin menandai pesanan ini sebagai selesai?')
                        ->action(function (Order $record) {
                            $record->update([
                                'status' => 'SELESAI',
                                'completed_date' => now()
                            ]);

                            Notification::make()
                                ->title('Pesanan berhasil diselesaikan')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\ViewAction::make()
                        ->label('Lihat'),
                    Tables\Actions\EditAction::make()
                        ->label('Edit'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('cetak_multi_packing_slip')
                        ->label('Cetak Packing Slip (Batch)')
                        ->icon('heroicon-o-printer')
                        ->action(function (Collection $records) {
                            // Filter hanya pesanan yang sesuai status
                            $validRecords = $records->filter(fn($record) =>
                                in_array($record->status, ['PEMBAYARAN BERHASIL', 'DIKEMAS'])
                            );

                            if ($validRecords->isEmpty()) {
                                Notification::make()
                                    ->title('Tidak ada pesanan yang valid untuk dicetak')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            // Untuk batch, dapat menggunakan ZIP jika banyak
                            if ($validRecords->count() > 1) {
                                $zipFileName = 'packing-slips-batch.zip';
                                $zip = new ZipArchive();

                                $tempFile = tempnam(sys_get_temp_dir(), 'zip');
                                if ($zip->open($tempFile, ZipArchive::CREATE) === TRUE) {
                                    foreach ($validRecords as $record) {
                                        $pdf = app()->make('dompdf.wrapper');
                                        $pdf->loadView('pdf.packing-slip', ['order' => $record->load('orderItems.product', 'province', 'city')]);
                                        $pdfContent = $pdf->output();

                                        $zip->addFromString('packing-slip-' . $record->payment_code . '.pdf', $pdfContent);
                                    }
                                    $zip->close();

                                    return response()->download($tempFile, $zipFileName)->deleteFileAfterSend(true);
                                }
                            } else {
                                // Jika hanya 1 record
                                $record = $validRecords->first();
                                return response()->streamDownload(function () use ($record) {
                                    $pdf = app()->make('dompdf.wrapper');
                                    $pdf->loadView('pdf.packing-slip', ['order' => $record->load('orderItems.product', 'province', 'city')]);
                                    echo $pdf->stream();
                                }, 'packing-slip-' . $record->payment_code . '.pdf');
                            }
                        }),

                    Tables\Actions\BulkAction::make('tandai_dikemas')
                        ->label('Tandai Sebagai Dikemas')
                        ->icon('heroicon-o-archive-box')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalDescription('Apakah Anda yakin ingin menandai pesanan yang dipilih sebagai dikemas?')
                        ->action(function (Collection $records) {
                            $updated = 0;
                            $records->each(function ($record) use (&$updated) {
                                if ($record->status === 'PEMBAYARAN BERHASIL') {
                                    $record->update([
                                        'status' => 'DIKEMAS',
                                        'packing_date' => now()
                                    ]);
                                    $updated++;
                                }
                            });

                            Notification::make()
                                ->title("$updated pesanan berhasil dikemas")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('tandai_siap_kirim')
                        ->label('Tandai Siap Dikirim')
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalDescription('Apakah Anda yakin ingin menandai pesanan yang dipilih sebagai siap dikirim?')
                        ->action(function (Collection $records) {
                            $updated = 0;
                            $records->each(function ($record) use (&$updated) {
                                if ($record->status === 'DIKEMAS') {
                                    $record->update([
                                        'status' => 'SIAP DIKIRIM',
                                    ]);
                                    $updated++;
                                }
                            });

                            Notification::make()
                                ->title("$updated pesanan siap untuk dikirim")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('tandai_dikirim')
                        ->label('Tandai Sebagai Dikirim')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('purple')
                        ->requiresConfirmation()
                        ->modalDescription('Apakah Anda yakin ingin menandai pesanan yang dipilih sebagai dikirim?')
                        ->action(function (Collection $records) {
                            $updated = 0;
                            $records->each(function ($record) use (&$updated) {
                                if ($record->status === 'SIAP DIKIRIM') {
                                    $record->update([
                                        'status' => 'DIKIRIM',
                                        'delivery_date' => now()
                                    ]);
                                    $updated++;
                                }
                            });

                            Notification::make()
                                ->title("$updated pesanan berhasil dikirim")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['city', 'province']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['payment_code', 'full_name', 'email', 'phone'];
    }
}
