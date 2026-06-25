@extends('layouts.app')

@section('content')
    <div class="cart-table-area section-padding-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="checkout_details_area mt-50 clearfix">
                        <div class="cart-title">
                            <h2>{{ __('Security Confirmation') }}</h2>
                        </div>
                        <p>{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                        </p>

                        <div class="alert alert-info mt-4 mb-4">
                            <p>For your security, we need to verify your identity. Please enter your password to continue.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="row">
                                <!-- Password -->
                                <div class="col-12 mb-3">
                                    <label for="password">{{ __('Password') }}</label>
                                    <input id="password" name="password" type="password" class="form-control" required
                                        autocomplete="current-password" placeholder="Enter your password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 d-flex justify-content-between">
                                    <a href="{{ url()->previous() }}" class="btn amado-btn">
                                        {{ __('Cancel') }}
                                    </a>
                                    <button type="submit" class="btn amado-btn active">
                                        {{ __('Confirm') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
