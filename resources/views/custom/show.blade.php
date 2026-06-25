@extends('layouts.app')

@section('styles')
    <style>
        /* Custom Request Detail Styling - Amado Theme */
        .custom-request-detail {
            margin-bottom: 50px;
        }

        .custom-request-header {
            border-bottom: 3px solid #fbb710;
            padding-bottom: 25px;
            margin-bottom: 40px;
            background-color: #f5f7fa;
            padding: 30px;
        }

        .custom-request-header h2 {
            color: #242424;
            font-size: 30px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .custom-detail-section {
            margin-bottom: 40px;
            background-color: #ffffff;
            padding: 30px;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.1);
        }

        .custom-detail-section h4 {
            color: #242424;
            font-size: 18px;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
            position: relative;
        }

        .custom-detail-section h4::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 80px;
            height: 3px;
            background-color: #fbb710;
        }

        /* Status Badge - Amado Style */
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 0;
        }

        .status-waiting {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-offered {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #b8daff;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-dp {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-progress {
            background-color: #fbb710;
            color: #fff;
            border: 1px solid #fbb710;
        }

        .status-payment {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status-ready {
            background-color: #131212;
            color: #fff;
            border: 1px solid #131212;
        }

        .status-shipped {
            background-color: #6f42c1;
            color: #fff;
            border: 1px solid #6f42c1;
        }

        .status-completed {
            background-color: #28a745;
            color: #fff;
            border: 1px solid #28a745;
        }

        .status-canceled {
            background-color: #dc3545;
            color: #fff;
            border: 1px solid #dc3545;
        }

        .request-date {
            font-size: 14px;
            color: #6c757d;
            margin-top: 10px;
        }

        .request-price {
            font-weight: 600;
            color: #fbb710;
            font-size: 28px;
            line-height: 1;
        }

        /* Alert Messages - Amado Style */
        .alert {
            padding: 20px;
            margin-bottom: 25px;
            border: none;
            border-radius: 0;
            border-left: 4px solid;
        }

        .alert-success {
            color: #155724;
            background-color: #f8f9fa;
            border-left-color: #28a745;
        }

        .alert-info {
            color: #0c5460;
            background-color: #f8f9fa;
            border-left-color: #17a2b8;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8f9fa;
            border-left-color: #dc3545;
        }

        /* Progress Timeline - Amado Style */
        .progress-timeline {
            position: relative;
            margin-bottom: 40px;
        }

        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 30px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: 10px;
            top: 20px;
            height: 100%;
            width: 2px;
            background-color: #e0e0e0;
        }

        .timeline-item:last-child:before {
            height: 20px;
        }

        .timeline-dot {
            position: absolute;
            left: 0;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #fbb710;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e0e0e0;
            z-index: 10;
        }

        .timeline-dot.active {
            background-color: #fbb710;
            box-shadow: 0 0 0 2px #fbb710;
        }

        .timeline-dot.inactive {
            background-color: #e0e0e0;
            box-shadow: 0 0 0 2px #e0e0e0;
        }

        .timeline-date {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .timeline-content {
            padding: 20px;
            background-color: #f5f7fa;
            border-radius: 0;
            border-left: 3px solid #fbb710;
        }

        .timeline-content h5 {
            color: #242424;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .timeline-content p {
            color: #6c757d;
            margin-bottom: 0;
            line-height: 1.6;
        }

        .timeline-image {
            text-align: center;
        }

        .timeline-image a {
            display: inline-block;
            transition: all 0.3s ease;
        }

        .timeline-image a:hover {
            transform: translateY(-2px);
        }

        .timeline-image img {
            width: 100%;
            max-width: 200px;
            height: 150px;
            margin-top: 15px;
            border: 1px solid #e0e0e0;
            object-fit: cover;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .timeline-image img:hover {
            border-color: #fbb710;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Offer Details - Amado Style */
        .offer-details {
            background-color: #f5f7fa;
            border-left: 4px solid #fbb710;
            padding: 25px;
            margin-bottom: 25px;
        }

        .offer-price {
            font-size: 28px;
            font-weight: 600;
            color: #fbb710;
            line-height: 1;
            margin-bottom: 10px;
        }

        /* Payment Info - Amado Style */
        .payment-info {
            background-color: #f5f7fa;
            border-left: 4px solid #28a745;
            padding: 25px;
            margin-bottom: 25px;
        }

        /* Tracking Info - Amado Style */
        .tracking-info {
            background-color: #f5f7fa;
            border-left: 4px solid #6f42c1;
            padding: 25px;
            margin-bottom: 25px;
        }

        /* Add Reference Form - Amado Style */
        .add-reference-form {
            margin-top: 30px;
            padding: 25px;
            background-color: #f5f7fa;
            border: 1px solid #e0e0e0;
        }

        .add-reference-form h5 {
            color: #242424;
            font-size: 18px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .add-reference-form .form-control {
            height: 50px;
            border: none;
            background-color: #ffffff;
            border-radius: 0;
            padding: 15px;
            color: #6b6b6b;
            border: 1px solid #e0e0e0;
        }

        .add-reference-form .form-control:focus {
            border-color: #fbb710;
            box-shadow: 0 0 0 0.2rem rgba(251, 183, 16, 0.25);
        }

        /* Reference Images - Amado Style dengan ratio square seperti progress */
        .reference-images {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .reference-image-item {
            position: relative;
            width: 100%;
        }

        .image-container {
            position: relative;
            overflow: hidden;
            background-color: #f5f7fa;
            width: 100%;
            aspect-ratio: 1 / 1;
            /* Memaksa square ratio 1:1 */
            border: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .image-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-color: #fbb710;
        }

        .reference-image {
            transition: all 0.3s ease;
            opacity: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Mengisi penuh container sambil maintain aspect ratio */
            object-position: center;
            /* Pastikan gambar di tengah */
        }

        .reference-image.loaded {
            opacity: 1;
        }

        .image-container:hover .reference-image {
            transform: scale(1.05);
        }

        .reference-image-desc {
            padding: 10px;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-top: none;
            font-size: 14px;
            color: #6c757d;
            text-align: center;
        }

        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            transition: opacity 0.3s;
        }

        /* Carousel Styling - Amado Style dengan square ratio */
        #referenceCarousel {
            margin-bottom: 25px;
        }

        #referenceCarousel .carousel-item {
            min-height: 220px;
        }

        #referenceCarousel .carousel-item .image-container {
            aspect-ratio: 1 / 1;
            /* Square ratio konsisten */
            min-height: 200px;
            /* Fallback untuk browser lama */
        }

        /* Fallback untuk browser yang tidak support aspect-ratio */
        @supports not (aspect-ratio: 1 / 1) {
            .image-container {
                height: 200px !important;
            }

            #referenceCarousel .carousel-item .image-container {
                height: 200px !important;
            }
        }

        #referenceCarousel .carousel-indicators {
            bottom: -25px;
        }

        #referenceCarousel .carousel-indicators li {
            background-color: #e0e0e0;
            border-radius: 50%;
            width: 12px;
            height: 12px;
            margin: 0 5px;
        }

        #referenceCarousel .carousel-indicators li.active {
            background-color: #fbb710;
        }

        #referenceCarousel .carousel-control-prev,
        #referenceCarousel .carousel-control-next {
            width: 40px;
            height: 40px;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(19, 18, 18, 0.8);
            border-radius: 50%;
        }

        #referenceCarousel .carousel-control-prev {
            left: -20px;
        }

        #referenceCarousel .carousel-control-next {
            right: -20px;
        }

        #referenceCarousel .carousel-control-prev:hover,
        #referenceCarousel .carousel-control-next:hover {
            background-color: #fbb710;
        }

        /* Action Buttons - Amado Style */
        .action-buttons {
            margin-top: 40px;
            text-align: center;
        }

        .action-buttons .amado-btn {
            margin: 0 10px 15px 10px;
            min-width: 180px;
        }

        /* Amado Button Styles */
        .amado-btn {
            display: inline-block;
            min-width: 160px;
            height: 55px;
            color: #ffffff;
            border: none;
            border-radius: 0;
            padding: 0 20px;
            font-size: 16px;
            line-height: 55px;
            background-color: #fbb710;
            font-weight: 600;
            text-transform: uppercase;
            transition: all 500ms ease 0s;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .amado-btn:hover,
        .amado-btn:focus {
            color: #ffffff;
            background-color: #131212;
            text-decoration: none;
        }

        /* Button secondary menggunakan style Amado untuk kembali */
        .amado-btn-secondary {
            display: inline-block;
            min-width: 160px;
            height: 55px;
            color: #242424;
            border: 2px solid #242424;
            border-radius: 0;
            padding: 0 20px;
            font-size: 16px;
            line-height: 51px;
            background-color: transparent;
            font-weight: 600;
            text-transform: uppercase;
            transition: all 500ms ease 0s;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .amado-btn-secondary:hover,
        .amado-btn-secondary:focus {
            color: #ffffff;
            background-color: #242424;
            text-decoration: none;
        }

        /* Button Overrides for Amado Theme */
        .btn-primary {
            background-color: #fbb710;
            border-color: #fbb710;
            color: #fff;
            border-radius: 0;
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #131212;
            border-color: #131212;
            color: #fff;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
            border-radius: 0;
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-success:hover,
        .btn-success:focus {
            background-color: #1e7e34;
            border-color: #1e7e34;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
            border-radius: 0;
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-danger:hover,
        .btn-danger:focus {
            background-color: #c82333;
            border-color: #c82333;
        }

        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
            background-color: transparent;
            border-radius: 0;
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-outline-secondary:hover,
        .btn-outline-secondary:focus {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }

        .btn-outline-primary {
            color: #fbb710;
            border-color: #fbb710;
            background-color: transparent;
            border-radius: 0;
            padding: 8px 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 14px;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus {
            background-color: #fbb710;
            border-color: #fbb710;
            color: #fff;
        }

        /* Modal Styling - Amado Theme */
        .modal-content {
            border-radius: 0;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background-color: #f5f7fa;
            border-bottom: 1px solid #e0e0e0;
            border-radius: 0;
            padding: 20px 25px;
        }

        .modal-title {
            color: #242424;
            font-weight: 600;
            font-size: 18px;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            background-color: #f5f7fa;
            border-top: 1px solid #e0e0e0;
            border-radius: 0;
            padding: 15px 25px;
        }

        .form-control {
            border-radius: 0;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #fbb710;
            box-shadow: 0 0 0 0.2rem rgba(251, 183, 16, 0.25);
        }

        /* Responsive Design */
        @media (max-width: 768px) {

            .custom-request-header,
            .custom-detail-section {
                padding: 20px;
                margin-bottom: 25px;
            }

            .custom-request-header h2 {
                font-size: 24px;
            }

            .offer-price,
            .request-price {
                font-size: 24px;
            }

            .reference-images {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .reference-image-item .image-container {
                height: 150px;
                /* Tetap square ratio di mobile */
            }

            #referenceCarousel .carousel-item {
                min-height: 180px;
            }

            #referenceCarousel .carousel-item .image-container {
                height: 150px;
            }

            #referenceCarousel .carousel-item .d-flex {
                flex-direction: column;
                align-items: center;
            }

            #referenceCarousel .carousel-item .d-flex>div {
                width: 80% !important;
                margin-bottom: 15px;
            }

            .action-buttons .amado-btn,
            .action-buttons .amado-btn-secondary {
                width: 100%;
                margin: 0 0 15px 0;
                min-width: auto;
            }

            .timeline-image img {
                max-width: 150px;
                height: 120px;
            }
        }

        /* Status Timeline Specific Colors */
        .timeline-item.completed .timeline-dot {
            background-color: #fbb710;
            box-shadow: 0 0 0 2px #fbb710;
        }

        .timeline-item.pending .timeline-dot {
            background-color: #e0e0e0;
            box-shadow: 0 0 0 2px #e0e0e0;
        }

        .timeline-item.current .timeline-dot {
            background-color: #fbb710;
            box-shadow: 0 0 0 2px #fbb710;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(251, 183, 16, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(251, 183, 16, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(251, 183, 16, 0);
            }
        }

        /* FancyBox Overrides for Amado Theme */
        .fancybox-button {
            background: #fbb710 !important;
        }

        .fancybox-button:hover {
            background: #131212 !important;
        }

        .fancybox-navigation .fancybox-button {
            background: rgba(251, 183, 16, 0.8) !important;
        }

        .fancybox-navigation .fancybox-button:hover {
            background: rgba(19, 18, 18, 0.8) !important;
        }

        /* Section padding sesuai template */
        .section-padding-100 {
            padding: 100px 0;
        }
    </style>
@endsection

@section('content')
    <div class="cart-table-area section-padding-100 clearfix">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @include('notifikasi.notifikasi')

                    <div class="custom-request-detail">
                        <div class="custom-request-header">
                            <div class="d-flex justify-content-between align-items-start flex-wrap">
                                <div class="mb-3">
                                    <h2>{{ $customRequest->title }}</h2>
                                    <div class="request-date">
                                        <i class="fa fa-calendar"></i>
                                        <span>Dibuat: {{ $customRequest->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    @php
                                        $statusClass = '';
                                        switch ($customRequest->status) {
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
                                    <span class="status-badge {{ $statusClass }}">
                                        <i class="fa fa-info-circle"></i>
                                        {{ $statusText }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Informasi Permintaan -->
                                <div class="custom-detail-section">
                                    <h4><i class="fa fa-info-circle"></i> Informasi Permintaan</h4>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p><strong>Deskripsi:</strong><br>{{ $customRequest->description }}</p>

                                            @if ($customRequest->specifications)
                                                <p><strong>Spesifikasi:</strong><br>{{ $customRequest->specifications }}</p>
                                            @endif

                                            <div class="row mt-3">
                                                @if ($customRequest->budget)
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Budget Awal:</strong><br>
                                                        <span class="text-muted">Rp
                                                            {{ number_format($customRequest->budget, 0, ',', '.') }}</span>
                                                    </div>
                                                @endif

                                                @if ($customRequest->desired_deadline)
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Deadline Harapan:</strong><br>
                                                        <span
                                                            class="text-muted">{{ \Carbon\Carbon::parse($customRequest->desired_deadline)->format('d M Y') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gambar Referensi dengan Fancybox dan Loading -->
                                @if (count($customRequest->references) > 0)
                                    <div class="custom-detail-section">
                                        <h4><i class="fa fa-image"></i> Gambar Referensi
                                            ({{ count($customRequest->references) }})</h4>

                                        @if (count($customRequest->references) > 4)
                                            <!-- Carousel untuk banyak gambar -->
                                            <div id="referenceCarousel" class="carousel slide mb-3" data-ride="carousel">
                                                <ol class="carousel-indicators">
                                                    @foreach ($customRequest->references->chunk(3) as $key => $chunk)
                                                        <li data-target="#referenceCarousel"
                                                            data-slide-to="{{ $key }}"
                                                            class="{{ $key == 0 ? 'active' : '' }}"></li>
                                                    @endforeach
                                                </ol>

                                                <div class="carousel-inner">
                                                    @foreach ($customRequest->references->chunk(3) as $key => $chunk)
                                                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                                            <div class="d-flex justify-content-around">
                                                                @foreach ($chunk as $reference)
                                                                    <div class="mx-1" style="width: 32%;">
                                                                        <div class="image-container">
                                                                            <div class="loading-spinner">
                                                                                <div class="spinner-border text-warning"
                                                                                    role="status"
                                                                                    style="width: 1.5rem; height: 1.5rem;">
                                                                                    <span class="sr-only">Loading...</span>
                                                                                </div>
                                                                            </div>
                                                                            <a href="{{ asset('storage/' . $reference->image_path) }}"
                                                                                data-fancybox="gallery"
                                                                                data-caption="{{ $reference->description ?? 'Gambar Referensi' }}">
                                                                                <img src="{{ asset('storage/' . $reference->image_path) }}"
                                                                                    alt="Referensi"
                                                                                    class="reference-image lazy">
                                                                            </a>
                                                                        </div>
                                                                        @if ($reference->description)
                                                                            <div class="reference-image-desc">
                                                                                {{ Str::limit($reference->description, 25) }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <a class="carousel-control-prev" href="#referenceCarousel" role="button"
                                                    data-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="carousel-control-next" href="#referenceCarousel" role="button"
                                                    data-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </div>

                                            <div class="text-center mb-3">
                                                <button class="btn btn-outline-primary"
                                                    onclick="$('[data-fancybox=gallery]').first().trigger('click')">
                                                    <i class="fa fa-eye"></i> Lihat Semua Gambar
                                                </button>
                                            </div>
                                        @else
                                            <!-- Tampilan grid untuk sedikit gambar -->
                                            <div class="reference-images">
                                                @foreach ($customRequest->references as $reference)
                                                    <div class="reference-image-item">
                                                        <div class="image-container">
                                                            <div class="loading-spinner">
                                                                <div class="spinner-border text-warning" role="status"
                                                                    style="width: 1.5rem; height: 1.5rem;">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div>
                                                            </div>
                                                            <a href="{{ asset('storage/' . $reference->image_path) }}"
                                                                data-fancybox="gallery"
                                                                data-caption="{{ $reference->description ?? 'Gambar Referensi' }}">
                                                                <img src="{{ asset('storage/' . $reference->image_path) }}"
                                                                    alt="Referensi" class="reference-image lazy">
                                                            </a>
                                                        </div>
                                                        @if ($reference->description)
                                                            <div class="reference-image-desc">
                                                                {{ $reference->description }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Form tambah referensi -->
                                @if (in_array($customRequest->status, ['MENUNGGU_REVIEW', 'PENAWARAN_DIBERIKAN']))
                                    <div class="add-reference-form">
                                        <h5><i class="fa fa-plus-circle"></i> Tambah Gambar Referensi</h5>
                                        <form action="{{ route('custom.add-reference', $customRequest->id) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="image">Gambar</label>
                                                    <input type="file" class="form-control" id="image" name="image"
                                                        accept="image/*" required>
                                                    @error('image')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="description">Deskripsi (Opsional)</label>
                                                    <input type="text" class="form-control" id="description"
                                                        name="description" placeholder="Deskripsi gambar...">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-upload"></i> Tambah Referensi
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                <!-- Penawaran -->
                                @if ($customRequest->quoted_price)
                                    <div class="custom-detail-section">
                                        <h4><i class="fa fa-calculator"></i> Detail Penawaran</h4>
                                        <div class="offer-details">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Harga Total:</strong></p>
                                                    <p class="offer-price">Rp
                                                        {{ number_format($customRequest->quoted_price, 0, ',', '.') }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>DP yang diperlukan:</strong></p>
                                                    <p class="text-muted">Rp
                                                        {{ number_format($customRequest->down_payment, 0, ',', '.') }}</p>
                                                    <p><strong>Sisa Pembayaran:</strong></p>
                                                    <p class="text-muted">Rp
                                                        {{ number_format($customRequest->remaining_payment, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <p><strong>Estimasi Penyelesaian:</strong></p>
                                                    <p class="text-muted">
                                                        {{ \Carbon\Carbon::parse($customRequest->estimated_completion)->format('d M Y') }}
                                                    </p>

                                                    @if ($customRequest->admin_notes)
                                                        <p><strong>Catatan Admin:</strong></p>
                                                        <p class="text-muted">{{ $customRequest->admin_notes }}</p>
                                                    @endif
                                                </div>
                                            </div>

                                            @if ($customRequest->status === 'PENAWARAN_DIBERIKAN')
                                                <div class="mt-4 text-center">
                                                    <form action="{{ route('custom.accept-offer', $customRequest->id) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fa fa-check"></i> Terima Penawaran
                                                        </button>
                                                    </form>

                                                    <button type="button" class="btn btn-danger" data-toggle="modal"
                                                        data-target="#rejectModal">
                                                        <i class="fa fa-times"></i> Tolak Penawaran
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Informasi Pengiriman -->
                                @if ($customRequest->shipment)
                                    <div class="custom-detail-section">
                                        <h4><i class="fa fa-truck"></i> Informasi Pengiriman</h4>
                                        <div class="tracking-info">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Penerima:</strong><br>{{ $customRequest->shipment->full_name }}
                                                    </p>
                                                    <p><strong>Kontak:</strong><br>{{ $customRequest->shipment->phone }} /
                                                        {{ $customRequest->shipment->email }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Alamat:</strong><br>
                                                        {{ $customRequest->shipment->address }},<br>
                                                        {{ $customRequest->shipment->city->name }},
                                                        {{ $customRequest->shipment->province->name }}<br>
                                                        {{ $customRequest->shipment->postal_code }}
                                                    </p>
                                                    <p><strong>Biaya Pengiriman:</strong><br>Rp
                                                        {{ number_format($customRequest->shipment->shipping_cost, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if ($customRequest->shipment->tracking_number)
                                                <p><strong>Nomor Resi:</strong><br>
                                                    <span
                                                        class="badge badge-primary">{{ $customRequest->shipment->tracking_number }}</span>
                                                </p>
                                            @endif

                                            @if ($customRequest->shipment->notes)
                                                <p><strong>Catatan
                                                        Pengiriman:</strong><br>{{ $customRequest->shipment->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Progress Pengerjaan -->
                                @if (count($customRequest->progresses) > 0)
                                    <div class="custom-detail-section">
                                        <h4><i class="fa fa-tasks"></i> Progress Pengerjaan</h4>
                                        <div class="progress-timeline">
                                            @foreach ($customRequest->progresses as $progress)
                                                <div class="timeline-item">
                                                    <div class="timeline-dot"></div>
                                                    <div class="timeline-date">
                                                        {{ $progress->created_at->format('d M Y - H:i') }}</div>
                                                    <div class="timeline-content">
                                                        <h5>Update Progress</h5>
                                                        <p>{{ $progress->description }}</p>
                                                        @if ($progress->image_path)
                                                            <div class="timeline-image">
                                                                <a href="{{ asset('storage/' . $progress->image_path) }}"
                                                                    data-fancybox="progress-gallery"
                                                                    data-caption="Progress: {{ $progress->description }}">
                                                                    <img src="{{ asset('storage/' . $progress->image_path) }}"
                                                                        alt="Progress">
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Informasi Pembayaran -->
                                <div class="custom-detail-section">
                                    <h4><i class="fa fa-credit-card"></i> Informasi Pembayaran</h4>
                                    <div class="payment-info">
                                        @if ($customRequest->dp_payment_date)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>DP Dibayar:</strong><br>Rp
                                                        {{ number_format($customRequest->down_payment, 0, ',', '.') }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Tanggal Pembayaran
                                                            DP:</strong><br>{{ \Carbon\Carbon::parse($customRequest->dp_payment_date)->format('d M Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($customRequest->status === 'MENUNGGU_DP')
                                            <p class="mb-3">Menunggu pembayaran DP sebesar:</p>
                                            <p class="offer-price mb-3">Rp
                                                {{ number_format($customRequest->down_payment, 0, ',', '.') }}</p>
                                            <a href="{{ route('custom.payment.dp', $customRequest->id) }}"
                                                class="amado-btn">
                                                <i class="fa fa-credit-card"></i> Bayar DP Sekarang
                                            </a>
                                        @endif

                                        @if ($customRequest->full_payment_date)
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <p><strong>Pelunasan:</strong><br>Rp
                                                        {{ number_format($customRequest->remaining_payment, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Tanggal
                                                            Pelunasan:</strong><br>{{ \Carbon\Carbon::parse($customRequest->full_payment_date)->format('d M Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($customRequest->status === 'MENUNGGU_PELUNASAN')
                                            <p class="mb-3">Menunggu pelunasan sebesar:</p>
                                            <p class="offer-price mb-3">Rp
                                                {{ number_format($customRequest->remaining_payment, 0, ',', '.') }}</p>

                                            @if ($customRequest->shipment)
                                                <a href="{{ route('custom.payment.full', $customRequest->id) }}"
                                                    class="amado-btn">
                                                    <i class="fa fa-credit-card"></i> Bayar Pelunasan Sekarang
                                                </a>
                                            @else
                                                <a href="{{ route('custom.shipping', $customRequest->id) }}"
                                                    class="amado-btn">
                                                    <i class="fa fa-truck"></i> Isi Data Pengiriman
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <!-- Tombol konfirmasi penerimaan -->
                                @if ($customRequest->status === 'DIKIRIM')
                                    <div class="action-buttons">
                                        <form action="{{ route('custom.mark-complete', $customRequest->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="amado-btn w-100">
                                                <i class="fa fa-check-circle"></i> Konfirmasi Penerimaan Produk
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                <!-- Tombol kembali dengan style Amado -->
                                <div class="action-buttons">
                                    <a href="{{ route('custom.my-requests') }}" class="amado-btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar Permintaan
                                    </a>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <!-- Timeline Status -->
                                <div class="custom-detail-section">
                                    <h4><i class="fa fa-clock-o"></i> Timeline Status</h4>
                                    <div class="progress-timeline">
                                        <div
                                            class="timeline-item {{ $customRequest->created_at ? 'completed' : 'pending' }}">
                                            <div class="timeline-dot"></div>
                                            <div class="timeline-date">{{ $customRequest->created_at->format('d M Y') }}
                                            </div>
                                            <div class="timeline-content">
                                                <h5>Permintaan Dibuat</h5>
                                                <p>Permintaan produk kustom Anda telah berhasil dibuat.</p>
                                            </div>
                                        </div>

                                        <div
                                            class="timeline-item {{ in_array($customRequest->status, ['PENAWARAN_DIBERIKAN', 'PENAWARAN_DITOLAK', 'MENUNGGU_DP', 'DALAM_PENGERJAAN', 'MENUNGGU_PELUNASAN', 'SIAP_DIKIRIM', 'DIKIRIM', 'SELESAI']) ? 'completed' : 'pending' }}">
                                            <div class="timeline-dot"></div>
                                            <div class="timeline-content">
                                                <h5>Penawaran Diberikan</h5>
                                                <p>Admin memberikan penawaran harga untuk permintaan Anda.</p>
                                            </div>
                                        </div>

                                        <div
                                            class="timeline-item {{ in_array($customRequest->status, ['MENUNGGU_DP', 'DALAM_PENGERJAAN', 'MENUNGGU_PELUNASAN', 'SIAP_DIKIRIM', 'DIKIRIM', 'SELESAI']) ? 'completed' : ($customRequest->status === 'PENAWARAN_DIBERIKAN' ? 'current' : 'pending') }}">
                                            <div class="timeline-dot"></div>
                                            <div class="timeline-content">
                                                <h5>Penawaran Diterima</h5>
                                                <p>Anda menerima penawaran dan melanjutkan ke pembayaran DP.</p>
                                            </div>
                                        </div>

                                        <div
                                            class="timeline-item {{ in_array($customRequest->status, ['DALAM_PENGERJAAN', 'MENUNGGU_PELUNASAN', 'SIAP_DIKIRIM', 'DIKIRIM', 'SELESAI']) ? 'completed' : ($customRequest->status === 'MENUNGGU_DP' ? 'current' : 'pending') }}">
                                            <div class="timeline-dot"></div>
                                            <div class="timeline-content">
                                                <h5>DP Dibayar</h5>
                                                <p>Pembayaran DP telah diterima dan produk mulai dikerjakan.</p>
                                            </div>
                                        </div>

                                        <div
                                            class="timeline-item {{ in_array($customRequest->status, ['MENUNGGU_PELUNASAN', 'SIAP_DIKIRIM', 'DIKIRIM', 'SELESAI']) ? 'completed' : ($customRequest->status === 'DALAM_PENGERJAAN' ? 'current' : 'pending') }}">
                                            <div class="timeline-dot"></div>
                                            <div class="timeline-content">
                                                <h5>Produk Selesai Dibuat</h5>
                                                <p>Produk kustom Anda telah selesai dibuat.</p>
                                            </div>
                                        </div>

                                        <div
                                            class="timeline-item {{ in_array($customRequest->status, ['SIAP_DIKIRIM', 'DIKIRIM', 'SELESAI']) ? 'completed' : ($customRequest->status === 'MENUNGGU_PELUNASAN' ? 'current' : 'pending') }}">
                                            <div class="timeline-dot"></div>
                                            <div class="timeline-content">
                                                <h5>Pelunasan Dibayar</h5>
                                                <p>Pembayaran pelunasan telah diterima.</p>
                                            </div>
                                        </div>

                                        <div
                                            class="timeline-item {{ in_array($customRequest->status, ['DIKIRIM', 'SELESAI']) ? 'completed' : ($customRequest->status === 'SIAP_DIKIRIM' ? 'current' : 'pending') }}">
                                            <div class="timeline-dot"></div>
                                            <div class="timeline-content">
                                                <h5>Produk Dikirim</h5>
                                                <p>Produk kustom Anda sedang dalam perjalanan.</p>
                                            </div>
                                        </div>

                                        <div
                                            class="timeline-item {{ $customRequest->status == 'SELESAI' ? 'completed' : ($customRequest->status === 'DIKIRIM' ? 'current' : 'pending') }}">
                                            <div class="timeline-dot"></div>
                                            <div class="timeline-content">
                                                <h5>Selesai</h5>
                                                <p>Produk kustom Anda telah diterima.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tolak Penawaran -->
    @if ($customRequest->status === 'PENAWARAN_DIBERIKAN')
        <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">
                            <i class="fa fa-times-circle"></i> Tolak Penawaran
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('custom.reject-offer', $customRequest->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="reason">Alasan Penolakan (Opsional)</label>
                                <textarea class="form-control" id="reason" name="reason" rows="4"
                                    placeholder="Berikan alasan mengapa Anda menolak penawaran ini..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                <i class="fa fa-times"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-times-circle"></i> Tolak Penawaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi FancyBox dengan theme Amado
            $('[data-fancybox="gallery"]').fancybox({
                buttons: ['slideShow', 'fullScreen', 'thumbs', 'close'],
                loop: true,
                protect: true,
                animationEffect: "fade",
                transitionEffect: "slide",
                thumbs: {
                    autoStart: true
                },
                // Custom button styling untuk Amado theme
                infobar: true,
                toolbar: true,
                smallBtn: "auto",
                iframe: {
                    preload: false
                }
            });

            // FancyBox untuk progress images
            $('[data-fancybox="progress-gallery"]').fancybox({
                buttons: ['slideShow', 'fullScreen', 'close'],
                loop: true,
                protect: true,
                animationEffect: "fade",
                transitionEffect: "slide",
                infobar: true,
                toolbar: true,
                smallBtn: "auto"
            });

            // Setting interval carousel
            $('#referenceCarousel').carousel({
                interval: 5000
            });

            // Handling loading image dengan style Amado
            $('.reference-image').each(function() {
                var img = $(this);
                var spinner = img.closest('.image-container').find('.loading-spinner');

                if (img[0].complete) {
                    img.addClass('loaded');
                    spinner.fadeOut(300);
                } else {
                    img.on('load', function() {
                        img.addClass('loaded');
                        spinner.fadeOut(300);
                    });

                    img.on('error', function() {
                        spinner.html(
                            '<div class="text-danger"><i class="fa fa-exclamation-triangle"></i><br>Error loading image</div>'
                        );
                    });
                }
            });

            // Smooth scroll untuk internal links
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });

            // Form validation untuk add reference
            $('form[action*="add-reference"]').on('submit', function(e) {
                var fileInput = $(this).find('input[type="file"]');
                if (fileInput[0].files.length === 0) {
                    e.preventDefault();
                    alert('Silakan pilih file gambar terlebih dahulu!');
                    fileInput.focus();
                }
            });

            // Confirmation untuk actions penting
            $('form[action*="accept-offer"], form[action*="mark-complete"]').on('submit', function(e) {
                var action = $(this).find('button[type="submit"]').text().trim();
                if (!confirm('Apakah Anda yakin ingin ' + action.toLowerCase() + '?')) {
                    e.preventDefault();
                }
            });

            // Auto-hide alerts setelah 5 detik
            $('.alert').delay(5000).fadeOut('slow');

            // Tooltip untuk status badges
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
