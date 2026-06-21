@extends('layouts.authapp')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-form__content">
    <div class="auth-heading">
        <p>登録していただいたメールアドレスに認証メールを送付しました。<br />
            メール認証を完了してください。
        </p>
    </div>
    <div class="auth__button-group">
        <a class="auth__button-auth" href="{{ route('prof-edit') }}">認証はこちらから</a>
        <form class="auth__resend-form" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="auth__button-mail">認証メールを再送する</button>
        </form>
    </div>
</div>

@endsection