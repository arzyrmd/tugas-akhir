{{-- resources/views/pdfs/warehouse-order.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pesanan Produk Kustom - Gudang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin: 0 0 10px 0;
            font-weight: bold;
        }

        .header .subtitle {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
        }

        .payment-code {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }

        .payment-code .label {
            font-weight: bold;
            color: #92400e;
            font-size: 14px;
        }

        .payment-code .code {
            font-size: 18px;
            font-weight: bold;
            color: #92400e;
            margin-top: 5px;
        }

        .section {
            margin: 25px 0;
            page-break-inside: avoid;
        }

        .section-title {
            background: #2563eb;
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .info-grid td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .info-label {
            width: 180px;
            background: #f8fafc;
            font-weight: bold;
        }

        .description-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            white-space: pre-wrap;
        }

        .price-highlight {
            background: #dcfce7;
            color: #166534;
            font-weight: bold;
            padding: 5px;
            border-radius: 3px;
        }

        .references-section {
            margin-top: 15px;
        }

        .reference-item {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            page-break-inside: avoid;
            margin-bottom: 15px;
            display: inline-block;
            width: 45%;
            margin-right: 2%;
            vertical-align: top;
        }

        .reference-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            display: block;
        }

        .reference-caption {
            padding: 10px;
            background: #f8fafc;
            font-size: 11px;
            color: #374151;
        }

        .priority-box {
            background: #fef2f2;
            border: 2px solid #dc2626;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .priority-box .title {
            color: #dc2626;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #6b7280;
            font-size: 11px;
        }

        .signature-section {
            margin-top: 50px;
            width: 100%;
        }

        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            padding: 20px;
            margin-right: 5%;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
            font-size: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            color: white;
            background: #16a34a;
        }

        @media print {
            body {
                margin: 0;
                padding: 15px;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>PESANAN PRODUK KUSTOM</h1>
        <div class="subtitle">Dokumen untuk Gudang & Produksi</div>
        <div class="subtitle">ID Pesanan: #{{ $record->id }}</div>
        <div class="subtitle">Dicetak: {{ $generatedAt }}</div>
    </div>

    <!-- Kode Pembayaran DP -->
    <div class="payment-code">
        <div class="label">KODE PEMBAYARAN DP MIDTRANS</div>
        <div class="code">{{ $record->dp_payment_code }}</div>
        <div style="margin-top: 10px; font-size: 12px; color: #92400e;">
            Dibayar: {{ \Carbon\Carbon::parse($record->dp_payment_date)->format('d F Y H:i') }}
        </div>
    </div>

    <!-- Status Priority -->
    <div class="priority-box">
        <div class="title">🚨 PRIORITAS PENGERJAAN</div>
        <div>
            Status: <span class="status-badge">{{ $record->status }}</span><br>
            Estimasi Selesai:
            <strong>{{ \Carbon\Carbon::parse($record->estimated_completion)->format('d F Y') }}</strong><br>
            Sisa Waktu: <strong>{{ \Carbon\Carbon::parse($record->estimated_completion)->diffForHumans() }}</strong>
        </div>
    </div>

    <!-- Informasi Pesanan -->
    <div class="section">
        <div class="section-title">📋 INFORMASI PESANAN</div>
        <table class="info-grid">
            <tr>
                <td class="info-label">Judul Pesanan</td>
                <td><strong>{{ $record->title }}</strong></td>
            </tr>
            <tr>
                <td class="info-label">Pelanggan</td>
                <td>{{ $record->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Email Pelanggan</td>
                <td>{{ $record->user->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Tanggal Pesanan</td>
                <td>{{ \Carbon\Carbon::parse($record->created_at)->format('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td class="info-label">Deadline Harapan</td>
                <td>{{ $record->desired_deadline ? \Carbon\Carbon::parse($record->desired_deadline)->format('d F Y') : 'Tidak ditentukan' }}
                </td>
            </tr>
            <tr>
                <td class="info-label">Budget Pelanggan</td>
                <td>{{ $record->budget ? 'Rp ' . number_format($record->budget, 0, ',', '.') : 'Tidak ditentukan' }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Deskripsi Produk -->
    <div class="section">
        <div class="section-title">📝 DESKRIPSI PRODUK</div>
        <div class="description-box">{{ $record->description ?? 'Tidak ada deskripsi' }}</div>
    </div>

    <!-- Spesifikasi Detail -->
    @if ($record->specifications)
        <div class="section">
            <div class="section-title">🔧 SPESIFIKASI DETAIL</div>
            <div class="description-box">{{ $record->specifications }}</div>
        </div>
    @endif

    <!-- Informasi Harga -->
    <div class="section">
        <div class="section-title">💰 INFORMASI HARGA</div>
        <table class="info-grid">
            <tr>
                <td class="info-label">Harga Total</td>
                <td><span class="price-highlight">Rp {{ number_format($record->quoted_price, 0, ',', '.') }}</span>
                </td>
            </tr>
            <tr>
                <td class="info-label">DP (30%)</td>
                <td><span class="price-highlight">Rp {{ number_format($record->down_payment, 0, ',', '.') }}</span>
                </td>
            </tr>
            <tr>
                <td class="info-label">Sisa Pembayaran</td>
                <td>Rp {{ number_format($record->quoted_price - $record->down_payment, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="info-label">Status DP</td>
                <td><strong style="color: #16a34a;">✅ SUDAH DIBAYAR</strong></td>
            </tr>
        </table>
    </div>

    <!-- Catatan Admin -->
    @if ($record->admin_notes)
        <div class="section">
            <div class="section-title">📌 CATATAN ADMIN</div>
            <div class="description-box">{{ $record->admin_notes }}</div>
        </div>
    @endif

    <!-- Foto Referensi -->
    @if ($references && $references->count() > 0)
        <div class="section page-break">
            <div class="section-title">📸 FOTO REFERENSI</div>
            <div class="references-section">
                @foreach ($references as $reference)
                    <div class="reference-item">
                        @if ($reference->image_path)
                            <img src="{{ public_path('storage/' . $reference->image_path) }}"
                                alt="Referensi {{ $loop->iteration }}" class="reference-image">
                        @endif
                        @if ($reference->description)
                            <div class="reference-caption">
                                <strong>Referensi {{ $loop->iteration }}:</strong><br>
                                {{ $reference->description }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Checklist Pengerjaan -->
    <div class="section">
        <div class="section-title">✅ CHECKLIST PENGERJAAN</div>
        <div style="padding: 15px; border: 1px solid #e2e8f0; border-radius: 5px;">
            <div style="margin-bottom: 10px;">
                <input type="checkbox"> Bahan sudah disiapkan<br>
                <input type="checkbox"> Desain sudah dikonfirmasi<br>
                <input type="checkbox"> Proses produksi dimulai<br>
                <input type="checkbox"> Quality control passed<br>
                <input type="checkbox"> Packaging selesai<br>
                <input type="checkbox"> Siap untuk pengiriman
            </div>
        </div>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <div class="signature-box">
            <strong>Admin</strong>
            <div class="signature-line">
                Nama & Tanda Tangan
            </div>
        </div>
        <div class="signature-box">
            <strong>Supervisor Gudang</strong>
            <div class="signature-line">
                Nama & Tanda Tangan
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Dokumen ini digenerate otomatis dari sistem pada {{ $generatedAt }}</div>
        <div>Mohon simpan dokumen ini sebagai referensi pengerjaan produk kustom</div>
        <div style="margin-top: 10px; font-weight: bold;">⚠️ DOKUMEN RAHASIA - HANYA UNTUK INTERNAL PERUSAHAAN</div>
    </div>
</body>

</html>
