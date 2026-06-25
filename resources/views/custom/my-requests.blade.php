@extends('layouts.app')

@section('styles')
    <style>
        .custom-request-list .card {
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .custom-request-list .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .custom-request-card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            padding: 15px;
        }

        .custom-request-card-body {
            padding: 15px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-waiting {
            background-color: #ffeeba;
            color: #856404;
        }

        .status-offered {
            background-color: #b8daff;
            color: #004085;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-dp {
            background-color: #d4edda;
            color: #155724;
        }

        .status-progress {
            background-color: #17a2b8;
            color: #fff;
        }

        .status-payment {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-ready {
            background-color: #2196F3;
            color: #fff;
        }

        .status-shipped {
            background-color: #9c27b0;
            color: #fff;
        }

        .status-completed {
            background-color: #4CAF50;
            color: #fff;
        }

        .status-canceled {
            background-color: #f44336;
            color: #fff;
        }

        .request-date {
            font-size: 14px;
            color: #6c757d;
        }

        .request-price {
            font-weight: 600;
            color: #fbb710;
            font-size: 18px;
        }

        .pagination {
            margin-top: 30px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }

        .alert-info {
            color: #31708f;
            background-color: #d9edf7;
            border-color: #bce8f1;
        }

        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }

        .custom-section-title {
            border-bottom: 1px solid #ebebeb;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .no-requests {
            text-align: center;
            padding: 40px 0;
        }

        .no-requests i {
            font-size: 40px;
            color: #e0e0e0;
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="cart-table-area section-padding-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="custom-section-title">
                        <h2>Permintaan Produk Kustom Saya</h2>
                        <p>Daftar permintaan produk kustom yang Anda ajukan</p>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif

                    <div class="custom-request-list">
                        @if (count($customRequests) > 0)
                            @foreach ($customRequests as $request)
                                <div class="card">
                                    <div
                                        class="custom-request-card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5>{{ $request->title }}</h5>
                                            <span class="request-date">Dibuat:
                                                {{ $request->created_at->format('d M Y') }}</span>
                                        </div>
                                        <div>
                                            @php
                                                $statusClass = '';
                                                switch ($request->status) {
                                                    case 'MENUNGGU_REVIEW':
                                                        $statusClass = 'status-waiting';
                                                        $statusText = 'Menunggu Review';
                                                        break;
                                                    case 'PENAWARAN_DIBERIKAN':
                                                        $statusClass = 'status-offered';
                                                        $statusText = 'Penawaran Diberikan';
                                                        break;
                                                    case 'PENAWARAN_DITOLAK':
                                                        $statusClass = 'status-rejected';
                                                        $statusText = 'Penawaran Ditolak';
                                                        break;
                                                    case 'MENUNGGU_DP':
                                                        $statusClass = 'status-dp';
                                                        $statusText = 'Menunggu DP';
                                                        break;
                                                    case 'DALAM_PENGERJAAN':
                                                        $statusClass = 'status-progress';
                                                        $statusText = 'Dalam Pengerjaan';
                                                        break;
                                                    case 'MENUNGGU_PELUNASAN':
                                                        $statusClass = 'status-payment';
                                                        $statusText = 'Menunggu Pelunasan';
                                                        break;
                                                    case 'SIAP_DIKIRIM':
                                                        $statusClass = 'status-ready';
                                                        $statusText = 'Siap Dikirim';
                                                        break;
                                                    case 'DIKIRIM':
                                                        $statusClass = 'status-shipped';
                                                        $statusText = 'Dikirim';
                                                        break;
                                                    case 'SELESAI':
                                                        $statusClass = 'status-completed';
                                                        $statusText = 'Selesai';
                                                        break;
                                                    case 'DIBATALKAN':
                                                        $statusClass = 'status-canceled';
                                                        $statusText = 'Dibatalkan';
                                                        break;
                                                }
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                        </div>
                                    </div>
                                    <div class="custom-request-card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <p>{{ Str::limit($request->description, 150) }}</p>

                                                @if ($request->quoted_price)
                                                    <div class="d-flex mt-2">
                                                        <div class="mr-4">
                                                            <strong>Harga:</strong>
                                                            <span class="request-price">Rp
                                                                {{ number_format($request->quoted_price, 0, ',', '.') }}</span>
                                                        </div>

                                                        @if ($request->estimated_completion)
                                                            <div>
                                                                <strong>Estimasi Selesai:</strong>
                                                                <span>{{ \Carbon\Carbon::parse($request->estimated_completion)->format('d M Y') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <a href="{{ route('custom.show', $request->id) }}"
                                                    class="btn amado-btn">Lihat Detail</a>

                                                @if ($request->status === 'PENAWARAN_DIBERIKAN')
                                                    <div class="mt-2">
                                                        <form action="{{ route('custom.accept-offer', $request->id) }}"
                                                            method="POST" style="display: inline-block;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">Terima
                                                                Penawaran</button>
                                                        </form>

                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            data-toggle="modal"
                                                            data-target="#rejectModal{{ $request->id }}">
                                                            Tolak
                                                        </button>
                                                    </div>
                                                @endif

                                                @if ($request->status === 'MENUNGGU_DP')
                                                    <a href="{{ route('custom.payment.dp', $request->id) }}"
                                                        class="btn btn-sm btn-primary mt-2">
                                                        Bayar DP
                                                    </a>
                                                @endif

                                                @if ($request->status === 'MENUNGGU_PELUNASAN')
                                                    <a href="{{ route('custom.shipping', $request->id) }}"
                                                        class="btn btn-sm btn-primary mt-2">
                                                        Isi Data Pengiriman
                                                    </a>
                                                @endif

                                                @if ($request->status === 'DIKIRIM')
                                                    <form action="{{ route('custom.mark-complete', $request->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm btn-success mt-2">Konfirmasi Diterima</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Tolak Penawaran -->
                                @if ($request->status === 'PENAWARAN_DIBERIKAN')
                                    <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="rejectModalLabel">Tolak Penawaran</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('custom.reject-offer', $request->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="reason">Alasan Penolakan (Opsional)</label>
                                                            <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak
                                                            Penawaran</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="no-requests">
                                <i class="fa fa-file-o"></i>
                                <h4>Belum ada permintaan produk kustom</h4>
                                <p>Anda belum mengajukan permintaan produk kustom apapun.</p>
                                <a href="{{ route('custom.index') }}" class="btn amado-btn mt-3">Buat Permintaan</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
