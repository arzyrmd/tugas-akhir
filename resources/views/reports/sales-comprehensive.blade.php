<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Komprehensif</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .summary-section {
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #ddd;
        }

        .summary-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .summary-grid {
            width: 100%;
        }

        .summary-row {
            margin-bottom: 8px;
            overflow: hidden;
        }

        .summary-label {
            float: left;
            font-weight: bold;
            width: 45%;
            color: #333;
        }

        .summary-value {
            float: right;
            width: 50%;
            text-align: right;
            color: #666;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin: 30px 0 15px 0;
            color: #333;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        .orders-table th {
            background-color: #f1f3f4;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 10px;
        }

        .orders-table td {
            padding: 6px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .orders-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-success {
            background-color: #d4edda;
            color: #155724;
        }

        .status-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .type-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .type-reguler {
            background-color: #cce5ff;
            color: #004085;
        }

        .type-custom {
            background-color: #ffe4b3;
            color: #8a4800;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN PENJUALAN KOMPREHENSIF</h1>
        <p><strong>Periode:</strong> {{ $summary['period'] }}</p>
        <p><strong>Dibuat pada:</strong> {{ $generated_at->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title">📊 RINGKASAN EKSEKUTIF</div>
        <div class="summary-grid">
            <div class="summary-row clearfix">
                <div class="summary-label">Total Pesanan:</div>
                <div class="summary-value">{{ number_format($summary['total_orders']) }} pesanan</div>
            </div>
            <div class="summary-row clearfix">
                <div class="summary-label">Total Pendapatan:</div>
                <div class="summary-value">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-row clearfix">
                <div class="summary-label">Rata-rata Nilai Pesanan:</div>
                <div class="summary-value">Rp {{ number_format($summary['average_order_value'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-row clearfix">
                <div class="summary-label">Pesanan Reguler:</div>
                <div class="summary-value">{{ number_format($summary['regular_orders_count']) }}
                    ({{ $summary['total_orders'] > 0 ? number_format(($summary['regular_orders_count'] / $summary['total_orders']) * 100, 1) : 0 }}%)
                </div>
            </div>
            <div class="summary-row clearfix">
                <div class="summary-label">Produk Kustom:</div>
                <div class="summary-value">{{ number_format($summary['custom_orders_count']) }}
                    ({{ $summary['total_orders'] > 0 ? number_format(($summary['custom_orders_count'] / $summary['total_orders']) * 100, 1) : 0 }}%)
                </div>
            </div>
            <div class="summary-row clearfix">
                <div class="summary-label">Pendapatan Reguler:</div>
                <div class="summary-value">Rp {{ number_format($summary['regular_revenue'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-row clearfix">
                <div class="summary-label">Pendapatan Kustom:</div>
                <div class="summary-value">Rp {{ number_format($summary['custom_revenue'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-row clearfix">
                <div class="summary-label">Pesanan Lunas:</div>
                <div class="summary-value">{{ number_format($summary['paid_orders_count']) }} dari
                    {{ number_format($summary['total_orders']) }}</div>
            </div>
            <div class="summary-row clearfix">
                <div class="summary-label">Pendapatan Lunas:</div>
                <div class="summary-value">Rp {{ number_format($summary['paid_revenue'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Regular Orders Section -->
    @php
        $regularOrders = $orders->where('type', 'REGULER');
    @endphp

    @if ($regularOrders->count() > 0)
        <div class="section-title">🛍️ PESANAN REGULER ({{ $regularOrders->count() }} pesanan)</div>
        <table class="orders-table">
            <thead>
                <tr>
                    <th style="width: 8%">No</th>
                    <th style="width: 15%">No. Pesanan</th>
                    <th style="width: 18%">Pelanggan</th>
                    <th style="width: 20%">Produk</th>
                    <th style="width: 12%">Total</th>
                    <th style="width: 12%">Status</th>
                    <th style="width: 15%">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($regularOrders->take(50) as $index => $order)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $order['order_number'] }}</td>
                        <td>
                            <strong>{{ $order['customer_name'] }}</strong><br>
                            <small>{{ $order['customer_phone'] }}</small>
                        </td>
                        <td>
                            @foreach ($order['products'] as $product)
                                <div>{{ $product['name'] }} ({{ $product['quantity'] }}x)</div>
                            @endforeach
                        </td>
                        <td class="text-right">Rp {{ number_format($order['total_amount'], 0, ',', '.') }}</td>
                        <td>
                            <span
                                class="status-badge
                        @if (in_array($order['status'], ['PEMBAYARAN BERHASIL', 'SELESAI'])) status-success
                        @elseif(in_array($order['status'], ['DIKEMAS', 'SIAP DIKIRIM'])) status-info
                        @elseif($order['status'] == 'MENUNGGU PEMBAYARAN') status-warning
                        @else status-danger @endif">
                                {{ $order['status'] }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($order['order_date'])->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Custom Products Section -->
    @php
        $customOrders = $orders->where('type', 'CUSTOM');
    @endphp

    @if ($customOrders->count() > 0)
        <div class="section-title page-break">🎨 PRODUK KUSTOM ({{ $customOrders->count() }} permintaan)</div>
        <table class="orders-table">
            <thead>
                <tr>
                    <th style="width: 8%">No</th>
                    <th style="width: 15%">No. Pesanan</th>
                    <th style="width: 18%">Pelanggan</th>
                    <th style="width: 20%">Produk</th>
                    <th style="width: 12%">Harga</th>
                    <th style="width: 12%">Status</th>
                    <th style="width: 15%">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customOrders->take(50) as $index => $order)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $order['order_number'] }}</td>
                        <td>
                            <strong>{{ $order['customer_name'] }}</strong><br>
                            <small>{{ $order['customer_phone'] }}</small>
                        </td>
                        <td>
                            <strong>{{ $order['products'][0]['name'] }}</strong><br>
                            <small>{{ \Str::limit($order['products'][0]['description'] ?? '', 50) }}</small>
                        </td>
                        <td class="text-right">
                            @if ($order['total_amount'] > 0)
                                Rp {{ number_format($order['total_amount'], 0, ',', '.') }}
                            @else
                                <em>Belum dikutip</em>
                            @endif
                        </td>
                        <td>
                            <span
                                class="status-badge
                        @if ($order['status'] == 'SELESAI') status-success
                        @elseif(in_array($order['status'], ['DALAM_PENGERJAAN', 'SIAP_DIKIRIM'])) status-info
                        @elseif(in_array($order['status'], ['MENUNGGU_REVIEW', 'MENUNGGU_DP', 'MENUNGGU_PELUNASAN'])) status-warning
                        @else status-danger @endif">
                                {{ str_replace('_', ' ', $order['status']) }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($order['order_date'])->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- All Orders Summary Table -->
    <div class="section-title page-break">📋 SEMUA PESANAN - RINGKASAN</div>
    <table class="orders-table">
        <thead>
            <tr>
                <th style="width: 6%">No</th>
                <th style="width: 8%">Tipe</th>
                <th style="width: 14%">No. Pesanan</th>
                <th style="width: 16%">Pelanggan</th>
                <th style="width: 18%">Produk/Layanan</th>
                <th style="width: 12%">Total</th>
                <th style="width: 10%">Status Bayar</th>
                <th style="width: 12%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders->take(100) as $index => $order)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span class="type-badge {{ $order['type'] == 'REGULER' ? 'type-reguler' : 'type-custom' }}">
                            {{ $order['type'] }}
                        </span>
                    </td>
                    <td>{{ $order['order_number'] }}</td>
                    <td>
                        <strong>{{ $order['customer_name'] }}</strong>
                        @if ($order['customer_phone'])
                            <br><small>{{ $order['customer_phone'] }}</small>
                        @endif
                    </td>
                    <td>
                        @if ($order['type'] == 'REGULER')
                            {{ collect($order['products'])->pluck('name')->take(2)->join(', ') }}
                            @if (count($order['products']) > 2)
                                <br><small>+{{ count($order['products']) - 2 }} item lainnya</small>
                            @endif
                        @else
                            <strong>{{ $order['products'][0]['name'] }}</strong>
                            @if ($order['products'][0]['description'])
                                <br><small>{{ \Str::limit($order['products'][0]['description'], 40) }}</small>
                            @endif
                        @endif
                    </td>
                    <td class="text-right">
                        @if ($order['total_amount'] > 0)
                            Rp {{ number_format($order['total_amount'], 0, ',', '.') }}
                        @else
                            <em>-</em>
                        @endif
                    </td>
                    <td class="text-center">
                        <span
                            class="status-badge
                        @if ($order['payment_status'] == 'LUNAS') status-success
                        @elseif($order['payment_status'] == 'DP DIBAYAR') status-warning
                        @else status-danger @endif">
                            {{ $order['payment_status'] }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($order['order_date'])->format('d/m/Y') }}</td>
                </tr>
            @endforeach

            @if ($orders->count() > 100)
                <tr>
                    <td colspan="8" class="text-center" style="font-style: italic; color: #666; padding: 15px;">
                        ... dan {{ $orders->count() - 100 }} pesanan lainnya
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Statistics -->
    <div class="section-title">📈 STATISTIK LANJUTAN</div>
    <div class="summary-section">
        <div class="summary-grid">
            <div class="summary-row clearfix">
                <div class="summary-label">Tingkat Konversi Pembayaran:</div>
                <div class="summary-value">
                    {{ $summary['total_orders'] > 0 ? number_format(($summary['paid_orders_count'] / $summary['total_orders']) * 100, 1) : 0 }}%
                </div>
            </div>
            <div class="summary-row clearfix">
                <div class="summary-label">Kontribusi Pesanan Reguler:</div>
                <div class="summary-value">
                    {{ $summary['total_revenue'] > 0 ? number_format(($summary['regular_revenue'] / $summary['total_revenue']) * 100, 1) : 0 }}%
                    dari total revenue</div>
            </div>
            <div class="summary-row clearfix">
                <div class="summary-label">Kontribusi Produk Kustom:</div>
                <div class="summary-value">
                    {{ $summary['total_revenue'] > 0 ? number_format(($summary['custom_revenue'] / $summary['total_revenue']) * 100, 1) : 0 }}%
                    dari total revenue</div>
            </div>
            <div class="summary-row clearfix">
                <div class="summary-label">Rata-rata Pesanan Reguler:</div>
                <div class="summary-value">Rp
                    {{ $summary['regular_orders_count'] > 0 ? number_format($summary['regular_revenue'] / $summary['regular_orders_count'], 0, ',', '.') : 0 }}
                </div>
            </div>
            <div class="summary-row clearfix">
                <div class="summary-label">Rata-rata Produk Kustom:</div>
                <div class="summary-value">Rp
                    {{ $summary['custom_orders_count'] > 0 ? number_format($summary['custom_revenue'] / $summary['custom_orders_count'], 0, ',', '.') : 0 }}
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem pada {{ $generated_at->format('d/m/Y H:i:s') }}</p>
        <p>© {{ date('Y') }} - Sistem Laporan Penjualan</p>
    </div>
</body>

</html>
