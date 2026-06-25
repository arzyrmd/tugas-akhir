{{-- LOGIN MODAL --}}
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">
                    <i class="fa fa-sign-in me-2"></i>{{ __('Login') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">{{ __('Welcome back! Please login to your account') }}</p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <input type="hidden" name="login_attempt" value="1">

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="modal_login_email" class="form-label">{{ __('Email') }}</label>
                        <input id="modal_login_email" name="email" type="email"
                            class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                            required autofocus autocomplete="username" placeholder="your.email@example.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="modal_login_password" class="form-label">{{ __('Password') }}</label>
                        <input id="modal_login_password" name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" required
                            autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="modal_remember_me" name="remember">
                            <label class="form-check-label" for="modal_remember_me">{{ __('Remember me') }}</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-muted text-decoration-none modal-switch-link">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="loginForm" class="btn btn-modal-primary w-100">
                    <i class="fa fa-sign-in me-2"></i>{{ __('Log in') }}
                </button>
                <div class="text-center mt-3 w-100">
                    <p class="mb-0">{{ __("Don't have an account?") }}
                        <a href="{{ route('register') }}" class="modal-switch-link">
                            {{ __('Register here') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- REGISTER MODAL --}}
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">
                    <i class="fa fa-user-plus me-2"></i>{{ __('Create Account') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">{{ __('Join us to start shopping for quality furniture') }}</p>

                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf
                    <input type="hidden" name="register_attempt" value="1">

                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6 mb-3">
                            <label for="modal_register_name" class="form-label">{{ __('Full Name') }}</label>
                            <input id="modal_register_name" name="name" type="text"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required autofocus autocomplete="name" placeholder="Enter your full name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="col-md-6 mb-3">
                            <label for="modal_register_email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="modal_register_email" name="email" type="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                required autocomplete="username" placeholder="your.email@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-6 mb-3">
                            <label for="modal_register_phone" class="form-label">{{ __('Phone Number') }}</label>
                            <input id="modal_register_phone" name="phone" type="tel"
                                class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}"
                                required autocomplete="tel" placeholder="08xxxxxxxxxx">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="col-md-6 mb-3">
                            <label for="modal_register_address"
                                class="form-label">{{ __('Delivery Address') }}</label>
                            <input id="modal_register_address" name="address" type="text"
                                class="form-control @error('address') is-invalid @enderror"
                                value="{{ old('address') }}" required autocomplete="street-address"
                                placeholder="Your delivery address">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-md-6 mb-3">
                            <label for="modal_register_password" class="form-label">{{ __('Password') }}</label>
                            <input id="modal_register_password" name="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" required
                                autocomplete="new-password" placeholder="Min. 8 characters">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6 mb-3">
                            <label for="modal_password_confirmation"
                                class="form-label">{{ __('Confirm Password') }}</label>
                            <input id="modal_password_confirmation" name="password_confirmation" type="password"
                                class="form-control" required autocomplete="new-password"
                                placeholder="Re-enter your password">
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="terms_agreement" required>
                            <label class="form-check-label" for="terms_agreement">
                                {{ __('I agree to the') }}
                                <a href="#" class="text-decoration-none">{{ __('Terms & Conditions') }}</a>
                                {{ __('and') }}
                                <a href="#" class="text-decoration-none">{{ __('Privacy Policy') }}</a>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="registerForm" class="btn btn-modal-primary w-100">
                    <i class="fa fa-user-plus me-2"></i>{{ __('Create Account') }}
                </button>
                <div class="text-center mt-3 w-100">
                    <p class="mb-0">{{ __('Already have an account?') }}
                        <a href="{{ route('login') }}" class="modal-switch-link">
                            {{ __('Login here') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FORGOT PASSWORD MODAL --}}
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">
                    <i class="fa fa-key me-2"></i>{{ __('Forgot Password') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">
                    {{ __('Enter your email address and we will send you a password reset link') }}</p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
                    @csrf
                    <input type="hidden" name="forgot_password_attempt" value="1">

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="modal_forgot_email" class="form-label">{{ __('Email Address') }}</label>
                        <input id="modal_forgot_email" name="email" type="email"
                            class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                            required autofocus autocomplete="username" placeholder="your.email@example.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="forgotPasswordForm" class="btn btn-modal-primary w-100">
                    <i class="fa fa-paper-plane me-2"></i>{{ __('Send Password Reset Link') }}
                </button>
                <div class="text-center mt-3 w-100">
                    <p class="mb-0">{{ __('Remember your password?') }}
                        <a href="{{ route('login') }}" class="modal-switch-link">
                            {{ __('Back to login') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- RESET PASSWORD MODAL --}}
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">
                    <i class="fa fa-lock me-2"></i>{{ __('Reset Password') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">{{ __('Create a new secure password for your account') }}</p>

                <form method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
                    @csrf
                    <input type="hidden" name="reset_password_attempt" value="1">

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token"
                        value="{{ isset($reset_token) ? $reset_token : request()->route('token') }}">

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="modal_reset_email" class="form-label">{{ __('Email Address') }}</label>
                        <input id="modal_reset_email" name="email" type="email" class="form-control bg-light"
                            value="{{ old('email', isset($reset_email) ? $reset_email : request()->get('email', '')) }}"
                            required readonly>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="modal_reset_password" class="form-label">{{ __('New Password') }}</label>
                        <input id="modal_reset_password" name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" required
                            autocomplete="new-password" placeholder="Enter your new password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small
                            class="form-text text-muted">{{ __('Password must be at least 8 characters and contain letters, numbers, and special characters.') }}</small>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="modal_password_confirmation_reset"
                            class="form-label">{{ __('Confirm Password') }}</label>
                        <input id="modal_password_confirmation_reset" name="password_confirmation" type="password"
                            class="form-control" required autocomplete="new-password"
                            placeholder="Re-enter your new password">
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="resetPasswordForm" class="btn btn-modal-primary w-100">
                    <i class="fa fa-save me-2"></i>{{ __('Reset Password') }}
                </button>
                <div class="text-center mt-3 w-100">
                    <a href="{{ route('login') }}" class="text-muted modal-switch-link">
                        {{ __('Return to login') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- EMAIL VERIFICATION MODAL --}}
<div class="modal fade" id="verifyEmailModal" tabindex="-1" aria-labelledby="verifyEmailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verifyEmailModalLabel">
                    <i class="fa fa-envelope me-2"></i>{{ __('Verify Your Email') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fa fa-envelope-o" style="font-size: 4rem; color: #28a745;"></i>
                    </div>
                    <h6 class="mb-3">{{ __('Check Your Email') }}</h6>
                    <p class="text-muted">
                        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?') }}
                    </p>

                    @if (auth()->check())
                        <p class="text-muted small">
                            {{ __('We sent the verification link to:') }}<br>
                            <strong>{{ auth()->user()->email }}</strong>
                        </p>
                    @endif
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        <i class="fa fa-check-circle me-2"></i>{{ session('status') }}
                    </div>
                @endif

                <!-- Tips Section -->
                <div class="alert alert-info">
                    <h6 class="mb-2"><i class="fa fa-info-circle me-2"></i>{{ __("Haven't received the email?") }}
                    </h6>
                    <ul class="mb-0 small">
                        <li>{{ __('Check your spam or junk folder') }}</li>
                        <li>{{ __('Verify that you entered the correct email address') }}</li>
                        <li>{{ __('Wait a few minutes - emails can sometimes be delayed') }}</li>
                    </ul>
                </div>

                <!-- Resend Form -->
                <form method="POST" action="{{ route('verification.send') }}" id="resendVerificationForm">
                    @csrf
                    <input type="hidden" name="verify_email_attempt" value="1">
                </form>
            </div>
            <div class="modal-footer flex-column">
                <button type="submit" form="resendVerificationForm" class="btn btn-modal-primary w-100 mb-2">
                    <i class="fa fa-paper-plane me-2"></i>{{ __('Resend Verification Email') }}
                </button>



                <div class="text-center mt-3 w-100">
                    <small class="text-muted">
                        {{ __('Need help?') }}
                        <a href="#" class="text-decoration-none">{{ __('Contact Support') }}</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
