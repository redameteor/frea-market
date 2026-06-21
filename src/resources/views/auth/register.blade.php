@extends('layouts.authapp')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register-form__heading">
    <h1>会員登録</h1>
</div>
<div class="register-form__content">
    <form class="form" action="/register" method="post">
    @csrf
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">ユーザー名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="name" value="{{ old('name') }}">
                </div>
                <div class="form__error">
                    @error('name')
                    <p>{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">メールアドレス</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="email" value="{{ old('email') }}">
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
                    <input type="password" name="password" value="{{ old('password') }}">
                </div>
                <div class="form__error">
                    @error('password')
                        <p>{{ $message }}</p>
                    @enderror
                </div>    
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">確認用パスワード</span>
            </div>
            <div class="form__input--text">
                <input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}">
            </div>
        </div>
        <button class="form__button-submit" type="submit">登録する</button>
    </form>
    <a class="form__button-to-login" href="/login">ログインはこちら</a>
</div>
@endsection