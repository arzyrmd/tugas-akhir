<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Manifest Pengiriman #{{ $batch->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .batch-info {
            margin-bottom: 20px;
        }

        .batch-info table {
            width: 100%;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .items-table th {
            background-color: #f2f2f2;
        }

        .signature-box {
            border: 1px solid #ddd;
            height: 70px;
            margin-top: 5px;
        }

        .page-break {
            page-break-before: always;
        }

        .item-products {
            font-size: 10px;
        }

        .product-images {
            display: flex;
            flex-wrap: wrap;
            margin-top: 5px;
        }

        .product-image-container {
            margin-right: 5px;
            margin-bottom: 5px;
            border: 1px solid #eee;
            padding: 2px;
            width: 80px;
        }

        .product-image {
            width: 80px;
            height: auto;
            max-height: 80px;
            object-fit: contain;
        }

        .product-image-caption {
            font-size: 8px;
            text-align: center;
            margin-top: 2px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>MANIFEST PENGIRIMAN</h1>
        <h2>Batch #{{ $batch->id }}</h2>
    </div>

    <div class="batch-info">
        <table>
            <tr>
                <td width="150"><strong>Tanggal Pengiriman</strong></td>
                <td>: {{ $batch->scheduled_date->format('d M Y') }}</td>
                <td width="150"><strong>Area Pengiriman</strong></td>
                <td>: {{ $batch->area->name }}</td>
            </tr>
            <tr>
                <td><strong>Pengirim/Driver</strong></td>
                <td>: {{ $batch->driver_name ?: '-' }}</td>
                <td><strong>Total Pesanan</strong></td>
                <td>: {{ $items->count() }} pesanan</td>
            </tr>
            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>: {{ $date }}</td>
                <td><strong>Status Batch</strong></td>
                <td>: {{ $batch->status }}</td>
            </tr>
        </table>
    </div>

    <h3>Daftar Pesanan</h3>

    @foreach ($items as $index => $item)
        <div style="border: 1px solid #ddd; margin-bottom: 15px; padding: 10px; page-break-inside: avoid;">
            <table width="100%" style="border-collapse: collapse;">
                <tr>
                    <td width="60%" style="vertical-align: top;">
                        <div style="margin-bottom: 10px;">
                            <strong>No. {{ $index + 1 }} - ID #{{ $item['deliverable_id'] }}
                                ({{ $item['type'] }})</strong>
                        </div>

                        <table width="100%" style="border-collapse: collapse;">
                            <tr>
                                <td width="120"><strong>Penerima</strong></td>
                                <td>: {{ $item['recipient_name'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kontak</strong></td>
                                <td>: {{ $item['contact'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td>: {{ $item['address'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kota/Kode Pos</strong></td>
                                <td>: {{ $item['city'] }}, {{ $item['postal_code'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td>: Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                            </tr>
                            @if ($item['notes'])
                                <tr>
                                    <td><strong>Catatan</strong></td>
                                    <td>: {{ $item['notes'] }}</td>
                                </tr>
                            @endif
                        </table>

                        <div style="margin-top: 10px;">
                            <strong>Produk:</strong>
                            <ul style="margin-top: 5px; padding-left: 20px;">
                                @foreach ($item['items'] as $product)
                                    <li>{{ $product['product'] }} ({{ $product['quantity'] }})</li>
                                @endforeach
                            </ul>
                        </div>
                    </td>
                    <td width="40%" style="vertical-align: top; padding-left: 10px;">
                        <div style="margin-bottom: 10px;">
                            <strong>Foto Produk:</strong>
                        </div>

                        <div class="product-images">
                            @if (count($item['product_images']) > 0)
                                @foreach ($item['product_images'] as $image)
                                    <div class="product-image-container">
                                        <img src="{{ $image['path'] }}" class="product-image">
                                        <div class="product-image-caption">
                                            {{ $image['name'] }} ({{ $image['quantity'] }})
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div style="font-style: italic; color: #666;">Tidak ada foto produk</div>
                            @endif
                        </div>

                        <div style="margin-top: 15px;">
                            <strong>Tanda Tangan Penerima:</strong>
                            <div class="signature-box"></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach

    <div style="margin-top: 30px;">
        <table width="100%">
            <tr>
                <td width="50%" style="text-align: center;">
                    <p>Disiapkan oleh,</p>
                    <br><br><br>
                    <p>____________________</p>
                    <p>Admin</p>
                </td>
                <td width="50%" style="text-align: center;">
                    <p>Diterima oleh,</p>
                    <br><br><br>
                    <p>____________________</p>
                    <p>{{ $batch->driver_name ?: 'Driver/Pengirim' }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <h3>Checklist Pengiriman Batch #{{ $batch->id }}</h3>
    <p>Tanggal Pengiriman: {{ $batch->scheduled_date->format('d M Y') }}</p>

    <table class="items-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>ID Pesanan</th>
                <th>Penerima</th>
                <th>Status</th>
                <th>Waktu Pengiriman</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>#{{ $item['deliverable_id'] }} ({{ $item['type'] }})</td>
                    <td>{{ $item['recipient_name'] }}</td>
                    <td style="padding: 10px;">
                        □ Terkirim<br>
                        □ Gagal<br>
                        □ Reschedule
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
