@extends('layouts.authapp')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-form__heading">
    <h1>ログイン</h1>
</div>
<div class="login-form__content">
    <form class="form" action="/login" method="post">
    @csrf
    <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">メールアドレス</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="email" name="email" placeholder="sample@example.com" value="{{ old('email') }}">
                </div>
                <div class="form__error">
                    @error('email')
                        <p>{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">パスワード</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="password" name="password" placeholder="sample123example" value="{{ old('password') }}">
                </div>
                <div class="form__error">
                    @error('password')
                        <p>{{ $message }}</p>
                    @enderror
                </div>    
            </div>
        </div>
        <button class="form__button-submit" type="submit">ログイン</button>
    </form>
    <a class="form__button-to-login" href="/register">会員登録はこちら</a>
</div>
@endsection