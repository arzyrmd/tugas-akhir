@extends('layouts.app')

@section('styles')
    <style>
        /* Enhanced Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Main Content Animation */
        .custom-product-container {
            animation: fadeInUp 0.8s ease-out;
        }

        .form-section {
            animation: slideInLeft 0.6s ease-out;
            animation-delay: 0.2s;
            animation-fill-mode: both;
        }

        .benefits-section {
            animation: slideInRight 0.6s ease-out;
            animation-delay: 0.4s;
            animation-fill-mode: both;
        }

        /* Enhanced Form Styling */
        .custom-product-form {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            padding: 40px;
            margin-bottom: 30px;
        }

        .custom-product-form .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .custom-product-form label {
            font-weight: 600;
            color: #242424;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .custom-product-form label span {
            color: #fbb710;
            font-weight: bold;
        }

        .custom-product-form .form-control {
            border: 2px solid #f5f7fa;
            border-radius: 0;
            padding: 15px 20px;
            font-size: 14px;
            color: #6b6b6b;
            transition: all 0.3s ease;
            background-color: #f5f7fa;
        }

        .custom-product-form .form-control:focus {
            border-color: #fbb710;
            background-color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(251, 183, 16, 0.25);
        }

        .custom-product-form .form-control:hover {
            border-color: #e9ecef;
            background-color: #fff;
        }

        /* Compact Info Box - UPDATED FOR CONSISTENCY */
        .info-box {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border: 1px solid rgba(251, 183, 16, 0.1);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            color: #242424;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .info-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #fbb710, #f39c12);
        }

        .info-box h4 {
            color: #242424;
            margin-bottom: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            font-size: 18px;
        }

        .info-box h4 i {
            margin-right: 10px;
            font-size: 20px;
            color: #fbb710;
            padding: 8px;
            background: rgba(251, 183, 16, 0.1);
            border-radius: 50%;
        }

        .info-box p {
            margin: 0;
            color: #6b6b6b;
            line-height: 1.5;
            font-weight: 500;
        }

        /* Reference Images Enhancement */
        .reference-image-row {
            display: flex;
            margin-bottom: 15px;
            align-items: center;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            border: 2px dashed #e9ecef;
            transition: all 0.3s ease;
            animation: fadeInUp 0.4s ease-out;
        }

        .reference-image-row:hover {
            border-color: #fbb710;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(251, 183, 16, 0.1);
        }

        .reference-image-col {
            flex: 1;
            padding-right: 15px;
        }

        .reference-image-col:last-child {
            padding-right: 0;
        }

        .remove-reference-btn {
            flex: 0 0 40px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .remove-btn {
            background: #ff084e;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .remove-btn:hover {
            background: #ff4d79;
            transform: scale(1.1);
            box-shadow: 0 3px 10px rgba(255, 8, 78, 0.3);
        }

        /* Enhanced Alert Styling */
        .alert {
            padding: 20px;
            margin-bottom: 25px;
            border: none;
            border-radius: 8px;
            border-left: 4px solid;
            animation: slideInLeft 0.5s ease-out;
            font-weight: 500;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-left-color: #28a745;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-left-color: #dc3545;
        }

        .alert i {
            margin-right: 10px;
            font-size: 18px;
        }

        /* Enhanced Form Title */
        .custom-form-title {
            text-align: center;
            padding-bottom: 25px;
            margin-bottom: 35px;
            position: relative;
        }

        .custom-form-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(to right, #fbb710, #f39c12);
            border-radius: 2px;
        }

        .custom-form-title h2 {
            color: #242424;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 32px;
        }

        .custom-form-title p {
            color: #6b6b6b;
            font-size: 16px;
            margin-bottom: 0;
        }

        /* Enhanced Button Styling */
        .btn-add-reference {
            background: transparent;
            border: 2px dashed #fbb710;
            color: #fbb710;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-add-reference:hover {
            background: #fbb710;
            color: white;
            border-style: solid;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(251, 183, 16, 0.3);
        }

        .btn-add-reference i {
            transition: transform 0.3s ease;
        }

        .btn-add-reference:hover i {
            transform: rotate(180deg);
        }

        /* Enhanced Submit Button */
        .amado-btn-submit {
            background: linear-gradient(135deg, #fbb710 0%, #f39c12 100%);
            border: none;
            padding: 18px 40px;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 8px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .amado-btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .amado-btn-submit:hover::before {
            left: 100%;
        }

        .amado-btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(251, 183, 16, 0.4);
        }

        /* Sidebar Sticky Cards */
        .sidebar-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(251, 183, 16, 0.1);
        }

        .sidebar-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #fbb710, #f39c12, #e67e22);
        }

        /* UPDATED BENEFITS CARD FOR CONSISTENCY */
        .sidebar-card.benefits-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            color: #242424;
            border: 1px solid rgba(251, 183, 16, 0.1);
        }

        .sidebar-card.benefits-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #fbb710, #f39c12);
        }

        .sidebar-card.benefits-card h5 {
            color: #242424;
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar-card.benefits-card h5 i {
            color: #fbb710;
            font-size: 24px;
            padding: 8px;
            background: rgba(251, 183, 16, 0.1);
            border-radius: 50%;
        }

        .sidebar-card.benefits-card .summary-table li {
            color: #242424;
            border-bottom: 1px solid #f0f0f0;
            padding: 15px 0;
            display: flex;
            align-items: center;
            gap: 15px;
            line-height: 1.5;
            transition: all 0.3s ease;
            font-size: 15px;
            font-weight: 500;
        }

        .sidebar-card.benefits-card .summary-table li:last-child {
            border-bottom: none;
        }

        .sidebar-card.benefits-card .summary-table li:hover {
            color: #fbb710;
            background: rgba(251, 183, 16, 0.05);
            border-radius: 8px;
            padding-left: 15px;
            padding-right: 15px;
            transform: translateX(8px);
        }

        .sidebar-card.benefits-card .summary-table li i {
            color: #28a745;
            background: rgba(40, 167, 69, 0.1);
            padding: 6px;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .sidebar-card.contact-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            color: #242424;
            border: 2px solid rgba(251, 183, 16, 0.2);
        }

        .sidebar-card.contact-card h5 {
            color: #242424;
        }

        .sidebar-card.contact-card .contact-cta p {
            color: #6b6b6b;
            font-size: 16px;
            line-height: 1.6;
        }

        .sidebar-card h5 {
            color: #242424;
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar-card h5 i {
            color: #fbb710;
            font-size: 24px;
            padding: 8px;
            background: rgba(251, 183, 16, 0.1);
            border-radius: 50%;
        }

        .summary-table {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .summary-table li {
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 15px;
            color: #242424;
            line-height: 1.5;
            transition: all 0.3s ease;
            font-size: 15px;
            font-weight: 500;
        }

        .summary-table li:last-child {
            border-bottom: none;
        }

        .summary-table li:hover {
            color: #fbb710;
            transform: translateX(8px);
            background: rgba(251, 183, 16, 0.05);
            border-radius: 8px;
            padding-left: 15px;
            padding-right: 15px;
        }

        .summary-table li i {
            color: #28a745;
            font-size: 16px;
            flex-shrink: 0;
            background: rgba(40, 167, 69, 0.1);
            padding: 6px;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Progress Steps */
        .steps-container {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(251, 183, 16, 0.1);
        }

        .steps-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #fbb710, #f39c12);
        }

        .steps-container h5 {
            color: #242424;
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .steps-container h5 i {
            color: #fbb710;
            font-size: 24px;
            padding: 8px;
            background: rgba(251, 183, 16, 0.1);
            border-radius: 50%;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .step-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .step-item:hover {
            background: rgba(251, 183, 16, 0.05);
            border-radius: 8px;
            padding-left: 15px;
            padding-right: 15px;
            transform: translateX(8px);
        }

        .step-number {
            background: #fbb710;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
            margin-right: 15px;
            box-shadow: 0 2px 8px rgba(251, 183, 16, 0.3);
        }

        .step-content {
            flex: 1;
        }

        .step-content h6 {
            margin: 0 0 5px 0;
            color: #242424;
            font-size: 15px;
            font-weight: 600;
        }

        .step-content p {
            margin: 0;
            color: #6b6b6b;
            font-size: 14px;
            line-height: 1.5;
            font-weight: 400;
        }

        /* File Input Enhancement */
        .file-input-wrapper {
            position: relative;
            display: block;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-display {
            border: 2px dashed #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-input-display:hover {
            border-color: #fbb710;
            background: #fff;
        }

        .file-input-display i {
            font-size: 24px;
            color: #fbb710;
            margin-bottom: 10px;
        }

        .file-input-display p {
            margin: 0;
            color: #6b6b6b;
            font-size: 14px;
        }

        /* Contact CTA */
        .contact-cta {
            text-align: center;
            padding: 25px 20px;
        }

        .contact-cta p {
            margin-bottom: 20px;
            color: #6b6b6b;
            font-size: 16px;
            line-height: 1.6;
            font-weight: 500;
        }

        .contact-cta .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #fbb710;
            color: #fff;
            border: 2px solid #fbb710;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            font-size: 13px;
            width: 100%;
            max-width: 200px;
            margin: 0 auto;
        }

        .contact-cta .btn:hover {
            background: #242424;
            border-color: #242424;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(251, 183, 16, 0.3);
        }

        .contact-cta .btn i {
            font-size: 16px;
        }

        /* Responsive Enhancements */
        @media only screen and (max-width: 767px) {
            .custom-product-form {
                padding: 25px;
            }

            .custom-form-title h2 {
                font-size: 24px;
            }

            .reference-image-row {
                flex-direction: column;
                gap: 15px;
            }

            .reference-image-col {
                padding-right: 0;
            }

            .remove-reference-btn {
                align-self: flex-end;
            }

            /* Mobile sidebar cards remain normal positioned */
            .sidebar-card {
                margin-bottom: 20px;
                padding: 25px 20px;
            }

            .sidebar-card h5 {
                font-size: 18px;
            }

            .summary-table li {
                font-size: 14px;
                padding: 12px 0;
            }

            .contact-cta .btn {
                font-size: 12px;
                padding: 10px 20px;
                max-width: 180px;
            }

            .steps-container {
                order: 1;
            }

            .benefits-card {
                order: 2;
            }

            .contact-card {
                order: 3;
            }
        }

        /* Desktop sticky behavior */
        @media only screen and (min-width: 768px) {
            .sidebar-cards-container {
                position: sticky;
                top: 20px;
                height: fit-content;
            }

            .sidebar-card {
                overflow: hidden;
            }

            .contact-cta .btn {
                max-width: none;
                width: auto;
                display: inline-flex;
            }
        }

        /* Loading Animation */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection

@section('content')
    <div class="cart-table-area section-padding-100 clearfix">
        <div class="container-fluid custom-product-container">
            <div class="row">
                <div class="col-12 col-lg-8 form-section">
                    <div class="checkout_details_area mt-50 clearfix">
                        <div class="custom-form-title">
                            <h2><i class="fa fa-magic"></i> Permintaan Produk Kustom</h2>
                            <p>Wujudkan mebel impian Anda dengan desain yang unik dan berkualitas</p>
                        </div>

                        <div class="info-box">
                            <h4><i class="fa fa-info-circle"></i> Proses Pemesanan Kustom</h4>
                            <p>Isi formulir → Review & Penawaran → DP → Produksi → Update Progres → Pelunasan → Pengiriman
                            </p>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-triangle"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('custom.store') }}" method="POST" enctype="multipart/form-data"
                            class="custom-product-form" id="customProductForm">
                            @csrf
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="title"><i class="fa fa-tag"></i> Judul Permintaan <span>*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ old('title') }}" required
                                        placeholder="Contoh: Lemari Pakaian 3 Pintu Modern">
                                    @error('title')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="description"><i class="fa fa-align-left"></i> Deskripsi Produk
                                        <span>*</span></label>
                                    <textarea class="form-control" id="description" name="description" rows="4" required
                                        placeholder="Jelaskan dengan detail produk yang Anda inginkan...">{{ old('description') }}</textarea>
                                    <small class="form-text text-muted">
                                        <i class="fa fa-lightbulb-o"></i>
                                        Semakin detail deskripsi, semakin akurat penawaran kami.
                                    </small>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="specifications"><i class="fa fa-cogs"></i> Spesifikasi Teknis</label>
                                    <textarea class="form-control" id="specifications" name="specifications" rows="3"
                                        placeholder="Ukuran: 180x60x200 cm, Bahan: Kayu Jati, Warna: Natural, dll.">{{ old('specifications') }}</textarea>
                                    @error('specifications')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="budget"><i class="fa fa-money"></i> Perkiraan Budget (Rp)</label>
                                    <input type="number" class="form-control" id="budget" name="budget"
                                        value="{{ old('budget') }}" placeholder="5000000">
                                    @error('budget')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="desired_deadline"><i class="fa fa-calendar"></i> Deadline yang
                                        Diharapkan</label>
                                    <input type="date" class="form-control" id="desired_deadline" name="desired_deadline"
                                        value="{{ old('desired_deadline') }}">
                                    @error('desired_deadline')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label><i class="fa fa-image"></i> Gambar Referensi</label>
                                    <div class="reference-images">
                                        <div class="reference-image-row">
                                            <div class="reference-image-col">
                                                <div class="file-input-wrapper">
                                                    <input type="file" class="form-control" name="reference_images[]"
                                                        accept="image/*">
                                                    <div class="file-input-display">
                                                        <i class="fa fa-cloud-upload"></i>
                                                        <p>Klik untuk upload gambar referensi</p>
                                                    </div>
                                                </div>
                                                @error('reference_images.*')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="reference-image-col">
                                                <input type="text" class="form-control" name="reference_descriptions[]"
                                                    placeholder="Deskripsi gambar (opsional)">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" id="add-reference" class="btn btn-add-reference mt-3">
                                        <i class="fa fa-plus"></i> Tambah gambar referensi lain
                                    </button>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn amado-btn amado-btn-submit w-100">
                                        <i class="fa fa-paper-plane"></i> Kirim Permintaan Kustom
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-12 col-lg-4 benefits-section">
                    <div class="sidebar-cards-container">
                        <!-- Langkah-langkah Detail -->
                        <div class="steps-container">
                            <h5><i class="fa fa-list-ol"></i> Langkah Pemesanan</h5>
                            <div class="step-item">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h6>Isi Formulir</h6>
                                    <p>Lengkapi detail produk yang diinginkan</p>
                                </div>
                            </div>
                            <div class="step-item">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h6>Review & Penawaran</h6>
                                    <p>Tim ahli meninjau dan memberikan penawaran</p>
                                </div>
                            </div>
                            <div class="step-item">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h6>Pembayaran DP</h6>
                                    <p>Bayar DP untuk memulai produksi</p>
                                </div>
                            </div>
                            <div class="step-item">
                                <div class="step-number">4</div>
                                <div class="step-content">
                                    <h6>Produksi</h6>
                                    <p>Pengerjaan dengan update progres berkala</p>
                                </div>
                            </div>
                            <div class="step-item">
                                <div class="step-number">5</div>
                                <div class="step-content">
                                    <h6>Pelunasan & Kirim</h6>
                                    <p>Bayar sisanya dan produk dikirim</p>
                                </div>
                            </div>
                        </div>

                        <!-- Keuntungan -->
                        <div class="sidebar-card benefits-card">
                            <h5><i class="fa fa-star"></i> Keuntungan Produk Kustom</h5>
                            <ul class="summary-table">
                                <li>
                                    <i class="fa fa-check-circle"></i>
                                    <span>Desain sesuai keinginan Anda</span>
                                </li>
                                <li>
                                    <i class="fa fa-ruler"></i>
                                    <span>Ukuran presisi dengan ruangan</span>
                                </li>
                                <li>
                                    <i class="fa fa-diamond"></i>
                                    <span>Bahan berkualitas premium</span>
                                </li>
                                <li>
                                    <i class="fa fa-magic"></i>
                                    <span>Desain unik & eksklusif</span>
                                </li>
                                <li>
                                    <i class="fa fa-user-md"></i>
                                    <span>Konsultasi ahli gratis</span>
                                </li>
                                <li>
                                    <i class="fa fa-refresh"></i>
                                    <span>Update progres real-time</span>
                                </li>
                                <li>
                                    <i class="fa fa-shield"></i>
                                    <span>Garansi kualitas terjamin</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Contact -->
                        <div class="sidebar-card contact-card">
                            <h5><i class="fa fa-headphones"></i> Butuh Bantuan?</h5>
                            <div class="contact-cta">
                                <p>Tim customer service siap membantu Anda 24/7 untuk konsultasi dan pertanyaan</p>
                                <a href="tel:+62812345678" class="btn">
                                    <i class="fa fa-phone"></i>
                                    <span>Hubungi Kami</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Enhanced Add Reference functionality
            $('#add-reference').click(function() {
                const newRow = `
                <div class="reference-image-row" style="opacity: 0; transform: translateY(20px);">
                    <div class="reference-image-col">
                        <div class="file-input-wrapper">
                            <input type="file" class="form-control" name="reference_images[]" accept="image/*">
                            <div class="file-input-display">
                                <i class="fa fa-cloud-upload"></i>
                                <p>Klik untuk upload gambar referensi</p>
                            </div>
                        </div>
                    </div>
                    <div class="reference-image-col">
                        <input type="text" class="form-control" name="reference_descriptions[]" placeholder="Deskripsi gambar (opsional)">
                    </div>
                    <div class="remove-reference-btn">
                        <div class="remove-btn">
                            <i class="fa fa-trash"></i>
                        </div>
                    </div>
                </div>
            `;

                $('.reference-images').append(newRow);

                // Animate the new row
                const $newRow = $('.reference-image-row').last();
                $newRow.animate({
                    opacity: 1,
                    transform: 'translateY(0)'
                }, 300);

                // Add event listener for remove button
                $newRow.find('.remove-btn').click(function() {
                    $(this).closest('.reference-image-row').animate({
                        opacity: 0,
                        height: 0,
                        marginBottom: 0,
                        paddingTop: 0,
                        paddingBottom: 0
                    }, 300, function() {
                        $(this).remove();
                    });
                });
            });

            // File input display enhancement
            $(document).on('change', 'input[type="file"]', function() {
                const $fileInput = $(this);
                const $display = $fileInput.siblings('.file-input-display');
                const fileName = $fileInput.get(0).files[0]?.name;

                if (fileName) {
                    $display.html(`
                        <i class="fa fa-file-image-o" style="color: #28a745;"></i>
                        <p style="color: #28a745; font-weight: 500;">${fileName}</p>
                    `);
                    $display.css('border-color', '#28a745');
                }
            });

            // Form submission with loading animation
            $('#customProductForm').submit(function() {
                const $submitBtn = $(this).find('button[type="submit"]');
                $submitBtn.addClass('btn-loading');
                $submitBtn.find('i').removeClass('fa-paper-plane').addClass('fa-spinner fa-spin');
                $submitBtn.prop('disabled', true);
            });

            // Form validation enhancement
            $('input[required], textarea[required]').on('blur', function() {
                const $field = $(this);
                const $group = $field.closest('.form-group, .mb-3');

                if ($field.val().trim() === '') {
                    $field.addClass('is-invalid');
                    if (!$group.find('.invalid-feedback').length) {
                        $group.append('<div class="invalid-feedback">Field ini wajib diisi</div>');
                    }
                } else {
                    $field.removeClass('is-invalid').addClass('is-valid');
                    $group.find('.invalid-feedback').remove();
                }
            });

            // Auto-resize textarea
            $('textarea').each(function() {
                this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
            }).on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Smooth scroll to form elements
            $('label[for]').click(function() {
                const target = $('#' + $(this).attr('for'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 300);
                }
            });
        });
    </script>
@endpush
