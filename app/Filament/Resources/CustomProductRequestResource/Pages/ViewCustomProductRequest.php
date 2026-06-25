<?php

namespace App\Filament\Resources\CustomProductRequestResource\Pages;

use App\Filament\Resources\CustomProductRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Enums\FontWeight;

class ViewCustomProductRequest extends ViewRecord
{
    protected static string $resource = CustomProductRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('create_offer')
                ->label('Buat Penawaran')
                ->icon('heroicon-o-currency-dollar')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\TextInput::make('quoted_price')
                        ->label('Harga yang Ditawarkan')
                        ->numeric()
                        ->required()
                        ->prefix('Rp')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set, $state) {
                            $quotedPrice = (float) $state;

                            // Auto calculate DP (30%) - otomatis
                            if ($quotedPrice > 0) {
                                $autoDownPayment = $quotedPrice * 0.3;
                                $set('down_payment', $autoDownPayment);

                                // Calculate remaining payment
                                $remainingPayment = $quotedPrice - $autoDownPayment;
                                $set('remaining_payment', max(0, $remainingPayment));
                            } else {
                                $set('down_payment', 0);
                                $set('remaining_payment', 0);
                            }
                        }),
                    \Filament\Forms\Components\TextInput::make('down_payment')
                        ->label('DP yang Diperlukan (30% otomatis)')
                        ->numeric()
                        ->prefix('Rp')
                        ->disabled() // Tidak bisa diedit
                        ->dehydrated(true) // Tetap disimpan
                        ->helperText('Otomatis dihitung 30% dari harga total'),
                    \Filament\Forms\Components\TextInput::make('remaining_payment')
                        ->label('Sisa Pembayaran')
                        ->numeric()
                        ->prefix('Rp')
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Otomatis dihitung: Harga Total - DP'),
                    \Filament\Forms\Components\DatePicker::make('estimated_completion')
                        ->label('Estimasi Penyelesaian')
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('admin_notes')
                        ->label('Catatan Admin'),
                ])
                ->action(function (array $data, \App\Services\CustomProductService $customProductService) {
                    try {
                        $customProductService->createOffer($this->record, $data);
                        \Filament\Notifications\Notification::make()
                            ->title('Penawaran berhasil dibuat')
                            ->success()
                            ->send();
                        $this->refreshFormData(['status', 'quoted_price', 'down_payment', 'remaining_payment', 'estimated_completion', 'admin_notes']);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Error creating offer: '.$e->getMessage());
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal membuat penawaran')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn (): bool => $this->record->status === 'MENUNGGU_REVIEW'),

            Actions\Action::make('add_progress')
                ->label('Tambah Progress')
                ->icon('heroicon-o-photo')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('image')
                        ->label('Foto Progress')
                        ->image()
                        ->required()
                        ->directory('custom-product-progresses'),
                    \Filament\Forms\Components\Textarea::make('description')
                        ->label('Deskripsi Progress')
                        ->required(),
                ])
                ->action(function (array $data, \App\Services\CustomProductService $customProductService) {
                    try {
                        $customProductService->addProgressUpdate(
                            $this->record,
                            $data['image'],
                            $data['description']
                        );
                        \Filament\Notifications\Notification::make()
                            ->title('Progress berhasil ditambahkan')
                            ->success()
                            ->send();
                        $this->refreshFormData(['status']);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Error adding progress: '.$e->getMessage());
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal menambahkan progress')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn (): bool => $this->record->status === 'DALAM_PENGERJAAN'),

            // MODIFIED ACTION: Gabungan Selesai Dikerjakan & Upload Final Product
            Actions\Action::make('mark_completed')
                ->label('Selesai Dikerjakan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Section::make('Upload Foto Produk Final')
                        ->description('Upload foto produk yang telah selesai dikerjakan')
                        ->schema([
                            \Filament\Forms\Components\FileUpload::make('final_product_image')
                                ->label('Foto Produk Jadi')
                                ->image()
                                ->required()
                                ->directory('custom-product-finals')
                                ->helperText('Upload foto produk yang telah selesai dikerjakan'),
                            \Filament\Forms\Components\Textarea::make('notes')
                                ->label('Catatan Produk Final')
                                ->placeholder('Berikan detail tentang produk jadi, spesifikasi, atau petunjuk penggunaan')
                                ->rows(3),
                        ])
                        ->collapsible(false),

                    \Filament\Forms\Components\Section::make('Konfirmasi Penyelesaian')
                        ->description('Pastikan produk sudah selesai dan siap untuk pelunasan')
                        ->schema([
                            \Filament\Forms\Components\Placeholder::make('confirmation_text')
                                ->label('')
                                ->content('Dengan menandai sebagai selesai, status akan berubah menjadi "Menunggu Pelunasan" dan customer akan diberitahu bahwa produk sudah siap.')
                                ->columnSpanFull(),
                        ])
                        ->collapsible(false),
                ])
                ->action(function (array $data, \App\Services\CustomProductService $customProductService) {
                    try {
                        // Simpan foto produk final
                        $finalProduct = $this->record->finalProduct()->create([
                            'image_path' => $data['final_product_image'],
                            'notes' => $data['notes'] ?? null,
                        ]);

                        // Update status ke Menunggu Pelunasan
                        $customProductService->updateStatus($this->record, 'MENUNGGU_PELUNASAN');

                        \Filament\Notifications\Notification::make()
                            ->title('Produk berhasil diselesaikan')
                            ->body('Status berhasil diubah ke Menunggu Pelunasan dan foto produk final telah disimpan.')
                            ->success()
                            ->send();

                        $this->refreshFormData(['status', 'work_completed_at']);

                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Error marking request as completed: '.$e->getMessage());
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal menyelesaikan produk')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn (): bool => $this->record->status === 'DALAM_PENGERJAAN'),

            // ACTION: Upload Foto Final Product (untuk yang belum ada foto)
            Actions\Action::make('upload_final_product')
                ->label('Upload Foto Produk Final')
                ->icon('heroicon-o-camera')
                ->color('primary')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('final_product_image')
                        ->label('Foto Produk Jadi')
                        ->image()
                        ->required()
                        ->directory('custom-product-finals'),
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Catatan Produk Final')
                        ->placeholder('Berikan detail tentang produk jadi, spesifikasi, atau petunjuk penggunaan'),
                ])
                ->action(function (array $data) {
                    try {
                        // Simpan ke database menggunakan model - sama seperti di EditCustomProductRequest
                        $finalProduct = $this->record->finalProduct()->create([
                            'image_path' => $data['final_product_image'],
                            'notes' => $data['notes'] ?? null,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Foto produk final berhasil diupload')
                            ->body('Foto dan catatan produk final telah disimpan.')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Error uploading final product: ' . $e->getMessage());
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal upload foto produk')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn (): bool =>
                    $this->record->status === 'MENUNGGU_PELUNASAN' && !$this->record->finalProduct
                ),

            Actions\Action::make('cancel_request')
                ->label('Batalkan')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Alasan Pembatalan')
                        ->required(),
                ])
                ->action(function (array $data, \App\Services\CustomProductService $customProductService) {
                    try {
                        $customProductService->cancelRequest($this->record, $data['reason']);
                        \Filament\Notifications\Notification::make()
                            ->title('Permintaan berhasil dibatalkan')
                            ->success()
                            ->send();
                        $this->refreshFormData(['status', 'admin_notes']);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Error cancelling request: '.$e->getMessage());
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal membatalkan permintaan')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn (): bool => !in_array($this->record->status, ['SELESAI', 'DIBATALKAN'])),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Detail Permintaan')
                    ->columnSpanFull()
                    ->contained(false)
                    ->tabs([
                        Tabs\Tab::make('Informasi Umum')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Detail Permintaan')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('id')
                                                    ->label('ID Permintaan')
                                                    ->weight(FontWeight::Bold),

                                                TextEntry::make('status')
                                                    ->label('Status')
                                                    ->badge()
                                                    ->color(fn (string $state): string => match ($state) {
                                                        'MENUNGGU_REVIEW' => 'warning',
                                                        'PENAWARAN_DIBERIKAN' => 'primary',
                                                        'PENAWARAN_DITOLAK' => 'danger',
                                                        'MENUNGGU_DP' => 'success',
                                                        'DALAM_PENGERJAAN' => 'info',
                                                        'MENUNGGU_PELUNASAN' => 'warning',
                                                        'SIAP_DIKIRIM' => 'primary',
                                                        'DIKIRIM' => 'purple',
                                                        'SELESAI' => 'success',
                                                        'DIBATALKAN' => 'danger',
                                                        default => 'gray',
                                                    })
                                                    ->formatStateUsing(function ($state) {
                                                        $labels = [
                                                            'MENUNGGU_REVIEW' => 'Menunggu Review',
                                                            'PENAWARAN_DIBERIKAN' => 'Penawaran Diberikan',
                                                            'PENAWARAN_DITOLAK' => 'Penawaran Ditolak',
                                                            'MENUNGGU_DP' => 'Menunggu DP',
                                                            'DALAM_PENGERJAAN' => 'Dalam Pengerjaan',
                                                            'MENUNGGU_PELUNASAN' => 'Menunggu Pelunasan',
                                                            'SIAP_DIKIRIM' => 'Siap Dikirim',
                                                            'DIKIRIM' => 'Dikirim',
                                                            'SELESAI' => 'Selesai',
                                                            'DIBATALKAN' => 'Dibatalkan',
                                                        ];
                                                        return $labels[$state] ?? $state;
                                                    }),
                                                TextEntry::make('title')
                                                    ->label('Judul Permintaan')
                                                    ->weight(FontWeight::Bold),
                                                TextEntry::make('user.name')
                                                    ->label('Pelanggan')
                                                    ->icon('heroicon-o-user'),
                                                TextEntry::make('user.email')
                                                    ->label('Email Pelanggan')
                                                    ->icon('heroicon-o-envelope'),
                                                TextEntry::make('created_at')
                                                    ->label('Tanggal Permintaan')
                                                    ->dateTime('d F Y, H:i')
                                                    ->icon('heroicon-o-calendar')
                                            ]),

                                        TextEntry::make('description')
                                            ->label('Deskripsi')
                                            ->markdown()
                                            ->columnSpanFull(),

                                        TextEntry::make('specifications')
                                            ->label('Spesifikasi')
                                            ->markdown()
                                            ->columnSpanFull()
                                            ->visible(fn ($state): bool => !empty($state)),
                                    ]),

                                Section::make('Informasi Budget & Timeline')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('budget')
                                                    ->label('Budget Pelanggan')
                                                    ->money('IDR')
                                                    ->icon('heroicon-o-banknotes')
                                                    ->visible(fn ($state): bool => !empty($state)),
                                                TextEntry::make('desired_deadline')
                                                    ->label('Deadline Harapan')
                                                    ->date('d F Y')
                                                    ->icon('heroicon-o-clock')
                                                    ->visible(fn ($state): bool => !empty($state)),
                                            ]),
                                    ])
                                    ->visible(fn ($record): bool => !empty($record->budget) || !empty($record->desired_deadline)),
                            ]),

                        Tabs\Tab::make('Penawaran & Pembayaran')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Section::make('Detail Penawaran')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('quoted_price')
                                                    ->label('Harga Penawaran')
                                                    ->money('IDR')
                                                    ->weight(FontWeight::Bold)
                                                    ->color('success')
                                                    ->icon('heroicon-o-currency-dollar'),
                                                TextEntry::make('down_payment')
                                                    ->label('DP yang Diperlukan')
                                                    ->money('IDR')
                                                    ->icon('heroicon-o-credit-card'),
                                                TextEntry::make('remaining_payment')
                                                    ->label('Sisa Pembayaran')
                                                    ->money('IDR')
                                                    ->state(fn ($record): int => ($record->quoted_price ?? 0) - ($record->down_payment ?? 0))
                                                    ->icon('heroicon-o-banknotes')
                                            ]),

                                        TextEntry::make('estimated_completion')
                                            ->label('Estimasi Penyelesaian')
                                            ->date('d F Y')
                                            ->icon('heroicon-o-calendar-days')
                                            ->visible(fn ($state): bool => !empty($state)),

                                        TextEntry::make('admin_notes')
                                            ->label('Catatan Admin')
                                            ->markdown()
                                            ->columnSpanFull()
                                            ->visible(fn ($state): bool => !empty($state)),
                                    ])
                                    ->visible(fn ($record): bool => !empty($record->quoted_price)),

                                Section::make('Informasi Pembayaran')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('dp_payment_code')
                                                    ->label('Kode Pembayaran DP')
                                                    ->icon('heroicon-o-qr-code')
                                                    ->copyable()
                                                    ->visible(fn ($state): bool => !empty($state)),
                                                TextEntry::make('dp_payment_date')
                                                    ->label('Tanggal Pembayaran DP')
                                                    ->dateTime('d F Y, H:i')
                                                    ->icon('heroicon-o-check-circle')
                                                    ->visible(fn ($state): bool => !empty($state)),
                                                TextEntry::make('full_payment_code')
                                                    ->label('Kode Pembayaran Pelunasan')
                                                    ->icon('heroicon-o-qr-code')
                                                    ->copyable()
                                                    ->visible(fn ($state): bool => !empty($state)),
                                                TextEntry::make('full_payment_date')
                                                    ->label('Tanggal Pelunasan')
                                                    ->dateTime('d F Y, H:i')
                                                    ->icon('heroicon-o-check-circle')
                                                    ->visible(fn ($state): bool => !empty($state)),
                                            ]),
                                    ])
                                    ->visible(fn ($record): bool => !empty($record->dp_payment_code) || !empty($record->full_payment_code)),
                            ])
                            ->visible(fn ($record): bool => !empty($record->quoted_price)),

                        Tabs\Tab::make('Timeline Pengerjaan')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Section::make('Status Pengerjaan')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('work_started_at')
                                                    ->label('Pengerjaan Dimulai')
                                                    ->date('d F Y')
                                                    ->icon('heroicon-o-play')
                                                    ->visible(fn ($state): bool => !empty($state)),
                                                TextEntry::make('work_completed_at')
                                                    ->label('Pengerjaan Selesai')
                                                    ->date('d F Y')
                                                    ->icon('heroicon-o-check-circle')
                                                    ->visible(fn ($state): bool => !empty($state)),
                                                TextEntry::make('shipping_date')
                                                    ->label('Tanggal Pengiriman')
                                                    ->date('d F Y')
                                                    ->icon('heroicon-o-truck')
                                                    ->visible(fn ($state): bool => !empty($state)),
                                                TextEntry::make('delivery_date')
                                                    ->label('Tanggal Penerimaan')
                                                    ->date('d F Y')
                                                    ->icon('heroicon-o-home')
                                                    ->visible(fn ($state): bool => !empty($state)),
                                            ]),
                                    ]),
                            ])
                            ->visible(fn ($record): bool => !empty($record->work_started_at) || !empty($record->work_completed_at) || !empty($record->shipping_date) || !empty($record->delivery_date)),

                        Tabs\Tab::make('Foto Referensi')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Foto Referensi dari Pelanggan')
                                    ->schema([
                                        RepeatableEntry::make('references')
                                            ->schema([
                                                ImageEntry::make('image_path')
                                                    ->label('Foto')
                                                    ->height(250)
                                                    ->width(250),
                                                TextEntry::make('description')
                                                    ->label('Deskripsi')
                                                    ->markdown()
                                            ])
                                            ->columns(2)
                                            ->grid(2)
                                            ->visible(fn ($record): bool => $record->references && $record->references->count() > 0),

                                        TextEntry::make('no_references')
                                            ->label('')
                                            ->state('Belum ada foto referensi yang diupload')
                                            ->color('gray')
                                            ->visible(fn ($record): bool => !$record->references || $record->references->count() === 0),
                                    ]),
                            ]),

                        Tabs\Tab::make('Progress Pengerjaan')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make('Update Progress')
                                    ->schema([
                                        RepeatableEntry::make('progresses')
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        ImageEntry::make('image_path')
                                                            ->label('Foto Progress')
                                                            ->height(200)
                                                            ->width(200),
                                                        TextEntry::make('description')
                                                            ->label('Deskripsi')
                                                            ->markdown()
                                                            ->columnSpan(2)
                                                    ]),
                                                TextEntry::make('created_at')
                                                    ->label('Tanggal Update')
                                                    ->dateTime('d F Y, H:i')
                                                    ->color('gray')
                                            ])
                                            ->visible(fn ($record): bool => $record->progresses && $record->progresses->count() > 0),

                                        TextEntry::make('no_progress')
                                            ->label('')
                                            ->state('Belum ada update progress pengerjaan')
                                            ->color('gray')
                                            ->visible(fn ($record): bool => !$record->progresses || $record->progresses->count() === 0),
                                    ]),
                            ]),

                        Tabs\Tab::make('Pengiriman')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Section::make('Detail Pengiriman')
                                    ->schema([
                                        RepeatableEntry::make('shipments')
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        TextEntry::make('tracking_number')
                                                            ->label('Nomor Resi')
                                                            ->copyable()
                                                            ->icon('heroicon-o-clipboard-document'),
                                                        TextEntry::make('courier')
                                                            ->label('Kurir')
                                                            ->icon('heroicon-o-truck'),
                                                        TextEntry::make('shipping_address')
                                                            ->label('Alamat Pengiriman')
                                                            ->columnSpanFull()
                                                            ->icon('heroicon-o-map-pin'),
                                                        TextEntry::make('shipped_at')
                                                            ->label('Tanggal Pengiriman')
                                                            ->dateTime('d F Y, H:i')
                                                            ->icon('heroicon-o-calendar'),
                                                        TextEntry::make('delivered_at')
                                                            ->label('Tanggal Penerimaan')
                                                            ->dateTime('d F Y, H:i')
                                                            ->icon('heroicon-o-check-circle')
                                                            ->visible(fn ($state): bool => !empty($state)),
                                                    ]),
                                                TextEntry::make('notes')
                                                    ->label('Catatan Pengiriman')
                                                    ->markdown()
                                                    ->columnSpanFull()
                                                    ->visible(fn ($state): bool => !empty($state)),
                                            ])
                                            ->visible(fn ($record): bool => $record->shipments && $record->shipments->count() > 0),

                                        TextEntry::make('no_shipment')
                                            ->label('')
                                            ->state('Belum ada data pengiriman')
                                            ->color('gray')
                                            ->visible(fn ($record): bool => !$record->shipments || $record->shipments->count() === 0),
                                    ]),
                            ]),
                    ])
                    ->activeTab(1)
                    ->persistTabInQueryString(),
            ]);
    }
}
