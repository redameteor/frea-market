<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech_frea-market</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="ロゴ">
            </div>
            <form class="header-form" action="{{ route('search') }}" method="get">
                @csrf
                <div class="header-form__group-content">
                    <div class="header-form__input--text">
                        <input type="text" class="header-form__text" name="keyword" placeholder="なにをお探しですか？" value="{{ old('keyword') }}">
                    </div>
                    <div class="header-form__error">
                        @error('keyword')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </form>
            <div class="header__nav">
                <ul class="header__nav-list">
                    @if (Auth::check())
                    <li class="header__nav-item">
                        <a class="header__nav-log-button" href="/logout">ログアウト</a>
                    </li>
                    <li class="header__nav-item">
                        <a class="header__nav-prof-button" href="/profile">マイページ</a>
                    </li>
                    @else
                    <li class="header__nav-item">
                        <a class="header__nav-log-button" href="/login">ログイン</a>
                    </li>
                    <li class="header__nav-item">
                        <a class="header__nav-prof-button" href="/register">会員登録</a>
                    </li>
                    @endif
                    <li class="header__nav-item">
                        <a class="header__nav-sell-button" href="/sell">出品</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
    
</body>
</html>