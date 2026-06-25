<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesReportResource\Pages;
use App\Models\SalesReportView;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SalesReportResource extends Resource
{
    protected static ?string $model = SalesReportView::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Penjualan';
    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('order_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CUSTOM' => 'warning',
                        'REGULER' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('order_number')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product_info')
                    ->label('Produk')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        if ($record->order_type === 'CUSTOM') {
                            return $record->description ?? 'Tidak ada deskripsi';
                        }

                        // Perbaikan untuk menampilkan produk pesanan reguler
                        try {
                            $orderItems = \App\Models\OrderItem::where('order_id', $record->original_id)
                                ->with('product')
                                ->get();

                            if ($orderItems->isEmpty()) {
                                return 'Tidak ada produk ditemukan';
                            }

                            return $orderItems->map(function ($item) {
                                $productName = $item->product ? $item->product->name : 'Produk tidak ditemukan';
                                return $productName . ' (Qty: ' . $item->quantity . ', Harga: Rp ' . number_format($item->price, 0, ',', '.') . ')';
                            })->join("\n");
                        } catch (\Exception $e) {
                            return 'Error memuat produk: ' . $e->getMessage();
                        }
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        if ($record->order_type === 'CUSTOM') {
                            return match($record->status) {
                                'MENUNGGU_REVIEW' => 'MENUNGGU REVIEW',
                                'PENAWARAN_DIBERIKAN' => 'PENAWARAN DIBERIKAN',
                                'PENAWARAN_DITOLAK' => 'PENAWARAN DITOLAK',
                                'MENUNGGU_DP' => 'MENUNGGU PEMBAYARAN',
                                'DALAM_PENGERJAAN' => 'DALAM PENGERJAAN',
                                'MENUNGGU_PELUNASAN' => 'MENUNGGU PELUNASAN',
                                'SIAP_DIKIRIM' => 'SIAP DIKIRIM',
                                'DIKIRIM' => 'DIKIRIM',
                                'SELESAI' => 'SELESAI',
                                'DIBATALKAN' => 'DIBATALKAN',
                                default => $record->status
                            };
                        }
                        return $record->status;
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'MENUNGGU PEMBAYARAN', 'MENUNGGU REVIEW', 'MENUNGGU DP' => 'warning',
                        'PEMBAYARAN BERHASIL', 'SELESAI' => 'success',
                        'DIKEMAS', 'DALAM PENGERJAAN' => 'info',
                        'SIAP DIKIRIM', 'PENAWARAN DIBERIKAN' => 'primary',
                        'DIKIRIM' => 'purple',
                        'DIBATALKAN', 'PENAWARAN DITOLAK' => 'danger',
                        'MENUNGGU PELUNASAN' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Penjualan')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'LUNAS' => 'success',
                        'DP DIBAYAR' => 'warning',
                        'BELUM DIBAYAR' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('order_created_at')
                    ->label('Tanggal Pesanan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('profit_margin')
                    ->label('Margin Keuntungan')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order_type')
                    ->label('Tipe Pesanan')
                    ->options([
                        'REGULER' => 'Pesanan Reguler',
                        'CUSTOM' => 'Produk Kustom',
                    ]),

                Filter::make('periode_penjualan')
                    ->label('Periode Penjualan')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari_tanggal'], fn (Builder $query, $date) => $query->whereDate('order_created_at', '>=', $date))
                            ->when($data['sampai_tanggal'], fn (Builder $query, $date) => $query->whereDate('order_created_at', '<=', $date));
                    }),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options([
                        'LUNAS' => 'Lunas',
                        'DP DIBAYAR' => 'DP Dibayar',
                        'BELUM DIBAYAR' => 'Belum Dibayar',
                    ]),

                Filter::make('nilai_penjualan')
                    ->label('Rentang Nilai Penjualan')
                    ->form([
                        Forms\Components\TextInput::make('min_total')
                            ->label('Minimal')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('max_total')
                            ->label('Maksimal')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['min_total'], fn (Builder $query, $amount) => $query->where('total_amount', '>=', $amount))
                            ->when($data['max_total'], fn (Builder $query, $amount) => $query->where('total_amount', '<=', $amount));
                    }),
            ])
            ->headerActions([
                // Tombol Export PDF
                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->default(now()->startOfMonth())
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Akhir')
                            ->default(now()->endOfMonth())
                            ->required(),
                        Forms\Components\Select::make('order_type')
                            ->label('Tipe Pesanan')
                            ->options([
                                'ALL' => 'Semua Tipe',
                                'REGULER' => 'Pesanan Reguler',
                                'CUSTOM' => 'Produk Kustom',
                            ])
                            ->default('ALL'),
                        Forms\Components\Select::make('payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                'ALL' => 'Semua Status',
                                'LUNAS' => 'Lunas',
                                'DP DIBAYAR' => 'DP Dibayar',
                                'BELUM DIBAYAR' => 'Belum Dibayar',
                            ])
                            ->default('ALL'),
                    ])
                    ->action(function (array $data) {
                        return self::exportToPdf($data);
                    }),


            ])
            ->defaultSort('order_created_at', 'desc');
    }

    public static function exportToPdf(array $filters): \Symfony\Component\HttpFoundation\Response
    {
        $query = SalesReportView::query();

        // Apply filters
        if (isset($filters['start_date'])) {
            $query->whereDate('order_created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('order_created_at', '<=', $filters['end_date']);
        }

        if (isset($filters['order_type']) && $filters['order_type'] !== 'ALL') {
            $query->where('order_type', $filters['order_type']);
        }

        if (isset($filters['payment_status']) && $filters['payment_status'] !== 'ALL') {
            $query->where('payment_status', $filters['payment_status']);
        }

        $data = $query->orderBy('order_created_at', 'desc')->get();

        // Calculate statistics
        $statistics = [
            'total_orders' => $data->count(),
            'total_revenue' => $data->sum('total_amount'),
            'orders_by_type' => $data->groupBy('order_type')->map->count(),
            'orders_by_status' => $data->groupBy('payment_status')->map->count(),
            'period' => [
                'start' => $filters['start_date'],
                'end' => $filters['end_date']
            ]
        ];

        $pdf = Pdf::loadView('reports.sales-report-pdf', [
            'data' => $data,
            'statistics' => $statistics,
            'filters' => $filters
        ]);

        $filename = 'laporan-penjualan-' . date('Y-m-d-H-i-s') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalesReports::route('/'),
            'view' => Pages\ViewSalesReport::route('/{record}'),
        ];
    }
}
