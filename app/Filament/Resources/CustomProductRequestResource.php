<?php

namespace App\Filament\Resources;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Resources\CustomProductRequestResource\Pages;
use App\Models\CustomProductRequest;
use App\Filament\Resources\CustomProductRequestResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Services\CustomProductService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Placeholder;

class CustomProductRequestResource extends Resource
{
    protected static ?string $model = CustomProductRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Permintaan Produk Kustom';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'MENUNGGU_REVIEW')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }



public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Wizard::make([
                Step::make('Data Permintaan Customer')
                    ->description('Data asli permintaan dari customer (tidak dapat diubah)')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Forms\Components\Section::make('Informasi Customer')
                            ->description('Data customer yang mengajukan permintaan')
                            ->schema([
                                Forms\Components\TextInput::make('user.name')
                                    ->label('Nama Customer')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Data customer'),
                                Forms\Components\TextInput::make('user.email')
                                    ->label('Email Customer')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Email customer'),
                            ])
                            ->columns(2)
                            ->collapsible(),

                        Forms\Components\Section::make('Permintaan Asli')
                            ->description('Detail permintaan yang diajukan customer')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Judul Permintaan')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Deskripsi Permintaan')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->rows(4)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('specifications')
                                    ->label('Spesifikasi yang Diminta')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('Budget & Timeline Customer')
                            ->description('Budget dan deadline yang diinginkan customer')
                            ->schema([
                                Forms\Components\TextInput::make('budget')
                                    ->label('Budget Customer')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefix('Rp')
                                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', '.') : '-'),
                                Forms\Components\DatePicker::make('desired_deadline')
                                    ->label('Deadline Harapan Customer')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->native(false),
                                Forms\Components\DateTimePicker::make('created_at')
                                    ->label('Tanggal Permintaan Dibuat')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->native(false),
                            ])
                            ->columns(3)
                            ->collapsible(),
                    ])
                    ->columns(1),

                Step::make('Penawaran & Pricing')
                    ->description('Informasi penawaran harga dan timeline dari admin')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Forms\Components\Section::make('Harga Penawaran')
                            ->description('Tentukan harga dan breakdown pembayaran')
                            ->schema([
                                Forms\Components\TextInput::make('quoted_price')
                                    ->label('Harga yang Ditawarkan')
                                    ->numeric()
                                    ->required()
                                    ->prefix('Rp')
                                    ->helperText('Masukkan harga total untuk produk kustom ini')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $quotedPrice = (float) $state;

                                        // Auto calculate DP (30%) - otomatis dan tidak bisa diedit
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
                                Forms\Components\TextInput::make('down_payment')
                                    ->label('DP yang Diperlukan (30% otomatis)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled() // Tidak bisa diedit
                                    ->dehydrated(true) // Tetap disimpan ke database
                                    ->helperText('Otomatis dihitung 30% dari harga total'),
                                Forms\Components\TextInput::make('remaining_payment')
                                    ->label('Sisa Pembayaran')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Otomatis dihitung: Harga Total - DP'),
                            ])
                            ->columns(3),

                        Forms\Components\Section::make('Timeline Pengerjaan')
                            ->description('Estimasi waktu penyelesaian')
                            ->schema([
                                Forms\Components\DatePicker::make('estimated_completion')
                                    ->label('Estimasi Penyelesaian')
                                    ->required()
                                    ->native(false)
                                    ->helperText('Tanggal estimasi produk akan selesai'),
                            ])
                            ->columns(1),

                        Forms\Components\Section::make('Catatan Admin')
                            ->description('Catatan tambahan untuk customer')
                            ->schema([
                                Forms\Components\Textarea::make('admin_notes')
                                    ->label('Catatan Admin')
                                    ->rows(3)
                                    ->placeholder('Catatan tambahan untuk pelanggan')
                                    ->helperText('Catatan ini akan dilihat oleh customer')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),
                    ])
                    ->visible(fn (string $operation): bool => $operation === 'edit'),

                Step::make('Status & Progress Tracking')
                    ->description('Kelola status pengerjaan dan tracking progress')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Forms\Components\Section::make('Status Management')
                            ->description('Update status pengerjaan pesanan')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status Pesanan')
                                    ->options([
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
                                    ])
                                    ->required()
                                    ->helperText('Pilih status terkini dari pesanan'),
                            ])
                            ->columns(1),

                        Forms\Components\Section::make('Timeline Tracking')
                            ->description('Tracking otomatis timeline pengerjaan (diisi sistem)')
                            ->schema([
                                Forms\Components\DatePicker::make('work_started_at')
                                    ->label('Pengerjaan Dimulai')
                                    ->disabled()
                                    ->native(false)
                                    ->helperText('Diisi otomatis saat status berubah ke "Dalam Pengerjaan"'),
                                Forms\Components\DatePicker::make('work_completed_at')
                                    ->label('Pengerjaan Selesai')
                                    ->disabled()
                                    ->native(false)
                                    ->helperText('Diisi otomatis saat produk diselesaikan'),
                                Forms\Components\DatePicker::make('shipping_date')
                                    ->label('Tanggal Pengiriman')
                                    ->disabled()
                                    ->native(false)
                                    ->helperText('Diisi otomatis saat produk dikirim'),
                                Forms\Components\DatePicker::make('delivery_date')
                                    ->label('Tanggal Penerimaan')
                                    ->disabled()
                                    ->native(false)
                                    ->helperText('Diisi otomatis saat produk diterima customer'),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ])
                    ->visible(fn (string $operation): bool => $operation === 'edit'),

                Step::make('Informasi Pembayaran')
                    ->description('Detail tracking pembayaran DP dan pelunasan')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Forms\Components\Section::make('Pembayaran DP')
                            ->description('Informasi pembayaran DP (diisi otomatis oleh sistem)')
                            ->schema([
                                Forms\Components\TextInput::make('dp_payment_code')
                                    ->label('Kode Pembayaran DP')
                                    ->disabled()
                                    ->placeholder('Akan diisi otomatis setelah pembayaran')
                                    ->helperText('Kode unik pembayaran DP dari sistem'),
                                Forms\Components\DateTimePicker::make('dp_payment_date')
                                    ->label('Tanggal Pembayaran DP')
                                    ->disabled()
                                    ->native(false)
                                    ->helperText('Waktu pembayaran DP diterima'),
                            ])
                            ->columns(2)
                            ->collapsible(),

                        Forms\Components\Section::make('Pembayaran Pelunasan')
                            ->description('Informasi pelunasan (diisi otomatis oleh sistem)')
                            ->schema([
                                Forms\Components\TextInput::make('full_payment_code')
                                    ->label('Kode Pembayaran Pelunasan')
                                    ->disabled()
                                    ->placeholder('Akan diisi otomatis setelah pelunasan')
                                    ->helperText('Kode unik pembayaran pelunasan dari sistem'),
                                Forms\Components\DateTimePicker::make('full_payment_date')
                                    ->label('Tanggal Pelunasan')
                                    ->disabled()
                                    ->native(false)
                                    ->helperText('Waktu pelunasan diterima'),
                            ])
                            ->columns(2)
                            ->collapsible(),

                        Forms\Components\Section::make('Ringkasan Pembayaran')
    ->description('Ringkasan total pembayaran')
    ->schema([
        Forms\Components\Grid::make(3)
            ->schema([
                Forms\Components\Placeholder::make('total_price')
                    ->label('Total Harga')
                    ->content(function ($record) {
                        if (!$record || !$record->quoted_price) return 'Belum ditentukan';
                        return 'Rp ' . number_format($record->quoted_price, 0, ',', '.');
                    }),

                Forms\Components\Placeholder::make('dp_paid')
                    ->label('DP Dibayar')
                    ->content(function ($record) {
                        if (!$record || !$record->down_payment) return 'Belum dibayar';
                        return 'Rp ' . number_format($record->down_payment, 0, ',', '.');
                    }),

                Forms\Components\Placeholder::make('remaining_payment')
                    ->label('Sisa Pembayaran')
                    ->content(function ($record) {
                        if (!$record || !$record->quoted_price) return 'Belum dihitung';

                        $quotedPrice = $record->quoted_price ?? 0;
                        $downPayment = $record->down_payment ?? 0;
                        $remaining = $quotedPrice - $downPayment;

                        return 'Rp ' . number_format($remaining, 0, ',', '.');
                    }),
            ]),

        Forms\Components\Placeholder::make('payment_status')
            ->label('Status Pembayaran')
            ->content(function ($record) {
                if (!$record) return 'Data belum tersedia';

                $dpPaid = !empty($record->dp_payment_date);
                $fullPaid = !empty($record->full_payment_date);

                if ($fullPaid) {
                    return '✅ Lunas';
                } elseif ($dpPaid) {
                    return '🟡 DP Sudah Dibayar';
                } else {
                    return '❌ Belum Dibayar';
                }
            })
            ->columnSpanFull(),
    ])
    ->collapsible(),
                    ])
                    ->visible(fn (string $operation): bool => $operation === 'edit'),

                // STEP KHUSUS UNTUK CREATE - Data Customer bisa dipilih
                Step::make('Pilih Customer & Input Permintaan')
                    ->description('Pilih customer dan masukkan detail permintaan produk kustom')
                    ->icon('heroicon-o-user-plus')
                    ->schema([
                        Forms\Components\Section::make('Customer')
                            ->description('Pilih customer yang mengajukan permintaan')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Customer')
                                    ->placeholder('Pilih customer')
                                    ->helperText('Cari dan pilih customer dari database'),
                            ])
                            ->columns(1),

                        Forms\Components\Section::make('Detail Permintaan')
                            ->description('Masukkan detail permintaan produk kustom')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Judul Permintaan')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Pembuatan Kaos Custom dengan Logo Perusahaan')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Deskripsi Permintaan')
                                    ->required()
                                    ->rows(4)
                                    ->placeholder('Jelaskan detail produk yang diinginkan')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('specifications')
                                    ->label('Spesifikasi')
                                    ->rows(4)
                                    ->placeholder('Jelaskan spesifikasi teknis yang diinginkan (ukuran, bahan, warna, dll)')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Section::make('Budget & Timeline')
                            ->description('Informasi budget dan deadline dari customer')
                            ->schema([
                                Forms\Components\TextInput::make('budget')
                                    ->label('Budget Customer')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('Masukkan budget yang tersedia'),
                                Forms\Components\DatePicker::make('desired_deadline')
                                    ->label('Deadline Harapan')
                                    ->placeholder('Pilih tanggal deadline yang diinginkan')
                                    ->native(false),
                            ])
                            ->columns(2),
                    ])
                    ->visible(fn (string $operation): bool => $operation === 'create'),
            ])
            ->skippable() // ✅ Steps bisa diklik langsung
            ->persistStepInQueryString() // ✅ Step tersimpan di URL
            ->columnSpanFull()
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn ($state) => match (true) {
    in_array($state, ['MENUNGGU_REVIEW', 'MENUNGGU_PELUNASAN']) => 'warning',
    in_array($state, ['PENAWARAN_DIBERIKAN', 'SIAP_DIKIRIM']) => 'primary',
    in_array($state, ['PENAWARAN_DITOLAK', 'DIBATALKAN']) => 'danger',
    in_array($state, ['MENUNGGU_DP', 'SELESAI']) => 'success',
    $state === 'DALAM_PENGERJAAN' => 'info',
    $state === 'DIKIRIM' => 'primary',
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

                Tables\Columns\TextColumn::make('quoted_price')
                    ->label('Harga Penawaran')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(function (TextColumn $column): ?string {
                        return $column->getRecord()->created_at->format('d F Y H:i:s');
                    }),
                Tables\Columns\TextColumn::make('estimated_completion')
                    ->label('Estimasi Selesai')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc') // ✅ Data terbaru di atas
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
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
                    ])
                    ->placeholder('Semua Status'),

                // Filter berdasarkan pelanggan
                Tables\Filters\Filter::make('user')
                    ->label('Filter Pelanggan')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('Pelanggan')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->placeholder('Pilih pelanggan...')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['user_id'],
                            fn (Builder $query, $userId): Builder => $query->where('user_id', $userId)
                        );
                    }),

                // Filter berdasarkan rentang tanggal
                Tables\Filters\Filter::make('created_at')
                    ->label('Filter Tanggal Dibuat')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal')
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                // Filter berdasarkan rentang harga
                Tables\Filters\Filter::make('quoted_price')
                    ->label('Filter Harga')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('Harga Minimum')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('price_to')
                            ->label('Harga Maksimum')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('quoted_price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('quoted_price', '<=', $price),
                            );
                    }),
            ])
           ->actions([
    // GROUP 1: ACTIONS UTAMA PENGELOLAAN PESANAN
    ActionGroup::make([
        // ACTION: Buat Penawaran dengan Wizard Steps
        Action::make('create_offer')
            ->label('Buat Penawaran')
            ->icon('heroicon-o-currency-dollar')
            ->color('success')
            ->steps([
                Step::make('Info Permintaan')
                    ->description('Data permintaan dari customer (tidak bisa diubah)')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Permintaan')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (CustomProductRequest $record) => $record->title),
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nama Customer')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (CustomProductRequest $record) => $record->user->name),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Permintaan')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(3)
                            ->default(fn (CustomProductRequest $record) => $record->description),
                        Forms\Components\Textarea::make('specifications')
                            ->label('Spesifikasi yang Diminta')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(3)
                            ->default(fn (CustomProductRequest $record) => $record->specifications),
                    ])
                    ->columns(2),

                Step::make('Budget Customer')
                    ->description('Informasi budget dari customer')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Forms\Components\TextInput::make('customer_budget')
                            ->label('Budget Customer')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefix('Rp')
                            ->default(fn (CustomProductRequest $record) => number_format($record->budget, 0, ',', '.')),
                        Forms\Components\DatePicker::make('desired_deadline')
                            ->label('Deadline Harapan Customer')
                            ->disabled()
                            ->dehydrated(false)
                            ->native(false)
                            ->default(fn (CustomProductRequest $record) => $record->desired_deadline),
                    ])
                    ->columns(2),

                Step::make('Buat Penawaran')
                    ->description('Buat penawaran harga dan timeline')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Forms\Components\TextInput::make('quoted_price')
                            ->label('Harga yang Ditawarkan')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                $quotedPrice = (float) $state;
                                if ($quotedPrice > 0) {
                                    $autoDownPayment = $quotedPrice * 0.3;
                                    $set('down_payment', $autoDownPayment);
                                    $remainingPayment = $quotedPrice - $autoDownPayment;
                                    $set('remaining_payment', max(0, $remainingPayment));
                                } else {
                                    $set('down_payment', 0);
                                    $set('remaining_payment', 0);
                                }
                            }),
                        Forms\Components\TextInput::make('down_payment')
                            ->label('DP yang Diperlukan (30% otomatis)')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(true)
                            ->helperText('Otomatis dihitung 30% dari harga total'),
                        Forms\Components\TextInput::make('remaining_payment')
                            ->label('Sisa Pembayaran')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Otomatis dihitung: Harga Total - DP'),
                        Forms\Components\DatePicker::make('estimated_completion')
                            ->label('Estimasi Penyelesaian')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Step::make('Catatan Admin')
                    ->description('Tambahkan catatan untuk customer')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Catatan Admin')
                            ->placeholder('Catatan tambahan untuk customer mengenai penawaran ini')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ])
            ->action(function (array $data, CustomProductRequest $record, CustomProductService $customProductService) {
                try {
                    $customProductService->createOffer($record, $data);
                    Notification::make()
                        ->title('Penawaran berhasil dibuat')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Log::error('Error creating offer: '.$e->getMessage());
                    Notification::make()
                        ->title('Gagal membuat penawaran')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->visible(fn (CustomProductRequest $record): bool => $record->status === 'MENUNGGU_REVIEW'),

        // ACTION: Batalkan Permintaan dengan Wizard Steps
        Action::make('cancel_request')
            ->label('Batalkan')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->steps([
                Step::make('Info Pesanan')
                    ->description('Pesanan yang akan dibatalkan')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('order_title')
                            ->label('Judul Pesanan')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (CustomProductRequest $record) => $record->title),
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Customer')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (CustomProductRequest $record) => $record->user->name),
                        Forms\Components\TextInput::make('current_status')
                            ->label('Status Saat Ini')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (CustomProductRequest $record) => $record->status),
                    ])
                    ->columns(2),

                Step::make('Alasan Pembatalan')
                    ->description('Berikan alasan mengapa pesanan dibatalkan')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->schema([
                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan Pembatalan')
                            ->required()
                            ->placeholder('Jelaskan alasan pembatalan pesanan ini secara detail')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Step::make('Konfirmasi Pembatalan')
                    ->description('Konfirmasi pembatalan pesanan')
                    ->icon('heroicon-o-shield-exclamation')
                    ->schema([
                        Forms\Components\Section::make('⚠️ Perhatian')
                            ->description('Pesanan akan dibatalkan secara permanen. Customer akan diberitahu tentang pembatalan. Jika ada pembayaran DP, perlu ditangani manual. Tindakan ini tidak dapat dibatalkan.')
                            ->icon('heroicon-o-exclamation-triangle')
                            ->schema([])
                            ->columnSpanFull(),
                    ]),
            ])
            ->action(function (array $data, CustomProductRequest $record, CustomProductService $customProductService) {
                try {
                    $customProductService->cancelRequest($record, $data['reason']);
                    Notification::make()
                        ->title('Permintaan berhasil dibatalkan')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Log::error('Error cancelling request: '.$e->getMessage());
                    Notification::make()
                        ->title('Gagal membatalkan permintaan')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->visible(fn (CustomProductRequest $record): bool => !in_array($record->status, ['SELESAI', 'DIBATALKAN'])),
    ])
    ->label('Kelola Pesanan')
    ->icon('heroicon-o-cog-6-tooth')
    ->color('primary'),

    // GROUP 2: ACTIONS PROGRESS & PRODUKSI
    ActionGroup::make([
        // ACTION: Tambah Progress dengan Wizard Steps
        Action::make('add_progress')
            ->label('Tambah Progress')
            ->icon('heroicon-o-photo')
            ->color('info')
            ->steps([
                Step::make('Info Pesanan')
                    ->description('Informasi pesanan yang sedang dikerjakan')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('order_title')
                            ->label('Judul Pesanan')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (CustomProductRequest $record) => $record->title),
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Customer')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (CustomProductRequest $record) => $record->user->name),
                        Forms\Components\TextInput::make('payment_code')
                            ->label('Kode Pembayaran DP')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (CustomProductRequest $record) => $record->dp_payment_code),
                    ])
                    ->columns(2),

                Step::make('Upload Progress')
                    ->description('Upload foto dan deskripsi progress pengerjaan')
                    ->icon('heroicon-o-camera')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Foto Progress')
                            ->image()
                            ->required()
                            ->directory('custom-product-progresses')
                            ->helperText('Upload foto progress pengerjaan produk'),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Progress')
                            ->required()
                            ->placeholder('Jelaskan progress yang telah dicapai, tahapan yang sedang dikerjakan, dll.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ])
            ->action(function (array $data, CustomProductRequest $record, CustomProductService $customProductService) {
                try {
                    $customProductService->addProgressUpdate(
                        $record,
                        $data['image'],
                        $data['description']
                    );
                    Notification::make()
                        ->title('Progress berhasil ditambahkan')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Log::error('Error adding progress: '.$e->getMessage());
                    Notification::make()
                        ->title('Gagal menambahkan progress')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->visible(fn (CustomProductRequest $record): bool => $record->status === 'DALAM_PENGERJAAN'),

        // ACTION: Selesai Dikerjakan dengan Wizard Steps
        Action::make('mark_completed')
            ->label('Selesai Dikerjakan')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->steps([
                Step::make('Info Pesanan')
                    ->description('Ringkasan pesanan yang akan diselesaikan')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('order_title')
                            ->label('Judul Pesanan')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (CustomProductRequest $record) => $record->title),
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Customer')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (CustomProductRequest $record) => $record->user->name),
                        Forms\Components\TextInput::make('quoted_price_display')
                            ->label('Harga Total')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefix('Rp')
                            ->default(fn (CustomProductRequest $record) => number_format($record->quoted_price, 0, ',', '.')),
                        Forms\Components\TextInput::make('remaining_payment_display')
                            ->label('Sisa Pembayaran yang Harus Dilunasi')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefix('Rp')
                            ->default(fn (CustomProductRequest $record) => number_format($record->quoted_price - $record->down_payment, 0, ',', '.')),
                    ])
                    ->columns(2),

                Step::make('Upload Foto Produk Final')
                    ->description('Upload foto produk yang telah selesai dikerjakan')
                    ->icon('heroicon-o-camera')
                    ->schema([
                        Forms\Components\FileUpload::make('final_product_image')
                            ->label('Foto Produk Jadi')
                            ->image()
                            ->required()
                            ->directory('custom-product-finals')
                            ->helperText('Upload foto produk yang telah selesai dikerjakan'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Produk Final')
                            ->placeholder('Berikan detail tentang produk jadi, spesifikasi, atau petunjuk penggunaan')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Step::make('Konfirmasi Penyelesaian')
                    ->description('Konfirmasi bahwa produk sudah selesai dan siap untuk pelunasan')
                    ->icon('heroicon-o-check-badge')
                    ->schema([
                        Forms\Components\Section::make('Konfirmasi')
                            ->description('Yang akan terjadi setelah konfirmasi: Status pesanan akan berubah menjadi "Menunggu Pelunasan", Customer akan diberitahu bahwa produk sudah selesai, Customer dapat melihat foto produk final, Customer diminta untuk melakukan pelunasan')
                            ->icon('heroicon-o-information-circle')
                            ->schema([])
                            ->columnSpanFull(),
                    ]),
            ])
            ->action(function (array $data, CustomProductRequest $record, CustomProductService $customProductService) {
                try {
                    $finalProduct = $record->finalProduct()->create([
                        'image_path' => $data['final_product_image'],
                        'notes' => $data['notes'] ?? null,
                    ]);

                    $customProductService->updateStatus($record, 'MENUNGGU_PELUNASAN');

                    Notification::make()
                        ->title('Produk berhasil diselesaikan')
                        ->body('Status berhasil diubah ke Menunggu Pelunasan dan foto produk final telah disimpan.')
                        ->success()
                        ->send();

                } catch (\Exception $e) {
                    Log::error('Error marking request as completed: '.$e->getMessage());
                    Notification::make()
                        ->title('Gagal menyelesaikan produk')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->visible(fn (CustomProductRequest $record): bool => $record->status === 'DALAM_PENGERJAAN'),

        // ACTION: Upload Foto Final Product dengan Wizard Steps
        Action::make('upload_final_product')
            ->label('Upload Foto Produk Final')
            ->icon('heroicon-o-camera')
            ->color('primary')
            ->steps([
                Step::make('Info Pesanan')
                    ->description('Informasi pesanan yang akan diupload foto finalnya')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('order_title')
                            ->label('Judul Pesanan')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (CustomProductRequest $record) => $record->title),
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Customer')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (CustomProductRequest $record) => $record->user->name),
                    ])
                    ->columns(2),

                Step::make('Upload Foto')
                    ->description('Upload foto dan catatan produk final')
                    ->icon('heroicon-o-camera')
                    ->schema([
                        Forms\Components\FileUpload::make('final_product_image')
                            ->label('Foto Produk Jadi')
                            ->image()
                            ->required()
                            ->directory('custom-product-finals')
                            ->helperText('Upload foto produk yang telah selesai dikerjakan'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Produk Final')
                            ->placeholder('Berikan detail tentang produk jadi, spesifikasi, atau petunjuk penggunaan')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ])
            ->action(function (array $data, CustomProductRequest $record) {
                try {
                    $finalProduct = $record->finalProduct()->create([
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
            ->visible(fn (CustomProductRequest $record): bool =>
                $record->status === 'MENUNGGU_PELUNASAN' && !$record->finalProduct
            ),
    ])
    ->label('Progress & Produksi')
    ->icon('heroicon-o-wrench-screwdriver')
    ->color('info'),

    // GROUP 3: ACTIONS LAPORAN & DOKUMEN
    ActionGroup::make([
        // ACTION: Cetak PDF untuk Gudang
        Action::make('print_warehouse_pdf')
            ->label('Cetak PDF Gudang')
            ->icon('heroicon-o-printer')
            ->color('primary')
            ->action(function (CustomProductRequest $record) {
                try {
                    $pdf = self::generateWarehousePdf($record);
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, "Pesanan_Gudang_{$record->id}_{$record->dp_payment_code}.pdf");
                } catch (\Exception $e) {
                    Log::error('Error generating warehouse PDF: '.$e->getMessage());
                    Notification::make()
                        ->title('Gagal membuat PDF')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->visible(fn (CustomProductRequest $record): bool =>
                $record->status === 'DALAM_PENGERJAAN' &&
                !empty($record->dp_payment_code) &&
                !empty($record->dp_payment_date)
            ),
    ])
    ->label('Laporan & Dokumen')
    ->icon('heroicon-o-document-text')
    ->color('warning'),
])
->recordUrl(
    fn (CustomProductRequest $record): string => static::getUrl('view', ['record' => $record])
)
->bulkActions([
    Tables\Actions\BulkActionGroup::make([
        Tables\Actions\DeleteBulkAction::make(),
    ]),
]);
    }

    /**
     * Generate PDF untuk gudang dengan informasi lengkap
     */
    protected static function generateWarehousePdf(CustomProductRequest $record)
    {
        // Load data dengan relasi
        $record->load(['user', 'references', 'progresses']);

        // Data untuk PDF
        $data = [
            'record' => $record,
            'references' => $record->references,
            'generatedAt' => now()->format('d F Y H:i:s'),
        ];

        // Generate PDF menggunakan view
        $pdf = Pdf::loadView('pdf.warehouse-order', $data);

        // Set paper size dan orientasi
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReferencesRelationManager::class,
            RelationManagers\ProgressesRelationManager::class,
            RelationManagers\ShipmentsRelationManager::class,
            RelationManagers\FinalProductRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomProductRequests::route('/'),
            'create' => Pages\CreateCustomProductRequest::route('/create'),
            'view' => Pages\ViewCustomProductRequest::route('/{record}'),
            'edit' => Pages\EditCustomProductRequest::route('/{record}/edit'),
        ];
    }
}
