<!-- resources/views/pdf/packing-slip.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Packing Slip - {{ $order->payment_code }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        h2 {
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0 5px 0;
        }

        .order-info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.info-table td {
            padding: 5px;
        }

        table.info-table td:first-child {
            font-weight: bold;
            width: 150px;
        }

        table.product-table {
            border: 1px solid #ddd;
        }

        table.product-table th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        table.product-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .barcode {
            text-align: center;
            margin: 20px 0;
        }

        .notes {
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            margin-top: 20px;
        }

        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            margin-bottom: 5px;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="header">
        <!-- <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo"> -->
        <h1>PACKING SLIP</h1>
        <p>Dokumen ini untuk keperluan pengemasan barang di gudang</p>
    </div>

    <div class="barcode">
        <!-- Placeholder for barcode, in production you'd generate a real barcode -->
        <h2>{{ $order->payment_code }}</h2>
    </div>

    <h2>Informasi Pesanan</h2>
    <table class="info-table">
        <tr>
            <td>Nomor Pesanan</td>
            <td>: {{ $order->payment_code }}</td>
        </tr>
        <tr>
            <td>Tanggal Pesanan</td>
            <td>: {{ \Carbon\Carbon::parse($order->order_created_at)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>: {{ $order->status }}</td>
        </tr>
        <tr>
            <td>Metode Pembayaran</td>
            <td>: {{ $order->payment_method ?? '-' }}</td>
        </tr>
    </table>

    <h2>Informasi Pelanggan</h2>
    <table class="info-table">
        <tr>
            <td>Nama</td>
            <td>: {{ $order->full_name }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>: {{ $order->email }}</td>
        </tr>
        <tr>
            <td>No. Telepon</td>
            <td>: {{ $order->phone }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: {{ $order->address }}, {{ $order->city->name ?? '' }}, {{ $order->province->name ?? '' }}
                {{ $order->postal_code }}</td>
        </tr>
    </table>

    <h2>Daftar Produk</h2>
    <table class="product-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 15%;">Gambar</th>
                <th style="width: 45%;">Produk</th>
                <th style="width: 15%;" class="text-center">Jumlah</th>
                <th style="width: 20%;" class="text-right">Harga Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-center">
                        @if ($item->product && $item->product->image)
                            <img src="{{ public_path('storage/' . $item->product->image) }}" class="product-image"
                                alt="{{ $item->product->name }}">
                        @else
                            <small>Tidak ada gambar</small>
                        @endif
                    </td>
                    <td>
                        {{ $item->product->name ?? 'Produk tidak ditemukan' }}
                        @if ($item->product && $item->product->variants)
                            <br><small>{{ $item->product->variants }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($order->notes)
        <div class="notes">
            <strong>Catatan:</strong>
            <p>{{ $order->notes }}</p>
        </div>
    @endif

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p>Petugas Gudang</p>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <p>Petugas Pengiriman</p>
        </div>
    </div>

    <div class="footer">
        <p>Dicetak pada {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }} | {{ config('app.name') }}</p>
    </div>
</body>

</html>
