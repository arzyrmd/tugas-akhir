@extends('layouts.app')

@section('styles')
    <style>
        /* Menggunakan styling dari template Amado */
        .shipping-form {
            margin-bottom: 30px;
        }

        .shipping-header {
            border-bottom: 2px solid #fbb710;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .shipping-header h2 {
            font-size: 30px;
            color: #242424;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .shipping-header p {
            color: #6d6d6d;
            font-size: 16px;
            margin-bottom: 0;
        }

        /* Form control sesuai template Amado */
        .form-control {
            height: 60px;
            border: none;
            border-radius: 0;
            background-color: #f5f7fa;
            padding: 30px;
            color: #6b6b6b;
            font-size: 14px;
            transition: all 500ms ease 0s;
        }

        .form-control:focus {
            background-color: #ffffff;
            border: 2px solid #fbb710;
            box-shadow: none;
            color: #242424;
        }

        textarea.form-control {
            height: 120px;
            resize: vertical;
        }

        /* Label styling */
        label {
            font-size: 14px;
            color: #242424;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        label span {
            color: #fbb710;
        }

        /* Alert styling sesuai Amado */
        .alert {
            padding: 20px;
            margin-bottom: 30px;
            border: none;
            border-radius: 0;
            border-left: 4px solid;
        }

        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-left-color: #20d34a;
        }

        .alert-info {
            color: #31708f;
            background-color: #d9edf7;
            border-left-color: #fbb710;
        }

        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-left-color: #dc0647;
        }

        /* Info box styling */
        .info-box {
            background-color: #f5f7fa;
            border-left: 4px solid #fbb710;
            padding: 20px;
            margin-bottom: 30px;
        }

        .info-box p {
            margin-bottom: 0;
            color: #6d6d6d;
        }

        .info-box strong {
            color: #242424;
        }

        /* Cart summary sesuai template Amado */
        .cart-summary {
            background-color: #f5f7fa;
            padding: 30px 20px;
            position: relative;
            z-index: 1;
        }

        .cart-summary h5 {
            font-size: 18px;
            margin-bottom: 30px;
            color: #242424;
            font-weight: 600;
        }

        .summary-table {
            margin: 0;
            padding: 0;
        }

        .summary-table li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
            color: #6b6b6b;
        }

        .summary-table li:last-child {
            margin-bottom: 0;
            padding-top: 15px;
            border-top: 1px solid #ebebeb;
        }

        .custom-price {
            font-weight: 600;
            color: #fbb710;
            font-size: 16px;
        }

        .shipping-price {
            font-weight: 600;
            font-size: 14px;
            color: #242424;
        }

        .total-price {
            font-weight: 700;
            color: #fbb710;
            font-size: 18px;
        }

        /* Shipping summary box */
        .shipping-summary {
            background-color: #ffffff;
            border: 1px solid #ebebeb;
            padding: 20px;
            margin-top: 30px;
        }

        .shipping-summary h6 {
            font-size: 16px;
            color: #242424;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .shipping-summary ul {
            margin: 0;
            padding: 0;
        }

        .shipping-summary ul li {
            list-style: none;
            color: #6d6d6d;
            font-size: 14px;
            margin-bottom: 8px;
            position: relative;
            padding-left: 15px;
        }

        .shipping-summary ul li:before {
            content: "•";
            color: #fbb710;
            position: absolute;
            left: 0;
        }

        /* Button styling sesuai Amado */
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

        /* Button secondary menggunakan style Amado */
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

        /* Nice select styling sesuai Amado - lebih rapi */
        .nice-select {
            border-radius: 0 !important;
            height: 60px !important;
            line-height: 60px !important;
            background-color: #f5f7fa !important;
            border: none !important;
            color: #6b6b6b !important;
            font-size: 14px !important;
            padding: 0 30px !important;
            position: relative;
            cursor: pointer;
            transition: all 500ms ease 0s;
            width: 100% !important;
            display: block !important;
            box-sizing: border-box;
        }

        .nice-select:focus,
        .nice-select.open {
            background-color: #ffffff !important;
            border: 2px solid #fbb710 !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .nice-select .current {
            color: #6b6b6b !important;
            font-size: 14px !important;
            line-height: 60px !important;
            height: 60px !important;
            display: block !important;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            padding-right: 40px;
        }

        .nice-select::after {
            border-color: #6b6b6b transparent transparent !important;
            right: 30px !important;
            top: 50% !important;
            margin-top: -2px !important;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 5px 5px 0 5px;
            position: absolute;
            pointer-events: none;
        }

        .nice-select.open::after {
            border-color: transparent transparent #6b6b6b !important;
            border-width: 0 5px 5px 5px !important;
            margin-top: -3px !important;
        }

        .nice-select .list {
            background-color: #ffffff !important;
            border-radius: 0 !important;
            border: 1px solid #ebebeb !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
            max-height: 200px !important;
            overflow-y: auto !important;
            z-index: 1000 !important;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .nice-select .option {
            color: #6b6b6b !important;
            font-size: 14px !important;
            padding: 12px 30px !important;
            line-height: 1.4 !important;
            transition: all 300ms ease 0s;
            cursor: pointer;
            display: block !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nice-select .option.selected {
            background-color: #fbb710 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }

        .nice-select .option.focus,
        .nice-select .option:hover {
            background-color: #f5f7fa !important;
            color: #242424 !important;
        }

        .nice-select .option.selected.focus,
        .nice-select .option.selected:hover {
            background-color: #fbb710 !important;
            color: #ffffff !important;
        }

        /* Select normal untuk fallback - lebih rapi */
        select.form-control {
            height: 60px !important;
            background-color: #f5f7fa !important;
            border: none !important;
            padding: 0 30px !important;
            color: #6b6b6b !important;
            font-size: 14px !important;
            border-radius: 0 !important;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'><path fill='%236b6b6b' d='M2 0L0 2h4zm0 5L0 3h4z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 30px center;
            background-size: 12px;
            cursor: pointer;
        }

        select.form-control:focus {
            background-color: #ffffff !important;
            border: 2px solid #fbb710 !important;
            box-shadow: none !important;
            color: #242424 !important;
            outline: none !important;
        }

        /* Perbaiki label spacing */
        .form-group label,
        .mb-3 label {
            margin-bottom: 8px !important;
            display: block;
        }

        /* Error message styling */
        .text-danger {
            color: #dc0647 !important;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        /* Form text styling */
        .form-text {
            color: #6d6d6d;
            font-size: 12px;
            margin-top: 5px;
        }

        /* Responsive adjustments */
        @media only screen and (max-width: 767px) {
            .cart-summary {
                margin-top: 50px;
            }

            .shipping-header h2 {
                font-size: 24px;
            }

            .amado-btn,
            .amado-btn-secondary {
                width: 100%;
                margin-bottom: 15px;
                min-width: auto;
            }

            .nice-select {
                padding: 0 20px;
            }

            .nice-select::after {
                right: 20px;
            }

            .nice-select .option {
                padding: 12px 20px;
            }
        }

        /* Section padding sesuai template */
        .section-padding-10 {
            padding: 50px 0;
        }
    </style>
@endsection

@section('content')
    <div class="cart-table-area section-padding-10">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="shipping-form mt-50">
                        <div class="shipping-header">
                            <h2>Informasi Pengiriman</h2>
                            <p>Produk kustom Anda: {{ $customRequest->title }}</p>
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

                        <div class="info-box mb-4">
                            <p><strong>Langkah Selanjutnya:</strong> Isi informasi pengiriman untuk dapat melanjutkan ke
                                pembayaran pelunasan.</p>
                        </div>

                        <form action="{{ route('custom.add-shipping', $customRequest->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name">Nama Lengkap <span>*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                        value="{{ old('full_name', auth()->user()->name ?? '') }}" required>
                                    @error('full_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email">Email <span>*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="phone">Nomor Telepon <span>*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="address">Alamat Lengkap <span>*</span></label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="province_id">Provinsi <span>*</span></label>
                                    <select class="form-control" id="province_id" name="province_id" required>
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->id }}"
                                                {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                                {{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('province_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="city_id">Kota/Kabupaten <span>*</span></label>
                                    <select class="form-control" id="city_id" name="city_id" required>
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                    </select>
                                    @error('city_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="postal_code">Kode Pos <span>*</span></label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code"
                                        value="{{ old('postal_code') }}" required>
                                    @error('postal_code')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <!-- Input biaya ongkir dihilangkan, akan dihitung otomatis -->
                                <input type="hidden" id="shipping_cost" name="shipping_cost" value="0">

                                <div class="col-12 mb-3">
                                    <label for="notes">Catatan Pengiriman</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="amado-btn w-100">Simpan & Lanjutkan ke Pembayaran</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="cart-summary">
                        <h5>Ringkasan Pesanan</h5>
                        <ul class="summary-table">
                            <li><span>Produk:</span> <span>{{ $customRequest->title }}</span></li>
                            <li><span>Harga Produk:</span> <span class="custom-price">Rp
                                    {{ number_format($customRequest->quoted_price, 0, ',', '.') }}</span></li>
                            <li><span>DP Terbayar:</span> <span>Rp
                                    {{ number_format($customRequest->down_payment, 0, ',', '.') }}</span></li>
                            <li><span>Sisa Pembayaran:</span> <span>Rp
                                    {{ number_format($customRequest->remaining_payment, 0, ',', '.') }}</span></li>
                            <li><span>Biaya Pengiriman:</span> <span class="shipping-price" id="summary-shipping">Rp
                                    0</span></li>
                            <li><span>Total Pembayaran:</span> <span class="total-price" id="summary-total">Rp
                                    {{ number_format($customRequest->remaining_payment, 0, ',', '.') }}</span></li>
                        </ul>

                        <div class="shipping-summary">
                            <h6>Informasi Penting</h6>
                            <ul>
                                <li>Pengiriman dilakukan setelah pelunasan</li>
                                <li>Pastikan alamat pengiriman sudah benar</li>
                                <li>Lama pengiriman tergantung lokasi (2-7 hari)</li>
                                <li>Pembatalan tidak dapat dilakukan pada tahap ini</li>
                            </ul>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('custom.show', $customRequest->id) }}"
                                class="amado-btn-secondary w-100">Kembali ke Detail Pesanan</a>
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
            // Function to format number
            function formatNumber(num) {
                return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            }

            // Function to update summary
            function updateSummary(shippingCost) {
                var remainingPayment = {{ $customRequest->remaining_payment }};
                var total = remainingPayment + shippingCost;

                $('#summary-shipping').text('Rp ' + formatNumber(shippingCost));
                $('#summary-total').text('Rp ' + formatNumber(total));
                $('#shipping_cost').val(shippingCost);
            }

            // Function to reinitialize select if you're using a plugin like nice-select
            function reinitializeDropdown(selector) {
                if ($.fn.niceSelect) {
                    try {
                        $(selector).niceSelect('destroy');
                    } catch (e) {
                        // Ignore if already destroyed
                    }

                    // Small delay to ensure DOM is ready
                    setTimeout(function() {
                        $(selector).niceSelect({
                            searchable: false,
                            placeholder: 'select'
                        });

                        // Force style application
                        $(selector + '_nice-select').css({
                            'height': '60px',
                            'line-height': '60px',
                            'background-color': '#f5f7fa',
                            'border': 'none',
                            'border-radius': '0',
                            'width': '100%'
                        });
                    }, 50);
                }
            }

            // Function to calculate shipping cost
            function calculateShipping(cityId) {
                if (cityId) {
                    // Show loading state
                    $('#summary-shipping').html('<i class="fa fa-spinner fa-spin"></i> Menghitung...');

                    $.ajax({
                        url: '{{ route('checkout.shipping') }}',
                        type: 'GET',
                        data: {
                            city_id: cityId
                        },
                        success: function(data) {
                            // Update shipping cost and total
                            updateSummary(data.shipping_cost);
                        },
                        error: function() {
                            $('#summary-shipping').text('Rp 0');
                            alert('Gagal menghitung biaya pengiriman. Silakan coba lagi.');
                        }
                    });
                } else {
                    // Reset shipping cost
                    updateSummary(0);
                }
            }

            // Load cities when province changes
            $('#province_id').change(function() {
                var provinceId = $(this).val();

                if (provinceId) {
                    // Show loading state
                    $('#city_id').html('<option value="">Loading...</option>');

                    // Reinitialize dropdown if using nice-select
                    if ($.fn.niceSelect) {
                        reinitializeDropdown('#city_id');
                    }

                    $.ajax({
                        url: '{{ route('checkout.cities') }}',
                        type: 'GET',
                        data: {
                            province_id: provinceId
                        },
                        dataType: 'json',
                        success: function(data) {
                            // Remove previous options
                            $('#city_id').empty();

                            // Add default option
                            $('#city_id').append(
                                '<option value="">-- Pilih Kota/Kabupaten --</option>');

                            // Add city options
                            if (data.cities && data.cities.length > 0) {
                                $.each(data.cities, function(index, city) {
                                    $('#city_id').append('<option value="' + city.id +
                                        '">' + city.name + '</option>');
                                });

                                // Reinitialize dropdown
                                if ($.fn.niceSelect) {
                                    setTimeout(function() {
                                        reinitializeDropdown('#city_id');
                                    }, 100);
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", error);

                            // Reset dropdown on error
                            $('#city_id').html(
                                '<option value="">-- Pilih Kota/Kabupaten --</option>');

                            if ($.fn.niceSelect) {
                                reinitializeDropdown('#city_id');
                            }
                        }
                    });
                } else {
                    $('#city_id').empty();
                    $('#city_id').append('<option value="">-- Pilih Kota/Kabupaten --</option>');

                    if ($.fn.niceSelect) {
                        setTimeout(function() {
                            reinitializeDropdown('#city_id');
                        }, 100);
                    }

                    // Reset shipping cost when province is cleared
                    updateSummary(0);
                }
            });

            // Calculate shipping cost when city changes
            $('#city_id').change(function() {
                var cityId = $(this).val();
                if (cityId) {
                    calculateShipping(cityId);
                } else {
                    updateSummary(0);
                }
            });

            // Load cities for initial province if already selected
            @if (old('province_id'))
                var oldProvinceId = {{ old('province_id') }};
                var oldCityId = {{ old('city_id') ?? 'null' }};

                if (oldProvinceId) {
                    $.ajax({
                        url: '{{ route('checkout.cities') }}',
                        type: 'GET',
                        data: {
                            province_id: oldProvinceId
                        },
                        dataType: 'json',
                        success: function(data) {
                            $('#city_id').empty();
                            $('#city_id').append(
                                '<option value="">-- Pilih Kota/Kabupaten --</option>');

                            if (data.cities && data.cities.length > 0) {
                                $.each(data.cities, function(index, city) {
                                    var selected = (city.id == oldCityId) ? 'selected' : '';
                                    $('#city_id').append('<option value="' + city.id + '" ' +
                                        selected + '>' + city.name + '</option>');
                                });

                                if ($.fn.niceSelect) {
                                    setTimeout(function() {
                                        reinitializeDropdown('#city_id');
                                    }, 100);
                                }

                                // Calculate shipping if city is selected
                                if (oldCityId) {
                                    calculateShipping(oldCityId);
                                }
                            }
                        }
                    });
                }
            @endif

            // Set up initial dropdown styling if you're using nice-select
            if ($.fn.niceSelect) {
                reinitializeDropdown('#province_id');
                reinitializeDropdown('#city_id');
            }

            // Initial update of summary
            updateSummary(0);
        });
    </script>
@endpush
