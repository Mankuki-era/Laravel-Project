@extends('layouts.app')

@section('content')
<div class="pageTitle">
    <h1>
        新規登録画面
    </h1>
</div>
<div class="registerPage">
    <div class="form-contena">
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label for="name">{{ __('Name') }}</label>

                <div class="form-items">
                    <i class="fas fa-user icon"></i>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="email">{{ __('E-Mail Address') }}</label>

                <div class="form-items">
                    <i class="fas fa-envelope icon"></i>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>

                <div class="form-items">
                    <i class="fas fa-lock icon"></i>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="password-confirm">{{ __('Confirm Password') }}</label>

                <div class="form-items">
                    <i class="fas fa-lock icon"></i>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Register') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
