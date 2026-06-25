@extends('layouts.app')

@section('content')
    <div class="cart-table-area section-padding-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="checkout_details_area mt-50 clearfix">
                        <div class="cart-title">
                            <h2>{{ __('Forgot Your Password?') }}</h2>
                        </div>
                        <p>{{ __('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                        </p>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success mt-4 mb-4">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="row">
                                <!-- Email Address -->
                                <div class="col-12 mb-3">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input id="email" name="email" type="email" class="form-control"
                                        value="{{ old('email') }}" required autofocus placeholder="your.email@example.com">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <a class="text-muted" href="{{ route('login') }}">
                                            {{ __('Back to login') }}
                                        </a>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="cart-btn">
                                        <button type="submit"
                                            class="btn amado-btn w-100">{{ __('Send Reset Link') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
