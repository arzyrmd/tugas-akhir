<!-- Notifikasi dari session jika ada -->
@if (session('success') || session('error') || session('warning') || session('info'))
    @php
        $type = session('success')
            ? 'success'
            : (session('error')
                ? 'error'
                : (session('warning')
                    ? 'warning'
                    : 'info'));
        $message = session('success') ?? (session('error') ?? (session('warning') ?? session('info')));
        $title = [
            'success' => 'Berhasil!',
            'error' => 'Gagal!',
            'warning' => 'Perhatian!',
            'info' => 'Informasi!',
        ][$type];
        $icon = [
            'success' =>
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
            'error' =>
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
            'warning' =>
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
            'info' =>
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
        ][$type];
        $colors = [
            'success' => '#fbb710',
            'error' => '#ff3d57',
            'warning' => '#ff9800',
            'info' => '#03a9f4',
        ][$type];
    @endphp

    <div id="appNotification" class="app-notification" data-type="{{ $type }}">
        <div class="notification-content">
            <div class="notification-icon" style="background-color: {{ $colors }}">
                {!! $icon !!}
            </div>
            <div class="notification-text">
                <h4>{{ $title }}</h4>
                <p>{{ $message }}</p>
            </div>
        </div>
        <div class="notification-progress" style="background-color: {{ $colors }}20">
            <div class="progress-bar" style="background-color: {{ $colors }}"></div>
        </div>
        <button class="notification-close">&times;</button>
    </div>
@endif

<!-- Container untuk notifikasi AJAX -->
<div id="notificationContainer"></div>

<style>
    #notificationContainer {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }

    .app-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        width: 350px;
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        animation: slideInNotif 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
        transform-origin: center right;
        border: 1px solid rgba(0, 0, 0, 0.05);
        margin-bottom: 10px;
    }

    .notification-content {
        display: flex;
        padding: 20px;
    }

    .notification-icon {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 16px;
        flex-shrink: 0;
        color: white;
    }

    .notification-icon svg {
        stroke: white;
        width: 24px;
        height: 24px;
    }

    .notification-text {
        flex-grow: 1;
    }

    .notification-text h4 {
        margin: 0 0 6px 0;
        font-size: 17px;
        font-weight: 600;
        color: #333;
        letter-spacing: -0.2px;
    }

    .notification-text p {
        margin: 0;
        font-size: 14px;
        color: #666;
        line-height: 1.5;
    }

    .notification-progress {
        height: 4px;
        width: 100%;
        position: relative;
    }

    .progress-bar {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        animation: progress 5s linear forwards;
    }

    .notification-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(0, 0, 0, 0.05);
        border: none;
        color: #777;
        font-size: 16px;
        cursor: pointer;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        padding: 0;
        line-height: 1;
        transition: all 0.2s;
    }

    .notification-close:hover {
        background-color: rgba(0, 0, 0, 0.1);
        color: #333;
        transform: scale(1.1);
    }

    @keyframes slideInNotif {
        0% {
            transform: translateX(120%) scale(0.9);
            opacity: 0;
        }

        70% {
            transform: translateX(-5%) scale(1.02);
            opacity: 1;
        }

        100% {
            transform: translateX(0) scale(1);
            opacity: 1;
        }
    }

    @keyframes progress {
        from {
            width: 100%;
        }

        to {
            width: 0%;
        }
    }

    @keyframes fadeOut {
        0% {
            transform: translateX(0) scale(1);
            opacity: 1;
        }

        100% {
            transform: translateX(100%) scale(0.9);
            opacity: 0;
        }
    }

    /* Responsive */
    @media (max-width: 576px) {

        .app-notification,
        #notificationContainer .app-notification {
            width: calc(100% - 30px);
            right: 15px;
            left: 15px;
        }
    }

    /* Animasi loading untuk tombol */
    .btn-loading {
        position: relative;
        pointer-events: none;
        opacity: 0.8;
    }

    .btn-loading:after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: calc(50% - 8px);
        left: calc(50% - 8px);
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Efek update pada baris tabel */
    .cart-item-row.updating {
        background-color: rgba(251, 183, 16, 0.1);
        transition: background-color 0.3s ease;
    }

    .cart-item-row.removing {
        transition: all 0.3s ease;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi notifikasi dari session
        const notification = document.getElementById('appNotification');
        if (notification) {
            setupNotificationEvents(notification);
        }

        // Fungsi untuk menampilkan notifikasi
        window.showNotification = function(type, message) {
            const notificationContainer = document.getElementById('notificationContainer');
            const notificationId = 'notification-' + Date.now();

            // Tentukan judul dan ikon berdasarkan tipe
            const titles = {
                'success': 'Berhasil!',
                'error': 'Gagal!',
                'warning': 'Perhatian!',
                'info': 'Informasi!'
            };

            const icons = {
                'success': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
                'error': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
                'warning': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
                'info': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
            };

            const colors = {
                'success': '#fbb710',
                'error': '#ff3d57',
                'warning': '#ff9800',
                'info': '#03a9f4'
            };

            const title = titles[type] || titles['info'];
            const icon = icons[type] || icons['info'];
            const color = colors[type] || colors['info'];

            // Buat elemen notifikasi
            const notification = document.createElement('div');
            notification.id = notificationId;
            notification.className = 'app-notification';
            notification.setAttribute('data-type', type);

            notification.innerHTML = `
                <div class="notification-content">
                    <div class="notification-icon" style="background-color: ${color}">
                        ${icon}
                    </div>
                    <div class="notification-text">
                        <h4>${title}</h4>
                        <p>${message}</p>
                    </div>
                </div>
                <div class="notification-progress" style="background-color: ${color}20">
                    <div class="progress-bar" style="background-color: ${color}"></div>
                </div>
                <button class="notification-close">&times;</button>
            `;

            // Tambahkan ke container
            notificationContainer.appendChild(notification);

            // Setup event listeners dan timer
            setupNotificationEvents(notification);
        };

        // Setup event listeners untuk notifikasi
        function setupNotificationEvents(notification) {
            const progressBar = notification.querySelector('.progress-bar');
            const duration = 3000; // 5 detik
            let timer;

            // Set timer untuk auto close
            timer = setTimeout(function() {
                closeNotification(notification);
            }, duration);

            // Close notification when clicking the close button
            const closeBtn = notification.querySelector('.notification-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    closeNotification(notification);
                    clearTimeout(timer);
                });
            }

            // Pause timer when hovering
            notification.addEventListener('mouseenter', function() {
                if (progressBar) {
                    progressBar.style.animationPlayState = 'paused';
                }
                clearTimeout(timer);
            });

            // Resume timer when mouse leaves
            notification.addEventListener('mouseleave', function() {
                if (progressBar) {
                    progressBar.style.animationPlayState = 'running';
                }
                timer = setTimeout(function() {
                    closeNotification(notification);
                }, duration / 2); // Half the original time since progress already started
            });
        }

        // Fungsi untuk menutup notifikasi
        function closeNotification(notification) {
            notification.style.animation = 'fadeOut 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards';
            setTimeout(function() {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }
    });
</script>
