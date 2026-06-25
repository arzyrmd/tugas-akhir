@extends('layouts.app')

@section('content')
    <div class="cart-table-area section-padding-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="cart-title mt-50">
                        <h2>Keranjang Belanja</h2>
                    </div>

                    @include('notifikasi.notifikasi')

                    <div class="cart-table clearfix">
                        @if ($cartItems->count() > 0)
                            <table class="table table-responsive" id="cart-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Nama</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cartItems as $index => $item)
                                        <tr class="cart-item-row" id="cart-item-{{ $item->id }}">
                                            <td class="cart_product_img">
                                                <a href="{{ route('products.show', $item->product->slug) }}">
                                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                                        alt="{{ $item->product->name }}">
                                                </a>
                                            </td>
                                            <td class="cart_product_desc">
                                                <h5>{{ $item->product->name }}</h5>
                                            </td>
                                            <td class="price">
                                                <span>Rp {{ number_format($item->product->price, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="qty">
                                                <div class="qty-btn d-flex">
                                                    <p>Item</p>
                                                    <div class="quantity">
                                                        <span class="qty-minus"
                                                            onclick="var effect = document.getElementById('qty{{ $index }}'); var qty = effect.value; if(!isNaN(qty) && qty > 1) { effect.value--; updateCartQuantity({{ $item->id }}, effect.value); } return false;">
                                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                                        </span>
                                                        <input type="number" class="qty-text" id="qty{{ $index }}"
                                                            step="1" min="1" max="{{ $item->product->stock }}"
                                                            name="quantity" value="{{ $item->quantity }}"
                                                            data-item-id="{{ $item->id }}">
                                                        <span class="qty-plus"
                                                            onclick="var effect = document.getElementById('qty{{ $index }}'); var qty = effect.value; if(!isNaN(qty) && qty < {{ $item->product->stock }}) { effect.value++; updateCartQuantity({{ $item->id }}, effect.value); } return false;">
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-item-btn"
                                                    data-item-id="{{ $item->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info mt-3" id="empty-cart-message">
                                Keranjang belanja Anda kosong. <a href="{{ route('products.index') }}">Belanja sekarang</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="cart-summary">
                        <h5>Total Keranjang</h5>
                        <ul class="summary-table">
                            <li><span>subtotal:</span> <span id="cart-subtotal">Rp
                                    {{ number_format($subtotal, 0, ',', '.') }}</span></li>
                            <li><span>total:</span> <span id="cart-total">Rp
                                    {{ number_format($total, 0, ',', '.') }}</span></li>
                        </ul>
                        @if ($cartItems->count() > 0)
                            <a href="{{ route('checkout.index') }}" class="btn amado-btn w-100"
                                id="checkout-btn">Checkout</a>
                        @else
                            <a href="{{ route('products.index') }}" class="btn amado-btn w-100">Continue Shopping</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Style untuk efek hover dengan geser ke kiri */
        .cart-item-row {
            transition: transform 0.3s ease, opacity 0.3s ease, height 0.3s ease;
        }

        .cart-item-row:hover {
            transform: translateX(-100px);
            /* Geser ke kiri saat hover */
            background-color: #f9f9f9;
        }

        /* Styling untuk tombol update */
        .update-cart-btn {
            background-color: #fbb710;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .update-cart-btn:hover {
            background-color: #e0a50c;
        }

        /* Efek removing */
        .removing {
            opacity: 0.5;
        }
    </style>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSRF token untuk Laravel
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Fungsi untuk memformat angka ke format Rupiah
            function formatRupiah(angka) {
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Fungsi untuk update kuantitas keranjang menggunakan AJAX
            window.updateCartQuantity = function(itemId, quantity) {
                // Dapatkan row yang sedang diupdate
                const row = document.getElementById('cart-item-' + itemId);
                row.classList.add('updating');

                // Buat form data dengan method spoofing untuk PUT
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('quantity', quantity);

                fetch(`{{ url('cart/update') }}/${itemId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Tampilkan notifikasi
                        if (window.showNotification && data.message) {
                            window.showNotification(data.type, data.message);
                        }

                        // Update total keranjang
                        document.getElementById('cart-subtotal').textContent = formatRupiah(data.subtotal);
                        document.getElementById('cart-total').textContent = formatRupiah(data.total);

                        // Update jumlah item di keranjang (jika ada)
                        if (data.cart_count !== undefined) {
                            // Update cart count di menu nav
                            const cartCountElements = document.querySelectorAll('.cart-count');
                            cartCountElements.forEach(element => {
                                element.textContent = '(' + data.cart_count + ')';
                            });
                        }

                        // Hilangkan class updating
                        setTimeout(function() {
                            row.classList.remove('updating');
                        }, 500);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (window.showNotification) {
                            window.showNotification('error',
                                'Terjadi kesalahan saat mengupdate keranjang.');
                        }
                        row.classList.remove('updating');
                    });
            }

            // Event listener untuk tombol remove item
            const removeButtons = document.querySelectorAll('.remove-item-btn');

            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-item-id');
                    const row = document.getElementById('cart-item-' + itemId);

                    // Tampilkan konfirmasi jika diperlukan
                    if (!confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
                        return;
                    }

                    // Tambahkan class removing untuk efek visual
                    row.classList.add('removing');

                    // Buat form data dengan method spoofing untuk DELETE
                    const formData = new FormData();
                    formData.append('_method', 'DELETE');

                    fetch(`{{ url('cart/remove') }}/${itemId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Tampilkan notifikasi
                            if (window.showNotification && data.message) {
                                window.showNotification(data.type, data.message);
                            }

                            // Animasi hapus row
                            row.style.height = row.offsetHeight + 'px';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(30px)';

                            setTimeout(() => {
                                // Hapus row
                                row.remove();

                                // Update total keranjang
                                document.getElementById('cart-subtotal').textContent =
                                    formatRupiah(data.subtotal);
                                document.getElementById('cart-total').textContent =
                                    formatRupiah(data.total);

                                // Update jumlah item di keranjang (jika ada)
                                if (data.cart_count !== undefined) {
                                    // Update cart count di menu nav
                                    const cartCountElements = document.querySelectorAll(
                                        '.cart-count');
                                    cartCountElements.forEach(element => {
                                        element.textContent = '(' + data
                                            .cart_count + ')';
                                    });
                                }

                                // Jika keranjang kosong, tampilkan pesan
                                if (data.empty_cart) {
                                    const table = document.getElementById('cart-table');
                                    table.style.display = 'none';

                                    const cartTableDiv = document.querySelector(
                                        '.cart-table');
                                    const emptyMessage = document.createElement('div');
                                    emptyMessage.className = 'alert alert-info mt-3';
                                    emptyMessage.id = 'empty-cart-message';
                                    emptyMessage.innerHTML =
                                        'Keranjang belanja Anda kosong. <a href="{{ route('products.index') }}">Belanja sekarang</a>';

                                    cartTableDiv.appendChild(emptyMessage);

                                    // Update tombol checkout menjadi continue shopping
                                    const checkoutBtn = document.getElementById(
                                        'checkout-btn');
                                    checkoutBtn.href = '{{ route('products.index') }}';
                                    checkoutBtn.textContent = 'Continue Shopping';
                                }
                            }, 300);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            if (window.showNotification) {
                                window.showNotification('error',
                                    'Terjadi kesalahan saat menghapus item.');
                            }
                            row.classList.remove('removing');
                        });
                });
            });

            // Jika Anda ingin kuantitas langsung diupdate saat input berubah
            const qtyInputs = document.querySelectorAll('.qty-text');
            qtyInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const itemId = this.getAttribute('data-item-id');
                    updateCartQuantity(itemId, this.value);
                });
            });
        });
    </script>
@endsection
