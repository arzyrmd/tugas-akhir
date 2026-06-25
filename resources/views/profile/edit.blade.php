@extends('layouts.app')

@section('content')
    <div class="cart-table-area section-padding-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <!-- Profile Information -->
                    <div class="checkout_details_area mt-50 clearfix">
                        <div class="cart-title">
                            <h2>{{ __('Profile Information') }}</h2>
                        </div>
                        <p>{{ __("Update your account's profile information and email address.") }}</p>

                        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                            @csrf
                        </form>

                        <form method="post" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input id="name" name="name" type="text" class="form-control"
                                        value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input id="email" name="email" type="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" required autocomplete="username">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                        <div class="mt-2">
                                            <p class="text-sm mt-2">
                                                {{ __('Your email address is unverified.') }}

                                                <button form="send-verification" class="btn amado-btn">
                                                    {{ __('Click here to re-send the verification email.') }}
                                                </button>
                                            </p>

                                            @if (session('status') === 'verification-link-sent')
                                                <p class="mt-2 text-success">
                                                    {{ __('A new verification link has been sent to your email address.') }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn amado-btn">{{ __('Save') }}</button>

                                    @if (session('status') === 'profile-updated')
                                        <p class="text-success mt-2">
                                            {{ __('Saved.') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Update Password -->
                    <div class="checkout_details_area mt-50 clearfix">
                        <div class="cart-title">
                            <h2>{{ __('Update Password') }}</h2>
                        </div>
                        <p>{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>

                        <form method="post" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="current_password">{{ __('Current Password') }}</label>
                                    <input id="current_password" name="current_password" type="password"
                                        class="form-control" autocomplete="current-password">
                                    @error('current_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="password">{{ __('New Password') }}</label>
                                    <input id="password" name="password" type="password" class="form-control"
                                        autocomplete="new-password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                                    <input id="password_confirmation" name="password_confirmation" type="password"
                                        class="form-control" autocomplete="new-password">
                                    @error('password_confirmation')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn amado-btn">{{ __('Save') }}</button>

                                    @if (session('status') === 'password-updated')
                                        <p class="text-success mt-2">
                                            {{ __('Saved.') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Delete Account -->
                    <div class="checkout_details_area mt-50 clearfix">
                        <div class="cart-title">
                            <h2>{{ __('Delete Account') }}</h2>
                        </div>
                        <p>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                        </p>

                        <div class="cart-btn mt-30">
                            <button type="button" class="btn amado-btn w-100" data-toggle="modal"
                                data-target="#deleteAccountModal">
                                {{ __('Delete Account') }}
                            </button>
                        </div>

                        <!-- Delete Account Modal -->
                        <div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog"
                            aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteAccountModalLabel">{{ __('Delete Account') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>{{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                                        </p>

                                        <form method="post" action="{{ route('profile.destroy') }}">
                                            @csrf
                                            @method('delete')

                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label for="password">{{ __('Password') }}</label>
                                                    <input id="password" name="password" type="password"
                                                        class="form-control" placeholder="{{ __('Password') }}">
                                                    @error('password')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="col-12">
                                                    <button type="button" class="btn amado-btn"
                                                        data-dismiss="modal">{{ __('Cancel') }}</button>
                                                    <button type="submit"
                                                        class="btn amado-btn active">{{ __('Delete Account') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
