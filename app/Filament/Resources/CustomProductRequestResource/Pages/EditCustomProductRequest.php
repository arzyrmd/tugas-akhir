<?php

namespace App\Filament\Resources\CustomProductRequestResource\Pages;

use App\Filament\Resources\CustomProductRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Services\CustomProductService;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Get;
use Filament\Forms\Set;

class EditCustomProductRequest extends EditRecord
{
    protected static string $resource = CustomProductRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
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
                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
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
                ->action(function (array $data, CustomProductService $customProductService) {
                    try {
                        $customProductService->createOffer($this->record, $data);
                        Notification::make()
                            ->title('Penawaran berhasil dibuat')
                            ->success()
                            ->send();
                        $this->refreshFormData(['status', 'quoted_price', 'down_payment', 'remaining_payment', 'estimated_completion', 'admin_notes']);
                    } catch (\Exception $e) {
                        Log::error('Error creating offer: '.$e->getMessage());
                        Notification::make()
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
                ->action(function (array $data, CustomProductService $customProductService) {
                    try {
                        $customProductService->addProgressUpdate(
                            $this->record,
                            $data['image'],
                            $data['description']
                        );
                        Notification::make()
                            ->title('Progress berhasil ditambahkan')
                            ->success()
                            ->send();
                        $this->refreshFormData(['status']);
                    } catch (\Exception $e) {
                        Log::error('Error adding progress: '.$e->getMessage());
                        Notification::make()
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
                ->action(function (array $data, CustomProductService $customProductService) {
                    try {
                        // Simpan foto produk final
                        $finalProduct = $this->record->finalProduct()->create([
                            'image_path' => $data['final_product_image'],
                            'notes' => $data['notes'] ?? null,
                        ]);

                        // Update status ke Menunggu Pelunasan
                        $customProductService->updateStatus($this->record, 'MENUNGGU_PELUNASAN');

                        Notification::make()
                            ->title('Produk berhasil diselesaikan')
                            ->body('Status berhasil diubah ke Menunggu Pelunasan dan foto produk final telah disimpan.')
                            ->success()
                            ->send();

                        $this->refreshFormData(['status', 'work_completed_at']);

                    } catch (\Exception $e) {
                        Log::error('Error marking request as completed: '.$e->getMessage());
                        Notification::make()
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
                        // Simpan ke database menggunakan model
                        $finalProduct = $this->record->finalProduct()->create([
                            'image_path' => $data['final_product_image'],
                            'notes' => $data['notes'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Foto produk final berhasil diupload')
                            ->body('Foto dan catatan produk final telah disimpan.')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Log::error('Error uploading final product: ' . $e->getMessage());
                        Notification::make()
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
                ->action(function (array $data, CustomProductService $customProductService) {
                    try {
                        $customProductService->cancelRequest($this->record, $data['reason']);
                        Notification::make()
                            ->title('Permintaan berhasil dibatalkan')
                            ->success()
                            ->send();
                        $this->refreshFormData(['status', 'admin_notes']);
                    } catch (\Exception $e) {
                        Log::error('Error cancelling request: '.$e->getMessage());
                        Notification::make()
                            ->title('Gagal membatalkan permintaan')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn (): bool => !in_array($this->record->status, ['SELESAI', 'DIBATALKAN'])),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['quoted_price']) && isset($data['down_payment'])) {
            $data['remaining_payment'] = $data['quoted_price'] - $data['down_payment'];
        }
        return $data;
    }

    protected function afterSave(): void
    {
        // Refresh halaman setelah save agar RelationManager yang bergantung pada status terupdate
        $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
    }
}
