@extends('layouts.app')

@section('content')
<div class="pageTitle">
    <h1>
        ログイン画面
    </h1>
</div>
<div class="loginPage">
    <div class="form-contena">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">{{ __('E-Mail Address') }}</label>
                <div class="form-items">
                    <i class="fas fa-envelope icon"></i>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    
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
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
            </div>
            
            <div class="form-group row mb-0">
                <div class="col-md-8 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Login') }}
                    </button>
                    
                    @if (Route::has('password.request'))
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                    @endif
                </div>
            </div>
        </form>
        <div class="testUser">
            <h1>テスト用アカウント</h1>
            <p>メールアドレス：test1@gmail.com</p>
            <p>パスワード：sasaking</p>
        </div>
    </div>
</div>
@endsection
    