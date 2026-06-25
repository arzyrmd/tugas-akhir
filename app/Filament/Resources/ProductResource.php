<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\StockMovement; // Import StockMovement
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Notifications\Notification;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';
    protected static ?string $navigationGroup = 'Manajemen Produk';
    protected static ?string $navigationLabel = 'Produk';

    // Sinkronisasi dengan observer - batas minimum stok
    public static int $batasMinimumStok = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Step::make('Informasi Dasar')
                        ->description('Informasi dasar produk seperti nama, kategori, dan slug')
                        ->schema([
                            Forms\Components\Select::make('category_id')
                                ->relationship('category', 'name')
                                ->required()
                                ->label('Kategori Produk')
                                ->searchable()
                                ->preload(),
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Produk')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $state, Forms\Set $set) =>
                                    $set('slug', Str::slug($state))),
                            Forms\Components\TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->maxLength(255)
                                ->readOnly()
                                ->dehydrated()
                                ->unique(ignoreRecord: true),
                            RichEditor::make('description')
                                ->label('Deskripsi')
                                ->required()
                                ->columnSpanFull()
                                ->toolbarButtons([
                                    'alignCenter',
                                    'alignLeft',
                                    'alignRight',
                                    'alignJustify',
                                    'bold',
                                    'italic',
                                    'underline',
                                    'strike',
                                    'subscript',
                                    'superscript',
                                    'h2',
                                    'h3',
                                    'bulletList',
                                    'orderedList',
                                    'link',
                                    'media',
                                    'table',
                                    'blockquote',
                                    'codeBlock',
                                    'hr',
                                    'undo',
                                    'redo',
                                ])
                                ->fileAttachmentsDisk('public')
                                ->fileAttachmentsDirectory('uploads/rich-editor')
                                ->fileAttachmentsVisibility('public')
                                ->placeholder('Masukkan deskripsi dengan format lengkap...')
                                ->helperText('Editor lengkap dengan alignment, upload gambar, tabel, dan format lainnya.')
                                ->extraAttributes([
                                    'style' => 'min-height: 250px;'
                                ]),
                        ])
                        ->columns(2),

                    Step::make('Harga & Stok')
                        ->description('Atur harga produk dan informasi stok')
                        ->schema([
                            Forms\Components\TextInput::make('price')
                                ->label('Harga')
                                ->required()
                                ->numeric()
                                ->prefix('Rp')
                                ->minValue(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $old, $set) {
                                    // Validasi perubahan harga signifikan
                                    if ($old && $state) {
                                        $percentageChange = abs(($state - $old) / $old * 100);
                                        if ($percentageChange >= 5) {
                                            Notification::make()
                                                ->title('Perubahan Harga Signifikan')
                                                ->body("Harga berubah " . number_format($percentageChange, 1) . "% dari sebelumnya")
                                                ->warning()
                                                ->send();
                                        }
                                    }
                                }),
                            Forms\Components\TextInput::make('stock')
                                ->label('Stok')
                                ->required()
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $old, $set) {
                                    // Peringatan stok rendah real-time
                                    if ($state <= self::$batasMinimumStok) {
                                        Notification::make()
                                            ->title('Peringatan Stok Rendah')
                                            ->body("Stok yang dimasukkan ({$state}) berada di bawah batas minimum (" . self::$batasMinimumStok . " unit)")
                                            ->warning()
                                            ->send();
                                    }

                                    // Peringatan stok habis
                                    if ($state == 0) {
                                        Notification::make()
                                            ->title('Stok Habis')
                                            ->body('Produk akan menjadi tidak tersedia karena stok habis')
                                            ->danger()
                                            ->send();
                                    }
                                })
                                ->helperText('Stok minimum yang disarankan: ' . self::$batasMinimumStok . ' unit. Stok ≤ ' . self::$batasMinimumStok . ' akan memicu notifikasi peringatan.'),
                            Forms\Components\TextInput::make('material')
                                ->label('Material')
                                ->maxLength(255),
                            Forms\Components\Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true)
                                ->helperText('Produk aktif akan tersedia untuk dijual. Perubahan status akan memicu notifikasi.')
                                ->live()
                                ->afterStateUpdated(function ($state, $old) {
                                    if ($old !== null && $state !== $old) {
                                        $status = $state ? 'diaktifkan' : 'dinonaktifkan';
                                        Notification::make()
                                            ->title('Status Produk Berubah')
                                            ->body("Produk akan {$status}")
                                            ->info()
                                            ->send();
                                    }
                                }),
                        ])
                        ->columns(2),

                    Step::make('Dimensi & Berat')
                        ->description('Informasi ukuran dan berat produk untuk pengiriman')
                        ->schema([
                            Forms\Components\TextInput::make('weight')
                                ->label('Berat (gram)')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->default(1000)
                                ->helperText('Masukkan berat dalam gram. 1kg = 1000 gram'),
                            Forms\Components\TextInput::make('width')
                                ->label('Lebar (cm)')
                                ->numeric()
                                ->minValue(0)
                                ->nullable(),
                            Forms\Components\TextInput::make('length')
                                ->label('Panjang (cm)')
                                ->numeric()
                                ->minValue(0)
                                ->nullable(),
                            Forms\Components\TextInput::make('height')
                                ->label('Tinggi (cm)')
                                ->numeric()
                                ->minValue(0)
                                ->nullable(),
                        ])
                        ->columns(2),

                    Step::make('Media')
                        ->description('Upload gambar utama dan galeri produk')
                        ->schema([
                            Forms\Components\FileUpload::make('image')
                                ->label('Gambar Utama')
                                ->image()
                                ->required()
                                ->directory('products')
                                ->visibility('public')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(2048)
                                ->imageEditor()
                                ->imageEditorMode(2)
                                ->helperText('Format: JPG, PNG, WebP. Maksimal 2MB'),
                            Forms\Components\FileUpload::make('gallery')
                                ->label('Galeri Gambar')
                                ->multiple()
                                ->image()
                                ->required()
                                ->minFiles(1)
                                ->maxFiles(3)
                                ->directory('products/gallery')
                                ->visibility('public')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(2048)
                                ->imageEditor()
                                ->imageEditorMode(2)
                                ->helperText('Wajib unggah minimal 1 gambar, maksimal 3 gambar. Format: JPG, PNG, WebP. Maksimal 2MB per gambar'),
                        ]),
                ])
                ->skippable()
                ->persistStepInQueryString()
                ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar Utama')
                    ->height(80)
                    ->width(80)
                    ->circular(),
                Tables\Columns\ImageColumn::make('gallery')
                    ->label('Galeri')
                    ->circular(false)
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->height(60)
                    ->width(60),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('idr')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->color(fn (Product $record): string => match (true) {
                        $record->stock == 0 => 'danger',
                        $record->stock <= self::$batasMinimumStok => 'warning',
                        default => 'success'
                    })
                    ->badge()
                    ->icon(fn (Product $record): string => match (true) {
                        $record->stock == 0 => 'heroicon-o-x-circle',
                        $record->stock <= self::$batasMinimumStok => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-check-circle'
                    })
                    ->formatStateUsing(fn (Product $record): string => match (true) {
                        $record->stock == 0 => 'Habis',
                        $record->stock <= self::$batasMinimumStok => $record->stock . ' (Rendah)',
                        default => (string) $record->stock
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Filter Kategori')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->placeholder('Semua Status'),

                Tables\Filters\TernaryFilter::make('stok_rendah')
                    ->label('Kondisi Stok')
                    ->trueLabel('Stok Rendah (≤ ' . self::$batasMinimumStok . ')')
                    ->falseLabel('Stok Aman (> ' . self::$batasMinimumStok . ')')
                    ->placeholder('Semua Kondisi')
                    ->queries(
                        true: fn (Builder $query) => $query->where('stock', '<=', self::$batasMinimumStok),
                        false: fn (Builder $query) => $query->where('stock', '>', self::$batasMinimumStok),
                    ),

                Tables\Filters\Filter::make('stok_habis')
                    ->label('Stok Habis')
                    ->query(fn (Builder $query): Builder => $query->where('stock', 0))
                    ->toggle(),

                Tables\Filters\Filter::make('harga_range')
                    ->form([
                        Forms\Components\TextInput::make('harga_dari')
                            ->label('Harga Dari')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('harga_sampai')
                            ->label('Harga Sampai')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['harga_dari'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['harga_sampai'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['harga_dari'] ?? null) {
                            $indicators['harga_dari'] = 'Harga dari: Rp ' . number_format($data['harga_dari'], 0, ',', '.');
                        }
                        if ($data['harga_sampai'] ?? null) {
                            $indicators['harga_sampai'] = 'Harga sampai: Rp ' . number_format($data['harga_sampai'], 0, ',', '.');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),

                    // Action Stok Masuk menggunakan StockMovement
                    Tables\Actions\Action::make('stok_masuk')
                        ->label('Stok Masuk')
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->visible(fn (Product $record): bool => $record->stock <= self::$batasMinimumStok)
                        ->modalHeading('Tambah Stok Produk')
                        ->modalDescription(fn (Product $record): string => "Tambah stok untuk produk: {$record->name}")
                        ->form([
                            Forms\Components\Placeholder::make('current_stock')
                                ->label('Stok Saat Ini')
                                ->content(fn (Product $record): string => $record->stock . ' unit'),
                            Forms\Components\TextInput::make('quantity')
                                ->label('Jumlah Tambahan')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->default(50)
                                ->suffix('unit')
                                ->helperText('Masukkan jumlah stok yang akan ditambahkan'),
                            Forms\Components\Select::make('reason')
                                ->label('Alasan Penambahan')
                                ->options([
                                    'purchase' => 'Pembelian dari Supplier',
                                    'return' => 'Retur dari Customer',
                                    'production' => 'Hasil Produksi',
                                    'transfer_in' => 'Transfer Masuk',
                                    'adjustment' => 'Koreksi Stok',
                                    'other' => 'Lainnya',
                                ])
                                ->required()
                                ->default('purchase'),
                            Forms\Components\Textarea::make('notes')
                                ->label('Catatan')
                                ->placeholder('Contoh: Pembelian dari PT. ABC, Faktur No. INV-001')
                                ->maxLength(255),
                        ])
                        ->action(function (Product $record, array $data): void {
                            try {
                                // Mapping reason ke keterangan yang lebih user-friendly
                                $reasonMap = [
                                    'purchase' => 'Pembelian dari Supplier',
                                    'return' => 'Retur dari Customer',
                                    'production' => 'Hasil Produksi',
                                    'transfer_in' => 'Transfer Masuk',
                                    'adjustment' => 'Koreksi Stok',
                                    'other' => 'Lainnya',
                                ];

                                $reasonText = $reasonMap[$data['reason']] ?? 'Penambahan Stok';
                                $fullNotes = $reasonText;
                                if (!empty($data['notes'])) {
                                    $fullNotes .= ' - ' . $data['notes'];
                                }

                                StockMovement::recordStockIn(
                                    $record,
                                    (int) $data['quantity'],
                                    'StockAdjustment',
                                    null,
                                    $fullNotes
                                );

                                Notification::make()
                                    ->title('Stok Masuk Berhasil')
                                    ->body("Stok produk '{$record->name}' berhasil ditambah {$data['quantity']} unit. Total stok sekarang: {$record->fresh()->stock}")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal Menambah Stok')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    // Action Stok Keluar menggunakan StockMovement
                    Tables\Actions\Action::make('stok_keluar')
                        ->label('Stok Keluar')
                        ->icon('heroicon-o-minus-circle')
                        ->color('warning')
                        ->visible(fn (Product $record): bool => $record->stock > 0)
                        ->modalHeading('Kurangi Stok Produk')
                        ->modalDescription(fn (Product $record): string => "Kurangi stok untuk produk: {$record->name}")
                        ->form([
                            Forms\Components\Placeholder::make('current_stock')
                                ->label('Stok Saat Ini')
                                ->content(fn (Product $record): string => $record->stock . ' unit'),
                            Forms\Components\TextInput::make('quantity')
                                ->label('Jumlah Pengurangan')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->suffix('unit')
                                ->rules([
                                    function (Product $record) {
                                        return function (string $attribute, $value, \Closure $fail) use ($record) {
                                            if ($value > $record->stock) {
                                                $fail("Jumlah pengurangan tidak boleh melebihi stok saat ini ({$record->stock} unit).");
                                            }
                                        };
                                    },
                                ])
                                ->helperText(fn (Product $record): string => "Maksimal: {$record->stock} unit"),
                            Forms\Components\Select::make('reason')
                                ->label('Alasan Pengurangan')
                                ->options([
                                    'damaged' => 'Barang Rusak',
                                    'expired' => 'Kadaluarsa',
                                    'lost' => 'Kehilangan',
                                    'theft' => 'Pencurian',
                                    'transfer_out' => 'Transfer Keluar',
                                    'adjustment' => 'Koreksi Stok',
                                    'sample' => 'Sample/Demo',
                                    'other' => 'Lainnya',
                                ])
                                ->required()
                                ->default('damaged'),
                            Forms\Components\Textarea::make('notes')
                                ->label('Catatan')
                                ->placeholder('Contoh: Barang rusak saat pengiriman, Expired tanggal 01/12/2024')
                                ->maxLength(255),
                        ])
                        ->action(function (Product $record, array $data): void {
                            try {
                                // Mapping reason ke keterangan yang lebih user-friendly
                                $reasonMap = [
                                    'damaged' => 'Barang Rusak',
                                    'expired' => 'Kadaluarsa',
                                    'lost' => 'Kehilangan',
                                    'theft' => 'Pencurian',
                                    'transfer_out' => 'Transfer Keluar',
                                    'adjustment' => 'Koreksi Stok',
                                    'sample' => 'Sample/Demo',
                                    'other' => 'Lainnya',
                                ];

                                $reasonText = $reasonMap[$data['reason']] ?? 'Pengurangan Stok';
                                $fullNotes = $reasonText;
                                if (!empty($data['notes'])) {
                                    $fullNotes .= ' - ' . $data['notes'];
                                }

                                StockMovement::recordStockOut(
                                    $record,
                                    (int) $data['quantity'],
                                    'StockAdjustment',
                                    null,
                                    $fullNotes
                                );

                                Notification::make()
                                    ->title('Stok Keluar Berhasil')
                                    ->body("Stok produk '{$record->name}' berhasil dikurangi {$data['quantity']} unit. Stok sekarang: {$record->fresh()->stock}")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal Mengurangi Stok')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn (Product $record): string => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                        ->icon(fn (Product $record): string => $record->is_active ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                        ->color(fn (Product $record): string => $record->is_active ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Product $record): string => $record->is_active ? 'Nonaktifkan Produk' : 'Aktifkan Produk')
                        ->modalDescription(fn (Product $record): string => $record->is_active
                            ? 'Apakah Anda yakin ingin menonaktifkan produk ini? Produk tidak akan tersedia untuk dijual.'
                            : 'Apakah Anda yakin ingin mengaktifkan produk ini? Produk akan tersedia untuk dijual.')
                        ->action(function (Product $record): void {
                            $record->update(['is_active' => !$record->is_active]);

                            $status = $record->is_active ? 'diaktifkan' : 'dinonaktifkan';
                            Notification::make()
                                ->title('Status Berubah')
                                ->body("Produk {$record->name} berhasil {$status}")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Produk')
                        ->modalDescription('Apakah Anda yakin ingin menghapus produk ini? Aksi ini tidak dapat dibatalkan.')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Produk Dihapus')
                                ->body('Produk berhasil dihapus dari sistem.')
                        ),
                ])->tooltip('Aksi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan Produk')
                        ->icon('heroicon-o-play-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $records->each->update(['is_active' => true]);

                            Notification::make()
                                ->title('Produk Diaktifkan')
                                ->body($records->count() . ' produk berhasil diaktifkan')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan Produk')
                        ->icon('heroicon-o-pause-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $records->each->update(['is_active' => false]);

                            Notification::make()
                                ->title('Produk Dinonaktifkan')
                                ->body($records->count() . ' produk berhasil dinonaktifkan')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Produk Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua produk yang dipilih? Aksi ini tidak dapat dibatalkan.')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Produk Dihapus')
                                ->body('Produk terpilih berhasil dihapus dari sistem.')
                        ),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Produk Pertama')
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $lowStockCount = Product::where('stock', '<=', self::$batasMinimumStok)->count();
        return $lowStockCount > 0 ? (string) $lowStockCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $lowStockCount = Product::where('stock', '<=', self::$batasMinimumStok)->count();
        return $lowStockCount > 0 ? 'danger' : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $lowStockCount = Product::where('stock', '<=', self::$batasMinimumStok)->count();
        return $lowStockCount > 0 ? "{$lowStockCount} produk dengan stok rendah" : null;
    }
}
