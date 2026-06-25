{{-- resources/views/invoices/custom-dp.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice DP - {{ $customRequest->dp_payment_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #1a202c;
            background: #f5f5f5;
            min-height: 100vh;
            padding: 20px;
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        .header {
            background: #8B4513;
            color: white;
            padding: 30px 40px;
            position: relative;
        }

        .header-pattern {
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0 0 0 100px;
        }

        .header-content {
            display: table;
            width: 100%;
            position: relative;
            z-index: 2;
        }

        .company-info {
            display: table-cell;
            vertical-align: top;
            width: 60%;
        }

        .company-info h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .company-info .tagline {
            font-size: 14px;
            font-style: italic;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .company-info p {
            margin: 4px 0;
            font-size: 12px;
        }

        .contact-info {
            margin-top: 12px;
        }

        .contact-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 5px;
        }

        .invoice-info {
            display: table-cell;
            vertical-align: top;
            width: 40%;
            text-align: right;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .invoice-meta {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .invoice-meta strong {
            font-weight: bold;
        }

        .content-area {
            padding: 40px;
        }

        .section {
            margin: 30px 0;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #8B4513;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #D2691E;
            position: relative;
        }

        .section-title-accent {
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 50px;
            height: 2px;
            background: #CD853F;
        }

        .customer-shipping {
            width: 100%;
            margin-bottom: 30px;
        }

        .customer-info,
        .shipping-info {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            margin-right: 2%;
        }

        .shipping-info {
            margin-right: 0;
        }

        .info-box {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid #D2691E;
            border: 1px solid #e2e8f0;
        }

        .info-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #1a202c;
            font-size: 14px;
        }

        .info-box p {
            margin: 6px 0;
            color: #4a5568;
            font-size: 12px;
        }

        .payment-details {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
        }

        .payment-details th {
            padding: 16px 12px;
            text-align: left;
            background: #8B4513;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #6d3510;
        }

        .payment-details td {
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .payment-details tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .payment-details .amount {
            text-align: right;
            font-weight: bold;
            color: #2d3748;
        }

        .total-row {
            background: #8B4513 !important;
            color: white !important;
            font-weight: bold !important;
        }

        .total-row td {
            border-bottom: 1px solid #6d3510;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-success {
            background: #48bb78;
            color: white;
        }

        .status-approved {
            background: #4299e1;
            color: white;
        }

        .notes {
            background: #fef5e7;
            padding: 25px;
            border-radius: 8px;
            border-left: 5px solid #f6ad55;
            margin: 30px 0;
            border: 1px solid #fed7aa;
        }

        .notes-title {
            font-weight: bold;
            color: #c05621;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .notes ul {
            margin: 0;
            padding-left: 20px;
        }

        .notes li {
            margin: 6px 0;
            color: #744210;
            font-size: 12px;
        }

        .footer {
            background: #2d3748;
            color: white;
            padding: 25px 40px;
            text-align: center;
            margin-top: 40px;
        }

        .footer p {
            margin: 6px 0;
            font-size: 12px;
        }

        .footer hr {
            margin: 15px 0;
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }

        .watermark {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(139, 69, 19, 0.05);
            font-weight: 900;
            z-index: 1;
            pointer-events: none;
        }

        .product-detail-box {
            background: #fff5f5;
            padding: 25px;
            border-radius: 8px;
            border-left: 5px solid #e53e3e;
            border: 1px solid #fecaca;
        }

        .highlight-box {
            background: #e6fffa;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #4fd1c7;
            text-align: center;
            margin: 20px 0;
        }

        .highlight-box .amount {
            font-size: 20px;
            font-weight: bold;
            color: #234e52;
        }

        .payment-success {
            background: #c6f6d5 !important;
            color: #22543d !important;
            font-weight: bold !important;
        }

        .payment-success td {
            border-bottom: 1px solid #9ae6b4;
        }

        /* PDF Specific Styles */
        @media print {
            body {
                background: white;
                padding: 0;
                font-size: 12px;
            }

            .invoice-container {
                box-shadow: none;
                border-radius: 0;
                max-width: none;
            }

            .header {
                background: #8B4513 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .payment-details th {
                background: #8B4513 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .total-row {
                background: #8B4513 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .payment-success {
                background: #c6f6d5 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .status-success {
                background: #48bb78 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .status-approved {
                background: #4299e1 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .footer {
                background: #2d3748 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .notes {
                background: #fef5e7 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .info-box {
                background: #f8f9ff !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .product-detail-box {
                background: #fff5f5 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .watermark {
                font-size: 80px;
                opacity: 0.1;
            }

            .page-break {
                page-break-before: always;
            }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {

            .customer-info,
            .shipping-info {
                width: 100%;
                display: block;
                margin-bottom: 20px;
            }

            .header-content {
                display: block;
            }

            .company-info,
            .invoice-info {
                display: block;
                width: 100%;
                text-align: left;
            }

            .invoice-info {
                margin-top: 20px;
                text-align: left;
            }

            .content-area {
                padding: 20px;
            }

            .contact-item {
                display: block;
                margin-bottom: 5px;
            }

            .payment-details {
                font-size: 11px;
            }

            .payment-details th,
            .payment-details td {
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="watermark">DP LUNAS</div>

    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-pattern"></div>
            <div class="header-content">
                <div class="company-info">
                    <h1>AZWAELY PUTRA MEBEL</h1>
                    <div class="tagline">Furniture Berkualitas & Terpercaya</div>
                    <p>Jl. Raya Karang Anyar No.69, Pekauman Kulon</p>
                    <p>Kec. Dukuhturi, Kabupaten Tegal, Jawa Tengah 52192</p>
                    <p>444P+H2 Pekauman Kulon, Tegal Regency, Central Java</p>
                    <div class="contact-info">
                        <div class="contact-item">
                            <span>azwaely-putra@gmail.com</span>
                        </div>
                        <div class="contact-item">
                            <span>085712424969</span>
                        </div>
                    </div>
                </div>
                <div class="invoice-info">
                    <div class="invoice-title">INVOICE DP</div>
                    <div class="invoice-meta">
                        <strong>No. Invoice:</strong> {{ $customRequest->dp_payment_code }}<br>
                        <strong>Tanggal:</strong> {{ $customRequest->dp_payment_date->format('d M Y') }}<br>
                        <strong>Jatuh Tempo:</strong> {{ $customRequest->dp_payment_date->format('d M Y') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <!-- Customer Info -->
            <div class="customer-shipping">
                <div class="customer-info">
                    <div class="section-title">
                        Informasi Pelanggan
                        <div class="section-title-accent"></div>
                    </div>
                    <div class="info-box">
                        <div class="info-title">{{ $customRequest->user->name }}</div>
                        <p>Email: {{ $customRequest->user->email }}</p>
                        @if ($customRequest->user->phone)
                            <p>Telp: {{ $customRequest->user->phone }}</p>
                        @endif
                        <p><strong>ID Permintaan:</strong> #{{ $customRequest->id }}</p>
                    </div>
                </div>

                <div class="shipping-info">
                    <div class="section-title">
                        Status Pembayaran
                        <div class="section-title-accent"></div>
                    </div>
                    <div class="info-box">
                        <div class="info-title" style="color: #22543d;">✓ DOWN PAYMENT LUNAS</div>
                        <p><strong>Tanggal Pembayaran:</strong>
                            {{ $customRequest->dp_payment_date->format('d M Y, H:i') }} WIB</p>
                        <p><strong>Metode:</strong> Transfer Bank</p>
                        <p><strong>Status:</strong> <span class="status-badge status-success">Berhasil</span></p>
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="section">
                <div class="section-title">
                    Detail Produk Kustom
                    <div class="section-title-accent"></div>
                </div>
                <div class="product-detail-box">
                    <div class="info-title">{{ $customRequest->title }}</div>
                    @if ($customRequest->description)
                        <p><strong>Deskripsi:</strong> {{ $customRequest->description }}</p>
                    @endif
                    <p><strong>Status:</strong> <span class="status-badge status-approved">Dalam Proses</span></p>
                    <p><strong>Tanggal Permintaan:</strong> {{ $customRequest->created_at->format('d M Y') }}</p>
                    @if ($customRequest->approved_at)
                        <p><strong>Tanggal Persetujuan:</strong> {{ $customRequest->approved_at->format('d M Y') }}</p>
                    @endif
                    <p><strong>Estimasi Selesai:</strong>
                        {{ \Carbon\Carbon::parse($customRequest->estimated_completion)->format('d M Y') }}</p>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="section">
                <div class="section-title">
                    Rincian Pembayaran Down Payment
                    <div class="section-title-accent"></div>
                </div>
                <table class="payment-details">
                    <thead>
                        <tr>
                            <th>Deskripsi</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Down Payment - {{ $customRequest->title }}</strong><br>
                                <small>{{ $customRequest->description }}</small><br>
                                <small style="color: #8B4513; font-weight: 600;">
                                    <em>Total Nilai Project: Rp
                                        {{ number_format($customRequest->quoted_price, 0, ',', '.') }}</em>
                                </small>
                            </td>
                            <td>{{ $customRequest->dp_payment_date->format('d M Y') }}</td>
                            <td><span class="status-badge status-success">Lunas</span></td>
                            <td class="amount">Rp {{ number_format($customRequest->down_payment, 0, ',', '.') }}</td>
                        </tr>

                        <tr class="total-row">
                            <td colspan="3"><strong>💰 TOTAL PEMBAYARAN DP</strong></td>
                            <td class="amount"><strong>Rp
                                    {{ number_format($customRequest->down_payment, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Payment Summary -->
            <div class="section">
                <div class="section-title">
                    Ringkasan Pembayaran Project
                    <div class="section-title-accent"></div>
                </div>
                <table class="payment-details">
                    <tbody>
                        <tr>
                            <td><strong>Total Harga Produk</strong></td>
                            <td class="amount">Rp {{ number_format($customRequest->quoted_price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Down Payment (Sudah Dibayar)</strong></td>
                            <td class="amount" style="color: #22543d;">Rp
                                {{ number_format($customRequest->down_payment, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="total-row">
                            <td><strong>SISA PEMBAYARAN</strong></td>
                            <td class="amount"><strong>Rp
                                    {{ number_format($customRequest->remaining_payment, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr class="payment-success">
                            <td><strong>STATUS DP</strong></td>
                            <td class="amount"><strong>LUNAS</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Notes -->
            <div class="notes">
                <div class="notes-title">📋 Informasi Penting:</div>
                <ul>
                    <li>Invoice ini merupakan bukti sah pembayaran down payment produk kustom</li>
                    <li>Produk akan mulai dikerjakan setelah pembayaran DP dikonfirmasi</li>
                    <li>Estimasi penyelesaian:
                        {{ \Carbon\Carbon::parse($customRequest->estimated_completion)->format('d M Y') }}</li>
                    <li>Sisa pembayaran akan ditagihkan setelah produk selesai dan siap diserahkan</li>
                    <li>Untuk pertanyaan lebih lanjut, hubungi customer service kami di 085712424969</li>
                    <li>Terima kasih telah mempercayai Azwaely Putra Mebel untuk kebutuhan furniture Anda</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Terima kasih atas kepercayaan Anda kepada Azwaely Putra Mebel!</strong></p>
            <p>Invoice ini dibuat secara otomatis dan sah tanpa tanda tangan.</p>
            <p>Dicetak pada: {{ now()->format('d M Y H:i') }} WIB</p>
            <hr>
            <p>© {{ now()->year }} Azwaely Putra Mebel. Semua hak dilindungi. | Furniture Berkualitas Sejak Lama</p>
        </div>
    </div>
</body>

</html>
