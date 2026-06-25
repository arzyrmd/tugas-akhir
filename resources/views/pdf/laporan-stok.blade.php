<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $judul }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        img {
            max-width: 50px;
            height: auto;
        }
    </style>
</head>

<body>
    <h2>{{ $judul }}</h2>
    <p><strong>Periode:</strong> {{ $tanggal_mulai }} s/d {{ $tanggal_akhir }}</p>
    <p><strong>Tanggal Cetak:</strong> {{ $tanggal_cetak }}</p>

    {{-- ✅ Tampilkan Top 5 Produk Terlaris --}}
    @if (!empty($produk_terlaris))
        <h3 style="margin-top: 20px;">Top 5 Produk Terlaris</h3>
        <table border="1" cellpadding="6" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Gambar</th>
                    <th>Total Terjual</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($produk_terlaris as $produk)
                    <tr>
                        <td>{{ $produk['nama_produk'] }}</td>
                        <td>
                            @if (!empty($produk['gambar']))
                                <img src="{{ public_path('storage/' . $produk['gambar']) }}" alt="gambar" width="60">
                            @else
                                Tidak Ada Gambar
                            @endif
                        </td>
                        <td>{{ $produk['total_terjual'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ✅ Tabel Utama --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Gambar</th>
                <th>Nama Produk</th>
                <th>Kode</th>
                <th>Kategori</th>

                @if ($jenis_laporan === 'stok_keseluruhan')
                    <th>Stok</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Status</th>
                @else
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Sebelum</th>
                    <th>Setelah</th>
                    <th>Catatan</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if (isset($item['gambar']))
                            <img src="{{ public_path('storage/' . $item['gambar']) }}" alt="gambar">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item['nama_produk'] ?? '-' }}</td>
                    <td>{{ $item['kode_produk'] }}</td>
                    <td>{{ $item['kategori'] }}</td>

                    @if ($jenis_laporan === 'stok_keseluruhan')
                        <td>{{ $item['stok_saat_ini'] }}</td>
                        <td>{{ $item['total_masuk'] }}</td>
                        <td>{{ $item['total_keluar'] }}</td>
                        <td>{{ $item['status'] }}</td>
                    @else
                        <td>{{ $item['tanggal'] }}</td>
                        <td>{{ $item['jumlah'] }}</td>
                        <td>{{ $item['stok_sebelum'] }}</td>
                        <td>{{ $item['stok_setelah'] }}</td>
                        <td>{{ $item['keterangan'] }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
