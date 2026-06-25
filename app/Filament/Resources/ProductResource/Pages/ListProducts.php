<?php

namespace App\Filament\Resources\ProductResource\Pages;
use App\Models\OrderItem;
use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Models\StockMovement; // Gunakan model StockMovement baru
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Produk'),

            // Tambah Action untuk cetak laporan stok
            Action::make('cetakLaporanStok')
                ->label('Cetak Laporan Stok')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->modalHeading('Cetak Laporan Stok')
                ->modalDescription('Pilih jenis laporan dan periode yang diinginkan')
                ->form([
                    \Filament\Forms\Components\Select::make('jenis_laporan')
                        ->label('Jenis Laporan')
                        ->options([
                            'stok_masuk' => 'Stok Masuk',
                            'stok_keluar' => 'Stok Keluar',
                            'stok_keseluruhan' => 'Stok Keseluruhan',
                        ])
                        ->required(),
                    \Filament\Forms\Components\DatePicker::make('tanggal_mulai')
                        ->label('Dari Tanggal')
                        ->default(now()->subDays(30))
                        ->required(),
                    \Filament\Forms\Components\DatePicker::make('tanggal_akhir')
                        ->label('Sampai Tanggal')
                        ->default(now())
                        ->required(),
                ])
                ->action(function (array $data) {
                    return $this->generatePdf($data['jenis_laporan'], $data['tanggal_mulai'], $data['tanggal_akhir']);
                }),

            // Action untuk Stok Masuk
            Action::make('stokMasuk')
                ->label('Stok Masuk')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->modalHeading('Penambahan Stok Produk')
                ->modalDescription('Gunakan form ini untuk menambah stok produk (pembelian, retur, dll)')
                ->form([
                    \Filament\Forms\Components\Select::make('product_id')
                        ->label('Pilih Produk')
                        ->options(Product::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            if ($state) {
                                $product = Product::find($state);
                                if ($product) {
                                    $set('current_stock', $product->stock);
                                }
                            }
                        }),
                    \Filament\Forms\Components\Placeholder::make('current_stock_display')
                        ->label('Stok Saat Ini')
                        ->content(function ($get) {
                            $stock = $get('current_stock');
                            return $stock ? $stock . ' unit' : '-';
                        }),
                    \Filament\Forms\Components\Hidden::make('current_stock'),
                    \Filament\Forms\Components\TextInput::make('quantity')
                        ->label('Jumlah Tambahan')
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->suffix('unit'),
                    \Filament\Forms\Components\Select::make('reason')
                        ->label('Alasan Penambahan')
                        ->options([
                            'purchase' => 'Pembelian dari Supplier',
                            'return' => 'Retur dari Customer',
                            'production' => 'Hasil Produksi',
                            'transfer_in' => 'Transfer Masuk',
                            'adjustment' => 'Koreksi Stok',
                            'other' => 'Lainnya',
                        ])
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Catatan')
                        ->placeholder('Contoh: Pembelian dari PT. ABC, Faktur No. INV-001')
                        ->maxLength(255),
                ])
                ->action(function (array $data) {
                    try {
                        $product = Product::findOrFail($data['product_id']);

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
                            $product,
                            (int) $data['quantity'],
                            'StockAdjustment',
                            null,
                            $fullNotes
                        );

                        Notification::make()
                            ->success()
                            ->title('Stok Masuk Berhasil')
                            ->body("Stok produk '{$product->name}' berhasil ditambahkan sebanyak {$data['quantity']} unit. Stok sekarang: {$product->fresh()->stock} unit")
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Gagal Menambah Stok')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            // Action untuk Stok Keluar
            Action::make('stokKeluar')
                ->label('Stok Keluar')
                ->icon('heroicon-o-minus-circle')
                ->color('warning')
                ->modalHeading('Pengurangan Stok Produk')
                ->modalDescription('Gunakan form ini untuk mengurangi stok produk (kerusakan, kehilangan, dll)')
                ->form([
                    \Filament\Forms\Components\Select::make('product_id')
                        ->label('Pilih Produk')
                        ->options(Product::where('stock', '>', 0)->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            if ($state) {
                                $product = Product::find($state);
                                if ($product) {
                                    $set('current_stock', $product->stock);
                                }
                            }
                        }),
                    \Filament\Forms\Components\Placeholder::make('current_stock_display')
                        ->label('Stok Saat Ini')
                        ->content(function ($get) {
                            $stock = $get('current_stock');
                            return $stock ? $stock . ' unit' : '-';
                        }),
                    \Filament\Forms\Components\Hidden::make('current_stock'),
                    \Filament\Forms\Components\TextInput::make('quantity')
                        ->label('Jumlah Pengurangan')
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->suffix('unit')
                        ->rules([
                            function ($get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $currentStock = $get('current_stock');
                                    if ($value > $currentStock) {
                                        $fail("Jumlah pengurangan tidak boleh melebihi stok saat ini ({$currentStock} unit).");
                                    }
                                };
                            },
                        ]),
                    \Filament\Forms\Components\Select::make('reason')
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
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Catatan')
                        ->placeholder('Contoh: Barang rusak saat pengiriman, Expired tanggal 01/12/2024')
                        ->maxLength(255),
                ])
                ->action(function (array $data) {
                    try {
                        $product = Product::findOrFail($data['product_id']);

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
                            $product,
                            (int) $data['quantity'],
                            'StockAdjustment',
                            null,
                            $fullNotes
                        );

                        Notification::make()
                            ->success()
                            ->title('Stok Keluar Berhasil')
                            ->body("Stok produk '{$product->name}' berhasil dikurangi sebanyak {$data['quantity']} unit. Stok sekarang: {$product->fresh()->stock} unit")
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Gagal Mengurangi Stok')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\ProductStatsOverview::class,
        ];
    }

    /**
     * Generate PDF laporan berdasarkan jenis laporan yang dipilih
     *
     * @param string $jenisLaporan
     * @param string $tanggalMulai
     * @param string $tanggalAkhir
     * @return mixed
     */
    protected function generatePdf(string $jenisLaporan, string $tanggalMulai, string $tanggalAkhir)
    {
        $tanggalMulai = Carbon::parse($tanggalMulai);
        $tanggalAkhir = Carbon::parse($tanggalAkhir);
        $data = [];
        $judul = '';

        switch ($jenisLaporan) {
            case 'stok_masuk':
                $data = $this->getDataStokMasuk($tanggalMulai, $tanggalAkhir);
                $judul = 'Laporan Stok Masuk';
                break;

            case 'stok_keluar':
                $data = $this->getDataStokKeluar($tanggalMulai, $tanggalAkhir);
                $judul = 'Laporan Stok Keluar';
                break;

            case 'stok_keseluruhan':
                $data = $this->getDataStokKeseluruhan();
                $judul = 'Laporan Stok Keseluruhan';
                break;
        }
        // Ambil data produk terlaris selama periode jika jenis laporan bukan stok keseluruhan
        $produkTerlaku = [];
        if ($jenisLaporan !== 'stok_keseluruhan') {
            $produkTerlaku = OrderItem::select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_terjual'))
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->whereBetween('orders.created_at', [$tanggalMulai, $tanggalAkhir])
                ->groupBy('order_items.product_id')
                ->orderByDesc('total_terjual')
                ->select([
                    'order_items.product_id',
                    'products.name as nama_produk',
                    'products.image as gambar',
                    DB::raw('SUM(order_items.quantity) as total_terjual')
                ])
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'nama_produk' => $item->nama_produk,
                        'gambar' => $item->gambar,
                        'total_terjual' => $item->total_terjual,
                    ];
                })
                ->toArray();
        }

        $pdfData = [
            'judul' => $judul,
            'tanggal_mulai' => $tanggalMulai->format('d-m-Y'),
            'tanggal_akhir' => $tanggalAkhir->format('d-m-Y'),
            'tanggal_cetak' => now()->format('d-m-Y H:i:s'),
            'data' => $data,
            'jenis_laporan' => $jenisLaporan,
            'produk_terlaris' => $produkTerlaku, // <-- ini ditambahkan
        ];

        // Generate PDF
        $pdf = PDF::loadView('pdf.laporan-stok', $pdfData)->setPaper('a4', 'landscape');

        // Buat nama file unik
        $filename = strtolower(str_replace(' ', '-', $judul)) . '-' . now()->format('YmdHis') . '.pdf';

        // Notifikasi sukses
        Notification::make()
            ->success()
            ->title('Laporan Berhasil Dibuat')
            ->body('File PDF laporan telah dibuat dan siap diunduh.')
            ->send();

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }

    /**
     * Ambil data stok masuk dalam rentang tanggal tertentu menggunakan tabel stock_movements
     *
     * @param Carbon $tanggalMulai
     * @param Carbon $tanggalAkhir
     * @return array
     */
    protected function getDataStokMasuk(Carbon $tanggalMulai, Carbon $tanggalAkhir): array
    {
        // Gunakan join untuk memastikan hanya data dengan produk yang valid
        $stokMasuk = StockMovement::where('stock_movements.type', 'in')
            ->whereBetween('stock_movements.created_at', [
                $tanggalMulai->startOfDay(),
                $tanggalAkhir->endOfDay()
            ])
            ->join('products', 'stock_movements.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select([
                'stock_movements.*',
                'products.name as product_name',
                'products.image as product_image',
                'categories.name as category_name'
            ])
            ->orderBy('stock_movements.created_at', 'desc')
            ->get()
            ->map(function ($movement) {
                return [
                    'tanggal' => Carbon::parse($movement->created_at)->format('d-m-Y H:i'),
                    'kode_produk' => $movement->product_id,
                    'nama_produk' => $movement->product_name,
                    'gambar' => $movement->product_image,
                    'kategori' => $movement->category_name ?? '-',
                    'jumlah' => $movement->quantity,
                    'stok_sebelum' => $movement->before_stock,
                    'stok_setelah' => $movement->after_stock,
                    'keterangan' => $movement->notes ?? ($movement->reference_type ? "Ref: {$movement->reference_type} #{$movement->reference_id}" : 'Penambahan stok'),
                ];
            })
            ->toArray();

        return $stokMasuk;
    }

    /**
     * Ambil data stok keluar dalam rentang tanggal tertentu menggunakan tabel stock_movements
     *
     * @param Carbon $tanggalMulai
     * @param Carbon $tanggalAkhir
     * @return array
     */
    protected function getDataStokKeluar(Carbon $tanggalMulai, Carbon $tanggalAkhir): array
    {
        // Gunakan join untuk memastikan hanya data dengan produk yang valid
        $stokKeluar = StockMovement::where('stock_movements.type', 'out')
            ->whereBetween('stock_movements.created_at', [
                $tanggalMulai->startOfDay(),
                $tanggalAkhir->endOfDay()
            ])
            ->join('products', 'stock_movements.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select([
                'stock_movements.*',
                'products.name as product_name',
                'products.image as product_image',
                'categories.name as category_name'
            ])
            ->orderBy('stock_movements.created_at', 'desc')
            ->get()
            ->map(function ($movement) {
                return [
                    'tanggal' => Carbon::parse($movement->created_at)->format('d-m-Y H:i'),
                    'kode_produk' => $movement->product_id,
                    'nama_produk' => $movement->product_name,
                    'gambar' => $movement->product_image,
                    'kategori' => $movement->category_name ?? '-',
                    'jumlah' => $movement->quantity,
                    'stok_sebelum' => $movement->before_stock,
                    'stok_setelah' => $movement->after_stock,
                    'keterangan' => $movement->notes ?? ($movement->reference_type ? "Ref: {$movement->reference_type} #{$movement->reference_id}" : 'Pengurangan stok'),
                ];
            })
            ->toArray();

        return $stokKeluar;
    }

    /**
     * Ambil data stok keseluruhan saat ini
     *
     * @return array
     */
    protected function getDataStokKeseluruhan(): array
    {
        $batasMinimum = ProductResource::$batasMinimumStok;

        $stokKeseluruhan = Product::with('category')
            ->get()
            ->map(function ($product) use ($batasMinimum) {
                // Hitung jumlah total stok masuk dan keluar
                $totalStokMasuk = StockMovement::where('product_id', $product->id)
                    ->where('type', 'in')
                    ->sum('quantity');

                $totalStokKeluar = StockMovement::where('product_id', $product->id)
                    ->where('type', 'out')
                    ->sum('quantity');

                return [
                    'kode_produk' => $product->id,
                    'nama_produk' => $product->name,
                    'gambar' => $product->image,
                    'kategori' => $product->category->name ?? '-',
                    'stok_saat_ini' => $product->stock,
                    'total_masuk' => $totalStokMasuk,
                    'total_keluar' => $totalStokKeluar,
                    'status' => $product->stock <= $batasMinimum ? 'Rendah' : 'Normal',
                ];
            })
            ->toArray();

        return $stokKeseluruhan;
    }
}
