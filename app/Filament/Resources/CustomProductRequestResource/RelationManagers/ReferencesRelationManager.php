<?php

namespace App\Filament\Resources\CustomProductRequestResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReferencesRelationManager extends RelationManager
{
    protected static string $relationship = 'references';

    protected static ?string $recordTitleAttribute = 'image_path';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image_path')
                    ->label('Gambar Referensi')
                    ->image()
                    ->required()
                    ->directory('custom-product-references')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                    ->maxSize(5120) // 5MB max
                    ->imageEditor()
                    ->imageCropAspectRatio(null)
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('1080')
                    ->uploadingMessage('Mengupload gambar...')
                    ->disk('public')
                    ->visibility('public'),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->placeholder('Tambahkan deskripsi untuk gambar referensi ini')
                    ->maxLength(500),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Gambar')
                    ->size(80)
                    ->circular(false)
                    ->checkFileExistence(false)
                    ->disk('public'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->wrap()
                    ->placeholder('Tidak ada deskripsi'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_description')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('description'))
                    ->label('Dengan Deskripsi'),

                Tables\Filters\Filter::make('recent')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->label('7 Hari Terakhir'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Tambah Gambar Referensi')
                    ->modalDescription('Upload gambar referensi untuk permintaan produk kustom ini')
                    ->modalWidth('lg')
                    ->successNotificationTitle('Gambar referensi berhasil ditambahkan'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($record) {
                        // Pastikan file ada
                        if (!$record->image_path || !Storage::disk('public')->exists($record->image_path)) {
                            \Filament\Notifications\Notification::make()
                                ->title('File tidak ditemukan')
                                ->body('Gambar mungkin telah dihapus atau dipindahkan.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Get file info
                        $filePath = storage_path('app/public/' . $record->image_path);
                        $fileName = 'referensi_' . $record->id . '_' . basename($record->image_path);

                        // Get mime type safely
                        $mimeType = 'application/octet-stream'; // default
                        try {
                            $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
                        } catch (\Exception $e) {
                            // fallback to default
                        }

                        // Show success notification
                        \Filament\Notifications\Notification::make()
                            ->title('File berhasil didownload')
                            ->success()
                            ->send();

                        // Return download response
                        return response()->download($filePath, $fileName, [
                            'Content-Type' => $mimeType,
                        ]);
                    }),

                Tables\Actions\ViewAction::make()
                    ->modalHeading('Lihat Gambar Referensi')
                    ->modalContent(function ($record) {
                        if (!$record->image_path || !Storage::disk('public')->exists($record->image_path)) {
                            return new \Illuminate\Support\HtmlString('
                                <div class="text-center py-8">
                                    <div class="text-gray-400 text-6xl mb-4">📷</div>
                                    <p class="text-gray-500">Gambar tidak ditemukan</p>
                                </div>
                            ');
                        }

                        $imagePath = Storage::url($record->image_path);
                        $description = $record->description ?: 'Tidak ada deskripsi';
                        $createdAt = $record->created_at->format('d M Y H:i');

                        return new \Illuminate\Support\HtmlString('
                            <div class="text-center">
                                <img src="' . $imagePath . '" alt="Preview" class="max-w-full h-auto max-h-96 mx-auto rounded-lg shadow-lg">
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <p class="text-gray-700 font-medium">Deskripsi:</p>
                                    <p class="mt-1 text-gray-600">' . e($description) . '</p>
                                    <p class="mt-3 text-sm text-gray-400">Ditambahkan: ' . $createdAt . '</p>
                                </div>
                            </div>
                        ');
                    })
                    ->modalWidth('2xl'),

                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Gambar Referensi')
                    ->modalWidth('lg')
                    ->successNotificationTitle('Gambar referensi berhasil diperbarui'),

                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Gambar Referensi')
                    ->modalDescription('Apakah Anda yakin ingin menghapus gambar referensi ini? Tindakan ini tidak dapat dibatalkan.')
                    ->successNotificationTitle('Gambar referensi berhasil dihapus')
                    ->before(function ($record) {
                        // Hapus file dari storage saat record dihapus
                        if ($record->image_path && Storage::disk('public')->exists($record->image_path)) {
                            Storage::disk('public')->delete($record->image_path);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk download action
                    Tables\Actions\BulkAction::make('bulk_download')
                        ->label('Download Semua')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Download Semua Gambar')
                        ->modalDescription('Akan mendownload semua gambar yang dipilih dalam format ZIP.')
                        ->action(function ($records) {
                            $zip = new \ZipArchive();
                            $zipFileName = 'gambar_referensi_' . now()->format('Y-m-d_H-i-s') . '.zip';
                            $zipPath = storage_path('app/temp/' . $zipFileName);

                            // Create temp directory if not exists
                            if (!file_exists(dirname($zipPath))) {
                                mkdir(dirname($zipPath), 0755, true);
                            }

                            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                                $addedFiles = 0;
                                foreach ($records as $record) {
                                    if ($record->image_path && Storage::disk('public')->exists($record->image_path)) {
                                        $filePath = storage_path('app/public/' . $record->image_path);
                                        $fileName = 'referensi_' . $record->id . '_' . basename($record->image_path);
                                        $zip->addFile($filePath, $fileName);
                                        $addedFiles++;
                                    }
                                }
                                $zip->close();

                                if ($addedFiles > 0) {
                                    // Show success notification
                                    \Filament\Notifications\Notification::make()
                                        ->title('ZIP file berhasil dibuat')
                                        ->body($addedFiles . ' gambar berhasil didownload')
                                        ->success()
                                        ->send();

                                    // Return download response
                                    return response()->download($zipPath)->deleteFileAfterSend(true);
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Tidak ada file yang bisa didownload')
                                        ->body('Semua gambar yang dipilih tidak ditemukan')
                                        ->warning()
                                        ->send();
                                }
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Gagal membuat ZIP file')
                                    ->body('Terjadi kesalahan saat membuat file ZIP')
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Gambar Referensi')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua gambar yang dipilih? Tindakan ini tidak dapat dibatalkan.')
                        ->successNotificationTitle('Gambar referensi berhasil dihapus')
                        ->before(function ($records) {
                            // Hapus semua file dari storage
                            foreach ($records as $record) {
                                if ($record->image_path && Storage::disk('public')->exists($record->image_path)) {
                                    Storage::disk('public')->delete($record->image_path);
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->poll('30s'); // Auto refresh every 30 seconds
    }
}
