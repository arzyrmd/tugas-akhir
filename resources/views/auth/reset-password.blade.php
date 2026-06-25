@extends('layouts.app')

@section('content')
    <div class="cart-table-area section-padding-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="checkout_details_area mt-50 clearfix">
                        <div class="cart-title">
                            <h2>{{ __('Reset Your Password') }}</h2>
                        </div>
                        <p>{{ __('Create a new secure password for your account') }}</p>

                        <form method="POST" action="{{ route('password.store') }}">
                            @csrf

                            <!-- Password Reset Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="row">
                                <!-- Email Address -->
                                <div class="col-12 mb-3">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input id="email" name="email" type="email" class="form-control bg-light"
                                        value="{{ old('email', $request->email) }}" required readonly>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="col-12 mb-3">
                                    <label for="password">{{ __('New Password') }}</label>
                                    <input id="password" name="password" type="password" class="form-control" required
                                        autocomplete="new-password" placeholder="Enter your new password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <p class="mt-2 small text-muted">Password must be at least 8 characters and contain
                                        letters, numbers, and special characters.</p>
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-12 mb-4">
                                    <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                                    <input id="password_confirmation" name="password_confirmation" type="password"
                                        class="form-control" required autocomplete="new-password"
                                        placeholder="Re-enter your new password">
                                    @error('password_confirmation')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn amado-btn w-100">{{ __('Reset Password') }}</button>
                                </div>

                                <div class="col-12 mt-3 text-center">
                                    <a href="{{ route('login') }}" class="text-muted">
                                        {{ __('Return to login') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
