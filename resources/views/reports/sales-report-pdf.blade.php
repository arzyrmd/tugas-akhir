<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2563eb;
        }

        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-left,
        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .info-box h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #2563eb;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .statistics {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            text-align: center;
            background-color: #f1f5f9;
            padding: 15px;
            margin-right: 10px;
            border-radius: 5px;
        }

        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            display: block;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .table-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            color: white;
        }

        .badge-warning {
            background-color: #f59e0b;
        }

        .badge-primary {
            background-color: #2563eb;
        }

        .badge-success {
            background-color: #10b981;
        }

        .badge-danger {
            background-color: #ef4444;
        }

        .badge-info {
            background-color: #06b6d4;
        }

        .badge-purple {
            background-color: #8b5cf6;
        }

        .badge-gray {
            background-color: #6b7280;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .product-info {
            max-width: 150px;
            word-wrap: break-word;
            font-size: 9px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <h2>Periode: {{ \Carbon\Carbon::parse($statistics['period']['start'])->format('d/m/Y') }} -
            {{ \Carbon\Carbon::parse($statistics['period']['end'])->format('d/m/Y') }}</h2>
    </div>

    <div class="info-section">
        <div class="info-left">
            <div class="info-box">
                <h3>Informasi Laporan</h3>
                <div class="info-row">
                    <span class="info-label">Tanggal Cetak:</span>
                    {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
                </div>
                <div class="info-row">
                    <span class="info-label">Periode:</span>
                    {{ \Carbon\Carbon::parse($statistics['period']['start'])->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($statistics['period']['end'])->format('d/m/Y') }}
                </div>
                <div class="info-row">
                    <span class="info-label">Tipe Pesanan:</span>
                    @if (isset($filters['order_type']) && $filters['order_type'] !== 'ALL')
                        {{ $filters['order_type'] == 'REGULER' ? 'Pesanan Reguler' : 'Produk Kustom' }}
                    @else
                        Semua Tipe
                    @endif
                </div>
                <div class="info-row">
                    <span class="info-label">Status Pembayaran:</span>
                    @if (isset($filters['payment_status']) && $filters['payment_status'] !== 'ALL')
                        {{ $filters['payment_status'] }}
                    @else
                        Semua Status
                    @endif
                </div>
            </div>
        </div>

        <div class="info-right">
            <div class="info-box">
                <h3>Ringkasan Statistik</h3>
                <div class="info-row">
                    <span class="info-label">Total Pesanan:</span>
                    {{ number_format($statistics['total_orders'], 0, ',', '.') }}
                </div>
                <div class="info-row">
                    <span class="info-label">Total Pendapatan:</span>
                    Rp {{ number_format($statistics['total_revenue'], 0, ',', '.') }}
                </div>
                <div class="info-row">
                    <span class="info-label">Pesanan Reguler:</span>
                    {{ $statistics['orders_by_type']['REGULER'] ?? 0 }}
                </div>
                <div class="info-row">
                    <span class="info-label">Produk Kustom:</span>
                    {{ $statistics['orders_by_type']['CUSTOM'] ?? 0 }}
                </div>
            </div>
        </div>
    </div>

    <div class="statistics">
        <div class="stat-box">
            <span class="stat-number">{{ number_format($statistics['total_orders'], 0, ',', '.') }}</span>
            <div class="stat-label">Total Pesanan</div>
        </div>
        <div class="stat-box">
            <span class="stat-number">Rp {{ number_format($statistics['total_revenue'], 0, ',', '.') }}</span>
            <div class="stat-label">Total Pendapatan</div>
        </div>
        <div class="stat-box">
            <span class="stat-number">{{ $statistics['orders_by_status']['LUNAS'] ?? 0 }}</span>
            <div class="stat-label">Pesanan Lunas</div>
        </div>
        <div class="stat-box">
            <span class="stat-number">{{ $statistics['orders_by_status']['BELUM DIBAYAR'] ?? 0 }}</span>
            <div class="stat-label">Belum Dibayar</div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="8%">Periode</th>
                    <th width="6%">Tipe</th>
                    <th width="10%">No. Pesanan</th>
                    <th width="12%">Pelanggan</th>
                    <th width="15%">Produk</th>
                    <th width="8%">Status</th>
                    <th width="10%">Total</th>
                    <th width="8%">Pembayaran</th>
                    <th width="10%">Tgl Pesanan</th>
                    <th width="10%">Tgl Bayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $record)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $record->periode }}</td>
                        <td class="text-center">
                            <span
                                class="badge {{ $record->order_type === 'CUSTOM' ? 'badge-warning' : 'badge-primary' }}">
                                {{ $record->order_type }}
                            </span>
                        </td>
                        <td>{{ $record->order_number }}</td>
                        <td>{{ $record->customer_name }}</td>
                        <td class="product-info">
                            @if ($record->order_type === 'CUSTOM')
                                {{ Str::limit($record->description ?? 'Tidak ada deskripsi', 50) }}
                            @else
                                {{ Str::limit($record->product_info, 50) }}
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $status =
                                    $record->order_type === 'CUSTOM'
                                        ? match ($record->status) {
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
                                            default => $record->status,
                                        }
                                        : $record->status;

                                $badgeClass = match ($status) {
                                    'MENUNGGU PEMBAYARAN',
                                    'MENUNGGU REVIEW',
                                    'MENUNGGU DP',
                                    'MENUNGGU PELUNASAN'
                                        => 'badge-warning',
                                    'PEMBAYARAN BERHASIL', 'SELESAI' => 'badge-success',
                                    'DIKEMAS', 'DALAM PENGERJAAN' => 'badge-info',
                                    'SIAP DIKIRIM', 'PENAWARAN DIBERIKAN' => 'badge-primary',
                                    'DIKIRIM' => 'badge-purple',
                                    'DIBATALKAN', 'PENAWARAN DITOLAK' => 'badge-danger',
                                    default => 'badge-gray',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                        </td>
                        <td class="text-right">Rp {{ number_format($record->total_amount, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span
                                class="badge {{ $record->payment_status === 'LUNAS' ? 'badge-success' : ($record->payment_status === 'DP DIBAYAR' ? 'badge-warning' : 'badge-danger') }}">
                                {{ $record->payment_status }}
                            </span>
                        </td>
                        <td class="text-center">
                            {{ $record->order_created_at ? $record->order_created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="text-center">
                            {{ $record->payment_date ? $record->payment_date->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>Total {{ $statistics['total_orders'] }} pesanan dengan nilai Rp
            {{ number_format($statistics['total_revenue'], 0, ',', '.') }}</p>
    </div>
</body>

</html>
