@extends('layouts.app')
@section('content')
    <div class="cart-table-area section-padding-10">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="checkout_details_area mt-50 clearfix">

                        <div class="cart-title">
                            <h2>Informasi Pengiriman</h2>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('checkout.process') }}" method="post" id="checkout-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" name="first_name" id="first_name"
                                        value="{{ old('first_name') }}" placeholder="Nama Depan" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" name="last_name" id="last_name"
                                        value="{{ old('last_name') }}" placeholder="Nama Belakang" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <input type="text" class="form-control" name="full_name" id="full_name"
                                        placeholder="Nama lengkap" value="{{ old('full_name') }}">
                                </div>
                                <div class="col-12 mb-3">
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="Email" value="{{ old('email', Auth::user()->email ?? '') }}" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <select class="w-100" name="province_id" id="province_id" required>
                                        <option value="">Pilih Provinsi</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->id }}"
                                                {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                                {{ $province->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <select class="w-100" name="city_id" id="city_id" required>
                                        <option value="">Pilih Kota / Kabupaten</option>
                                        <!-- Akan diisi dengan AJAX -->
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <input type="text" class="form-control mb-3" name="address" id="address"
                                        placeholder="Alamat Lengkap" value="{{ old('address') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" name="postal_code" id="postal_code"
                                        placeholder="Kode Pos" value="{{ old('postal_code') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="number" class="form-control" name="phone" id="phone" min="0"
                                        placeholder="No Handphone" value="{{ old('phone') }}" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <textarea name="notes" class="form-control w-100" id="notes" cols="30" rows="10"
                                        placeholder="Catatan untuk pesanan Anda">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="cart-summary">
                        <h5>Total Keranjang Belanja</h5>
                        <ul class="summary-table">
                            <li><span>subtotal:</span> <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></li>
                            <li><span>Biaya Pengiriman:</span> <span id="shipping-cost">Rp 0</span></li>
                            <li><span>total:</span> <span id="total-cost">Rp
                                    {{ number_format($total, 0, ',', '.') }}</span></li>
                        </ul>
                        <div class="cart-btn mt-100">
                            <button type="button" id="submit-checkout" class="btn amado-btn w-100">Checkout</button>
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

            // Fungsi untuk menginisialisasi ulang dropdown dengan Nice Select
            function reinitializeDropdown(selector) {
                if ($.fn.niceSelect) {
                    $(selector).niceSelect('destroy');
                    $(selector).niceSelect();

                    // Tambahkan scrollbar ke dropdown
                    setTimeout(function() {
                        $('.nice-select .list').css({
                            'max-height': '200px',
                            'overflow-y': 'auto'
                        });
                    }, 50);
                }
            }

            // Reset validation on input
            $('#checkout-form input, #checkout-form select').on('change keyup', function() {
                if ($(this).val()) {
                    $(this).removeClass('is-invalid');

                    // Reset style untuk nice-select
                    if ($(this).is('select') && $(this).next('.nice-select').length) {
                        $(this).next('.nice-select').removeClass('is-invalid-select');
                    }
                }
            });

            // Validate form dan submit form ketika tombol checkout diklik
            $('#submit-checkout').click(function(e) {
                e.preventDefault(); // Mencegah default behavior

                // Validasi form di sisi client
                var isValid = true;

                // Check required fields
                $('#checkout-form input[required], #checkout-form select[required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');

                        // Jika elemen adalah select dengan nice-select
                        if ($(this).is('select') && $(this).next('.nice-select').length) {
                            $(this).next('.nice-select').addClass('is-invalid-select');
                        }
                    } else {
                        $(this).removeClass('is-invalid');

                        // Reset style untuk nice-select
                        if ($(this).is('select') && $(this).next('.nice-select').length) {
                            $(this).next('.nice-select').removeClass('is-invalid-select');
                        }
                    }
                });

                if (isValid) {
                    // Debug: pastikan form method dan action benar
                    console.log('Form method:', $('#checkout-form').attr('method'));
                    console.log('Form action:', $('#checkout-form').attr('action'));

                    // Disable tombol untuk mencegah klik ganda
                    $(this).prop('disabled', true);
                    $(this).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');

                    // Pastikan form memiliki method POST
                    $('#checkout-form').attr('method', 'POST');

                    // Submit form
                    $('#checkout-form').submit();
                } else {
                    // Scroll ke field pertama yang error
                    var firstError = $('#checkout-form .is-invalid').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }

                    alert('Harap lengkapi semua field yang diperlukan!');
                }
            });

            // Mengupdate dropdown kota berdasarkan provinsi yang dipilih
            $('#province_id').change(function() {
                var provinceId = $(this).val();
                console.log("Provinsi dipilih:", provinceId);

                if (provinceId) {
                    // Tampilkan loading state
                    $('#city_id').html('<option value="">Loading...</option>');
                    reinitializeDropdown('#city_id');

                    $.ajax({
                        url: '{{ route('checkout.cities') }}',
                        type: 'GET',
                        data: {
                            province_id: provinceId
                        },
                        dataType: 'json',
                        success: function(data) {
                            console.log("Data yang diterima:", data);

                            // Hapus opsi sebelumnya
                            $('#city_id').empty();

                            // Tambahkan opsi default
                            $('#city_id').append(
                                '<option value="">Pilih Kota / Kabupaten </option>');

                            // Tambahkan opsi kota
                            if (data.cities && data.cities.length > 0) {
                                $.each(data.cities, function(index, city) {
                                    $('#city_id').append('<option value="' + city.id +
                                        '">' + city.name + '</option>');
                                    console.log("Menambahkan kota:", city.name);
                                });

                                // Cek native select content
                                console.log("Isi select setelah update:", $('#city_id').html());

                                // Inisialisasi ulang dropdown
                                setTimeout(function() {
                                    reinitializeDropdown('#city_id');
                                }, 100);
                            } else {
                                console.log("Tidak ada data kota diterima");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", error);
                            console.log("Status:", status);
                            console.log("Response:", xhr.responseText);

                            // Reset dropdown pada error
                            $('#city_id').html(
                                '<option value="">Pilih Kota / Kabupaten</option>');
                            reinitializeDropdown('#city_id');
                        },
                        complete: function() {
                            // Coba update dropdown lagi setelah selesai
                            setTimeout(function() {
                                reinitializeDropdown('#city_id');
                            }, 200);
                        }
                    });
                } else {
                    $('#city_id').empty();
                    $('#city_id').append('<option value="">Pilih Kota / Kabupaten</option>');
                    $('#shipping-cost').text('Rp 0');

                    // Inisialisasi ulang dropdown
                    setTimeout(function() {
                        reinitializeDropdown('#city_id');
                    }, 100);
                }
            });

            // Menghitung ongkir berdasarkan kota yang dipilih
            $('#city_id').change(function() {
                var cityId = $(this).val();
                if (cityId) {
                    // Tampilkan loading state
                    $('#shipping-cost').html('<i class="fa fa-spinner fa-spin"></i>');

                    $.ajax({
                        url: '{{ route('checkout.shipping') }}',
                        type: 'GET',
                        data: {
                            city_id: cityId
                        },
                        success: function(data) {
                            $('#shipping-cost').text(data.formatted_shipping);
                            $('#total-cost').text(data.formatted_total);
                        },
                        error: function() {
                            $('#shipping-cost').text('Rp 0');
                            alert('Gagal menghitung biaya pengiriman. Silakan coba lagi.');
                        }
                    });
                } else {
                    $('#shipping-cost').text('Rp 0');
                }
            });

            // Inisialisasi dropdown awal
            reinitializeDropdown('#province_id');
            reinitializeDropdown('#city_id');

            // Atur scrollbar untuk dropdown yang sudah ada
            $('.nice-select .list').css({
                'max-height': '200px',
                'overflow-y': 'auto'
            });

            // Tambahkan css untuk validasi
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    .is-invalid {
                        border-color: #dc3545 !important;
                        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
                    }
                    .is-invalid-select {
                        border-color: #dc3545 !important;
                        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
                    }
                `)
                .appendTo('head');
        });
    </script>
@endpush
