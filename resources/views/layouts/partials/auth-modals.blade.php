@include('layouts.partials.html')
{{-- JavaScript untuk Auto-show Modal - CLEANED VERSION --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============= CONFIGURATION =============
        const MODAL_CONFIG = {
            login: {
                id: 'loginModal',
                route: '{{ route('login') }}',
                routeName: 'login',
                errorFlag: 'login_attempt',
                controllerVar: 'show_login_modal'
            },
            register: {
                id: 'registerModal',
                route: '{{ route('register') }}',
                routeName: 'register',
                errorFlag: 'register_attempt',
                controllerVar: 'show_register_modal'
            },
            forgotPassword: {
                id: 'forgotPasswordModal',
                route: '{{ route('password.request') }}',
                routeName: 'password.request',
                errorFlag: 'forgot_password_attempt',
                controllerVar: 'show_forgot_password_modal',
                hasSuccess: true
            },
            resetPassword: {
                id: 'resetPasswordModal',
                route: null,
                routeName: 'password.reset',
                errorFlag: 'reset_password_attempt',
                controllerVar: 'show_reset_password_modal'
            },
            verifyEmail: {
                id: 'verifyEmailModal',
                route: '{{ route('verification.notice') }}',
                routeName: 'verification.notice',
                errorFlag: 'verify_email_attempt',
                controllerVar: 'show_verify_email_modal',
                hasSuccess: true
            }
        };

        const FORM_CONFIG = {
            loginForm: {
                loading: '<i class="fa fa-spinner fa-spin me-2"></i>{{ __('Logging in...') }}',
                original: '<i class="fa fa-sign-in me-2"></i>{{ __('Log in') }}'
            },
            registerForm: {
                loading: '<i class="fa fa-spinner fa-spin me-2"></i>{{ __('Creating Account...') }}',
                original: '<i class="fa fa-user-plus me-2"></i>{{ __('Create Account') }}'
            },
            forgotPasswordForm: {
                loading: '<i class="fa fa-spinner fa-spin me-2"></i>{{ __('Sending...') }}',
                original: '<i class="fa fa-paper-plane me-2"></i>{{ __('Send Password Reset Link') }}'
            },
            resetPasswordForm: {
                loading: '<i class="fa fa-spinner fa-spin me-2"></i>{{ __('Resetting...') }}',
                original: '<i class="fa fa-save me-2"></i>{{ __('Reset Password') }}'
            },
            resendVerificationForm: {
                loading: '<i class="fa fa-spinner fa-spin me-2"></i>{{ __('Sending...') }}',
                original: '<i class="fa fa-paper-plane me-2"></i>{{ __('Resend Verification Email') }}'
            }
        };

        // ============= GLOBAL VARIABLES =============
        let modals = {};
        let isModalSwitching = false;
        const currentRoute = '{{ Route::currentRouteName() }}';

        // ============= VALIDATION & SUCCESS FLAGS =============
        const validationFlags = {
            login: {{ old('login_attempt') && $errors->any() ? 'true' : 'false' }},
            register: {{ old('register_attempt') && $errors->any() ? 'true' : 'false' }},
            forgotPassword: {{ old('forgot_password_attempt') && $errors->any() ? 'true' : 'false' }},
            resetPassword: {{ old('reset_password_attempt') && $errors->any() ? 'true' : 'false' }},
            verifyEmail: {{ old('verify_email_attempt') && $errors->any() ? 'true' : 'false' }}
        };

        const successFlags = {
            hasMessage: {{ session('status') ? 'true' : 'false' }},
            message: {!! session('status') ? '"' . addslashes(session('status')) . '"' : 'null' !!},
            forgotPassword: {{ session('show_forgot_password_modal') ? 'true' : 'false' }},
            verifyEmail: {{ session('show_verify_email_modal') || session('verification_email_sent') ? 'true' : 'false' }}
        };

        // ============= BOOTSTRAP INITIALIZATION =============
        function waitForBootstrap() {
            return new Promise((resolve) => {
                function checkBootstrap() {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        resolve();
                    } else {
                        setTimeout(checkBootstrap, 50);
                    }
                }
                checkBootstrap();
            });
        }

        // ============= MODAL UTILITIES =============
        function initializeModals() {
            Object.keys(MODAL_CONFIG).forEach(key => {
                const config = MODAL_CONFIG[key];
                const element = document.getElementById(config.id);

                if (element) {
                    modals[key] = {
                        instance: new bootstrap.Modal(element, {
                            backdrop: 'static',
                            keyboard: false
                        }),
                        element: element,
                        config: config
                    };
                }
            });
        }

        function shouldPreventModalClose(modalKey) {
            const hasError = validationFlags[modalKey];
            const hasSuccess = modalKey === 'forgotPassword' ?
                (successFlags.hasMessage || successFlags.forgotPassword) && currentRoute ===
                'password.request' :
                modalKey === 'verifyEmail' ?
                successFlags.verifyEmail && currentRoute === 'verification.notice' : false;

            return (hasError || hasSuccess) && !isModalSwitching;
        }

        function hideAllModals() {
            Object.keys(modals).forEach(key => {
                if (modals[key] && !shouldPreventModalClose(key)) {
                    modals[key].instance.hide();
                }
            });
        }

        function showModalSafely(modalKey, route = null) {
            if (!modals[modalKey]) return;

            isModalSwitching = true;
            hideAllModals();

            setTimeout(() => {
                modals[modalKey].instance.show();
                if (route) {
                    window.history.pushState({}, '', route);
                }
                isModalSwitching = false;
            }, 300);
        }

        function setupModalCloseHandlers() {
            Object.keys(modals).forEach(key => {
                const modal = modals[key];
                if (!modal) return;

                const preventClose = (e) => {
                    if (shouldPreventModalClose(key)) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                };

                // Close buttons
                modal.element.querySelectorAll('.btn-close, [data-bs-dismiss="modal"]')
                    .forEach(btn => btn.addEventListener('click', preventClose));

                // Backdrop clicks
                modal.element.addEventListener('click', function(e) {
                    if (e.target === this) preventClose(e);
                });

                // Escape key
                modal.element.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') preventClose(e);
                });

                // Focus first input when modal opens
                modal.element.addEventListener('shown.bs.modal', function() {
                    const firstInput = this.querySelector(
                        'input:not([type="hidden"]):not([disabled]):not([readonly])');
                    if (firstInput) {
                        setTimeout(() => firstInput.focus(), 100);
                    }
                });
            });
        }

        // ============= AUTO SHOW MODAL LOGIC =============
        function autoShowModal() {
            // Priority 1: Validation errors
            for (const [key, hasError] of Object.entries(validationFlags)) {
                if (hasError && modals[key]) {
                    console.log(`Showing ${key} modal due to validation error`);
                    showModalSafely(key, modals[key].config.route);
                    return;
                }
            }

            // Priority 2: Success messages
            if (successFlags.verifyEmail && modals.verifyEmail && currentRoute === 'verification.notice') {
                console.log('Showing verify email modal due to success');
                showModalSafely('verifyEmail', modals.verifyEmail.config.route);
                return;
            }

            if ((successFlags.hasMessage || successFlags.forgotPassword) &&
                modals.forgotPassword && currentRoute === 'password.request') {
                console.log('Showing forgot password modal due to success');
                showModalSafely('forgotPassword', modals.forgotPassword.config.route);
                return;
            }

            // Priority 3: Controller variables
            @foreach (['login', 'register', 'forgotPassword', 'resetPassword', 'verifyEmail'] as $modal)
                @if (isset(
                        ${'show_' .
                            ($modal === 'forgotPassword' ? 'forgot_password' : ($modal === 'verifyEmail' ? 'verify_email' : $modal)) .
                            '_modal'}) &&
                        ${'show_' .
                            ($modal === 'forgotPassword' ? 'forgot_password' : ($modal === 'verifyEmail' ? 'verify_email' : $modal)) .
                            '_modal'})
                    if (modals.{{ $modal }}) {
                        console.log('Showing {{ $modal }} modal from controller variable');
                        showModalSafely('{{ $modal }}', modals.{{ $modal }}.config.route);
                        return;
                    }
                @endif
            @endforeach

            // Priority 4: Current route
            Object.keys(modals).forEach(key => {
                const modal = modals[key];
                if (modal && currentRoute === modal.config.routeName) {
                    console.log(`Showing ${key} modal based on route`);
                    showModalSafely(key);
                }
            });
        }

        // ============= EVENT HANDLERS =============
        function setupModalSwitching() {
            document.addEventListener('click', function(e) {
                const switchLink = e.target.closest('.modal-switch-link');
                if (!switchLink) return;

                e.preventDefault();
                e.stopPropagation();

                const href = switchLink.getAttribute('href');
                console.log('Modal switch link clicked:', href);

                // Find matching modal by route
                Object.keys(modals).forEach(key => {
                    const modal = modals[key];
                    if (modal && (href.includes(modal.config.routeName) || href === modal.config
                            .route)) {
                        showModalSafely(key, modal.config.route);
                    }
                });
            });
        }

        function setupExternalTriggers() {
            const triggerMap = {
                '.login-link, [data-bs-target="#loginModal"]': 'login',
                '.register-link, [data-bs-target="#registerModal"]': 'register',
                '.forgot-password-link, [data-bs-target="#forgotPasswordModal"]': 'forgotPassword',
                '.reset-password-link, [data-bs-target="#resetPasswordModal"]': 'resetPassword'
            };

            document.addEventListener('click', function(e) {
                Object.entries(triggerMap).forEach(([selector, modalKey]) => {
                    if (e.target.closest(selector)) {
                        e.preventDefault();
                        if (modals[modalKey]) {
                            showModalSafely(modalKey, modals[modalKey].config.route);
                        }
                    }
                });
            });
        }

        function setupFormSubmission() {
            Object.entries(FORM_CONFIG).forEach(([formId, config]) => {
                const form = document.getElementById(formId);
                if (!form) return;

                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (!submitBtn || submitBtn.disabled) return;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = config.loading;

                    // Special handling for forgot password form
                    if (formId === 'forgotPasswordForm') {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'keep_modal_open';
                        hiddenInput.value = '1';
                        this.appendChild(hiddenInput);
                    }

                    // Reset button after timeout
                    setTimeout(() => {
                        if (submitBtn.disabled) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = config.original;
                        }
                    }, 5000);
                });
            });
        }

        function setupSuccessMessageHandling() {
            const handleSuccessMessage = (modalKey, condition) => {
                if (!condition || !modals[modalKey]) return;

                setTimeout(() => {
                    const successAlert = modals[modalKey].element.querySelector('.alert-success');
                    if (successAlert) {
                        successAlert.style.transition = 'opacity 0.5s ease';
                        successAlert.style.opacity = '0';
                        setTimeout(() => {
                            successAlert.remove();
                            if (modalKey === 'forgotPassword') {
                                successFlags.hasMessage = false;
                            } else if (modalKey === 'verifyEmail') {
                                successFlags.verifyEmail = false;
                            }
                        }, 500);
                    }
                }, 5000);
            };

            handleSuccessMessage('forgotPassword',
                (successFlags.hasMessage || successFlags.forgotPassword) && currentRoute ===
                'password.request');
            handleSuccessMessage('verifyEmail',
                successFlags.verifyEmail && currentRoute === 'verification.notice');
        }

        function setupBrowserNavigation() {
            window.addEventListener('popstate', function() {
                console.log('Browser back/forward button pressed');

                const shouldHideAll = !Object.values(validationFlags).some(flag => flag) &&
                    !successFlags.hasMessage &&
                    !successFlags.forgotPassword &&
                    !successFlags.verifyEmail;

                if (shouldHideAll) {
                    hideAllModals();
                }
            });
        }

        // ============= INITIALIZATION =============
        waitForBootstrap().then(function() {
            console.log('Bootstrap ready, initializing modals...');

            initializeModals();
            setupModalCloseHandlers();
            setupModalSwitching();
            setupExternalTriggers();
            setupFormSubmission();
            setupSuccessMessageHandling();
            setupBrowserNavigation();

            setTimeout(autoShowModal, 100);

            console.log('Modal system fully initialized');
            console.log('Current route:', currentRoute);
            console.log('Validation flags:', validationFlags);
            console.log('Success flags:', successFlags);
        });
    });
    document.addEventListener('DOMContentLoaded', () => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.add('fade-out');
                setTimeout(() => {
                    alert.remove();
                }, 1000); // waktu tunggu sama dengan durasi fade-out
            }, 5000);
        });
    });
</script>
