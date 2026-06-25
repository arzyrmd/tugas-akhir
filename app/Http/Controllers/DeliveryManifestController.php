<?php

namespace App\Http\Controllers;

use App\Models\DeliveryBatch;
use App\Models\Order;
use App\Models\CustomProductRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DeliveryManifestController extends Controller
{
    public function generateManifest($batchId)
    {
        $batch = DeliveryBatch::with(['items.deliverable', 'area'])->findOrFail($batchId);

        // Kumpulkan semua item dengan alamat dan foto mereka
        $items = $batch->items->map(function ($item) {
            $deliverable = $item->deliverable;

            // Data dasar yang akan dikembalikan
            $data = [
                'id' => $item->id,
                'deliverable_id' => $deliverable->id,
                'type' => $item->deliverable_type === Order::class ? 'Pesanan Reguler' : 'Produk Kustom',
                'status' => $item->status,
                'product_images' => [] // Array untuk menyimpan gambar produk
            ];

            // Tambahkan data khusus berdasarkan jenis item
            if ($item->deliverable_type === Order::class) {
                $data['recipient_name'] = $deliverable->full_name;
                $data['contact'] = $deliverable->phone;
                $data['address'] = $deliverable->address;
                $data['city'] = $deliverable->city->name;
                $data['postal_code'] = $deliverable->postal_code;
                $data['total'] = $deliverable->total;
                $data['notes'] = $deliverable->notes;

                // Ambil item pesanan dan foto produk
                $data['items'] = $deliverable->orderItems->map(function ($orderItem) use (&$data) {
                    // Tambahkan foto produk ke array gambar
                    if ($orderItem->product->image) {
                        $data['product_images'][] = [
                            'name' => $orderItem->product->name,
                            'path' => public_path('storage/' . $orderItem->product->image),
                            'quantity' => $orderItem->quantity,
                        ];
                    }

                    return [
                        'product' => $orderItem->product->name,
                        'quantity' => $orderItem->quantity,
                    ];
                });
            } else {
                // Untuk produk kustom
                $shipment = $deliverable->shipment;
                $data['recipient_name'] = $shipment ? $shipment->full_name : 'Tidak ada data penerima';
                $data['contact'] = $shipment ? $shipment->phone : '-';
                $data['address'] = $shipment ? $shipment->address : 'Tidak ada alamat';
                $data['city'] = $shipment && $shipment->city ? $shipment->city->name : '-';
                $data['postal_code'] = $shipment ? $shipment->postal_code : '-';
                $data['total'] = $shipment ? $shipment->total : $deliverable->quoted_price;
                $data['notes'] = $shipment ? $shipment->notes : '';

                // Untuk produk kustom, ambil foto dari finalProduct (prioritas utama)
                $finalProduct = $deliverable->finalProduct;
                if ($finalProduct && $finalProduct->image_path) {
                    $data['product_images'][] = [
                        'name' => $deliverable->title,
                        'path' => public_path('storage/' . $finalProduct->image_path),
                        'quantity' => 1,
                    ];
                } else {
                    // Fallback: ambil foto terakhir dari progresses jika tidak ada final product
                    $latestProgress = $deliverable->progresses()->latest()->first();
                    if ($latestProgress && $latestProgress->image_path) {
                        $data['product_images'][] = [
                            'name' => $deliverable->title,
                            'path' => public_path('storage/' . $latestProgress->image_path),
                            'quantity' => 1,
                        ];
                    }
                }

                // Untuk produk kustom, kita hanya punya satu item
                $data['items'] = [
                    [
                        'product' => $deliverable->title,
                        'quantity' => 1,
                    ]
                ];
            }

            return $data;
        });

        // Kelompokkan item berdasarkan kota untuk efisiensi rute
        $itemsByCity = $items->groupBy('city');

        // Flatten kembali untuk manifest akhir dengan urutan berdasarkan kota
        $orderedItems = collect();
        foreach ($itemsByCity as $city => $cityItems) {
            foreach ($cityItems as $item) {
                $orderedItems->push($item);
            }
        }

        // Generate PDF
        $pdf = PDF::loadView('pdf.delivery-manifest', [
            'batch' => $batch,
            'items' => $orderedItems,
            'date' => now()->format('d M Y'),
        ]);

        // Format nama file yang informatif
        // Bersihkan nama driver dari karakter yang tidak valid untuk nama file
        $driverName = $batch->driver_name
            ? preg_replace('/[^a-zA-Z0-9-_]/', '', str_replace(' ', '-', strtolower($batch->driver_name)))
            : 'tidak-ada-driver';

        // Format tanggal pengiriman
        $deliveryDate = $batch->scheduled_date->format('Y-m-d');

        // Nama area pengiriman
        $areaName = str_replace(' ', '-', strtolower($batch->area->name));

        // Format nama file final
        $fileName = "manifest-{$driverName}-{$areaName}-{$deliveryDate}.pdf";

        return $pdf->download($fileName);
    }
}
