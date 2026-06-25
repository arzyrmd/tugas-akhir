<?php

namespace App\Filament\Resources\SalesReportResource\Pages;

use App\Filament\Resources\SalesReportResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Support\Enums\FontWeight;
use Filament\Actions\Action;
use Filament\Forms;
use Illuminate\Contracts\Support\Htmlable;

class ViewSalesReport extends ViewRecord
{
    protected static string $resource = SalesReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_single_pdf')
                ->label('Export ke PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    return $this->exportSingleRecordToPdf();
                }),


        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Detail Laporan Penjualan - ' . $this->record->order_number;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Pesanan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('periode')
                                    ->label('Periode')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),

                                TextEntry::make('order_type')
                                    ->label('Tipe Pesanan')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'CUSTOM' => 'warning',
                                        'REGULER' => 'primary',
                                        default => 'gray',
                                    }),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('order_number')
                                    ->label('Nomor Pesanan')
                                    ->weight(FontWeight::Bold)
                                    ->copyable(),

                                TextEntry::make('customer_name')
                                    ->label('Nama Pelanggan')
                                    ->weight(FontWeight::Bold),
                            ]),
                    ])
                    ->icon('heroicon-o-shopping-bag')
                    ->collapsible(),

                Section::make('Detail Produk')
                    ->schema([
                        TextEntry::make('product_info')
                            ->label('Informasi Produk')
                            ->html()
                            ->formatStateUsing(function ($record) {
                                if ($record->order_type === 'CUSTOM') {
                                    return '<div class="whitespace-pre-wrap">' .
                                           '<strong>Deskripsi Kustom:</strong><br>' .
                                           ($record->description ?? 'Tidak ada deskripsi') .
                                           '</div>';
                                }

                                // Untuk pesanan reguler, ambil detail produk
                                try {
                                    $orderItems = \App\Models\OrderItem::where('order_id', $record->original_id)
                                        ->with('product')
                                        ->get();

                                    if ($orderItems->isEmpty()) {
                                        return '<div class="text-gray-500">Tidak ada produk ditemukan</div>';
                                    }

                                    $productDetails = $orderItems->map(function ($item) {
                                        $productName = $item->product ? $item->product->name : 'Produk tidak ditemukan';
                                        return '<div class="mb-2">' .
                                               '<strong>' . $productName . '</strong><br>' .
                                               'Qty: ' . $item->quantity . '<br>' .
                                               'Harga: Rp ' . number_format($item->price, 0, ',', '.') . '<br>' .
                                               'Subtotal: Rp ' . number_format($item->quantity * $item->price, 0, ',', '.') .
                                               '</div>';
                                    })->join('<hr class="my-2">');

                                    return '<div class="space-y-2">' . $productDetails . '</div>';
                                } catch (\Exception $e) {
                                    return '<div class="text-red-500">Error memuat produk: ' . $e->getMessage() . '</div>';
                                }
                            }),
                    ])
                    ->icon('heroicon-o-cube')
                    ->collapsible(),

                Group::make([
                    Section::make('Status & Pembayaran')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('status')
                                        ->label('Status Pesanan')
                                        ->formatStateUsing(function ($record) {
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

                                    TextEntry::make('payment_status')
                                        ->label('Status Pembayaran')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'LUNAS' => 'success',
                                            'DP DIBAYAR' => 'warning',
                                            'BELUM DIBAYAR' => 'danger',
                                            default => 'gray',
                                        }),
                                ]),
                        ])
                        ->icon('heroicon-o-credit-card'),

                    Section::make('Informasi Finansial')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('total_amount')
                                        ->label('Total Penjualan')
                                        ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                                        ->weight(FontWeight::Bold)
                                        ->color('success')
                                        ->size('lg'),

                                    TextEntry::make('profit_margin')
                                        ->label('Margin Keuntungan')
                                        ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                                        ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                                        ->weight(FontWeight::Bold),
                                ]),
                        ])
                        ->icon('heroicon-o-banknotes'),
                ])
                ->columnSpan(1),

                Section::make('Timeline Pesanan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('order_created_at')
                                    ->label('Tanggal Pesanan')
                                    ->dateTime('d F Y, H:i')
                                    ->icon('heroicon-o-calendar-days'),

                                TextEntry::make('payment_date')
                                    ->label('Tanggal Pembayaran')
                                    ->dateTime('d F Y, H:i')
                                    ->icon('heroicon-o-credit-card')
                                    ->placeholder('Belum ada pembayaran'),
                            ]),

                        TextEntry::make('created_at')
                            ->label('Dibuat pada')
                            ->dateTime('d F Y, H:i')
                            ->icon('heroicon-o-clock'),
                    ])
                    ->icon('heroicon-o-clock')
                    ->collapsible(),

                // Section untuk detail tambahan jika ada
                Section::make('Catatan & Detail Tambahan')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull(),

                        TextEntry::make('original_id')
                            ->label('ID Referensi')
                            ->placeholder('Tidak tersedia'),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    protected function exportSingleRecordToPdf()
    {
        $record = $this->record;

        // Create a collection with single record for consistency with bulk export
        $data = collect([$record]);

        $statistics = [
            'total_orders' => 1,
            'total_revenue' => $record->total_amount,
            'orders_by_type' => [$record->order_type => 1],
            'orders_by_status' => [$record->payment_status => 1],
            'period' => [
                'start' => $record->order_created_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                'end' => $record->order_created_at?->format('Y-m-d') ?? now()->format('Y-m-d')
            ]
        ];

        $filters = [
            'order_type' => $record->order_type,
            'payment_status' => $record->payment_status
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.sales-report-pdf', [
            'data' => $data,
            'statistics' => $statistics,
            'filters' => $filters,
            'single_record' => true
        ]);

        $filename = 'detail-pesanan-' . $record->order_number . '-' . date('Y-m-d-H-i-s') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
