<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Detail Pengiriman Produk Kustom</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 15px;
            color: #333;
        }

        .header {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #333;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .company-subtitle {
            font-size: 10pt;
            color: #555;
            margin: 0 0 0 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .orange-dot {
            width: 10px;
            height: 10px;
            background-color: #f9b234;
            border-radius: 50%;
            margin-left: 5px;
            display: inline-block;
        }

        .document-title {
            margin: 10px 0 5px 0;
            font-size: 14pt;
            font-weight: normal;
        }

        .document-date {
            margin: 0;
            font-size: 9pt;
            color: #777;
        }

        .orange-line {
            width: 50px;
            height: 3px;
            background-color: #f9b234;
            margin: 0 0 10px 0;
        }

        .content-wrapper {
            display: flex;
            flex-wrap: wrap;
        }

        .left-column {
            width: 58%;
            padding-right: 2%;
        }

        .right-column {
            width: 40%;
        }

        .product-image {
            text-align: center;
            margin-bottom: 15px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }

        .product-image h3 {
            margin: 0 0 10px 0;
            font-size: 12pt;
            font-weight: normal;
        }

        .product-image .orange-line {
            margin: 0 auto 10px auto;
        }

        .product-image img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 3px;
        }

        .product-label {
            font-size: 8pt;
            color: #777;
            margin-top: 8px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }

        table,
        th,
        td {
            border: 1px solid #eee;
        }

        th,
        td {
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f9f9f9;
            width: 35%;
            font-weight: normal;
        }

        .section {
            margin-bottom: 15px;
        }

        .section h3 {
            margin: 0 0 5px 0;
            font-size: 12pt;
            font-weight: normal;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 8pt;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .signature {
            text-align: right;
            margin-top: 20px;
            padding-right: 40px;
        }

        .signature-line {
            border-top: 1px solid #ddd;
            width: 150px;
            display: inline-block;
            margin-bottom: 5px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 9pt;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo-container">
            <div class="company-name">Azwaely</div>
            <div class="company-subtitle">PUTRA MEBEL</div>
            <div class="orange-dot"></div>
        </div>
        <div class="orange-line"></div>
        <h2 class="document-title">Detail Pengiriman Produk Kustom</h2>
        <p class="document-date">{{ date('d M Y') }}</p>
    </div>

    <div class="content-wrapper">
        <div class="left-column">
            <div class="section">
                <h3>Informasi Produk</h3>
                <div class="orange-line"></div>
                <table>
                    <tr>
                        <th>ID Permintaan</th>
                        <td>{{ $parent->id }}</td>
                    </tr>
                    <tr>
                        <th>Judul Produk</th>
                        <td>{{ $parent->title }}</td>
                    </tr>
                    <tr>
                        <th>Harga Produk</th>
                        <td>Rp {{ number_format($parent->quoted_price, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h3>Informasi Penerima</h3>
                <div class="orange-line"></div>
                <table>
                    <tr>
                        <th>Nama</th>
                        <td>{{ $shipment->full_name }}</td>
                    </tr>
                    <tr>
                        <th>Telepon</th>
                        <td>{{ $shipment->phone }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $shipment->email }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $shipment->address }}, {{ $shipment->city->name }}, {{ $shipment->province->name }},
                            {{ $shipment->postal_code }}</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h3>Informasi Pengiriman</h3>
                <div class="orange-line"></div>
                <table>
                    <tr>
                        <th>Nomor Resi</th>
                        <td>{{ $shipment->tracking_number ?? 'Belum ada' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ $shipment->status }}</td>
                    </tr>
                    <tr>
                        <th>Biaya Pengiriman</th>
                        <td>Rp {{ number_format($shipment->shipping_cost, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>Rp {{ number_format($shipment->total, 0, ',', '.') }}</td>
                    </tr>
                    @if ($shipment->notes)
                        <tr>
                            <th>Catatan</th>
                            <td>{{ $shipment->notes }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="right-column">
            @if ($finalProductImage)
                <div class="product-image">
                    <h3>Foto Produk Final</h3>
                    <div class="orange-line"></div>
                    <img src="{{ storage_path('app/public/' . $finalProductImage) }}" alt="Produk Final">
                    <div class="product-label">Produk Kustom by Azwaely Putra Mebel</div>
                </div>
            @endif

            <div class="signature">
                <p>Hormat Kami,</p>
                <div class="signature-line"></div>
                <div class="signature-name">Azwaely Putra Mebel</div>
                <div>Tim Pengiriman</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak pada {{ date('d M Y H:i') }} dan merupakan dokumen sah pengiriman produk kustom.</p>
        <p>&copy; {{ date('Y') }} Azwaely Putra Mebel. Seluruh hak cipta dilindungi undang-undang.</p>
    </div>
</body>

</html>
