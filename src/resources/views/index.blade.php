@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="item-page-container">
    <input type="radio" id="tab-recommend" name="item-tab" checked>
    <input type="radio" id="tab-mylist" name="item-tab">
    <div class="tab-menu">
        <label for="tab-recommend" class="tab-item">おすすめ</label>
        <label for="tab-mylist" class="tab-item">マイリスト</label>
    </div>
    <hr class="tab-line">
    <div class="content-area recommend-content">
        <div class="item-grid">
            @forelse($recommendItems as $item)
                <div class="item-card">
                    <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
                    <p class="item-name">{{ $item->name }}</p>
                    @if($item->is_sold) 
                    <span class="sold-label">Sold</span> 
                    @endif
                </div>
            @empty
                <p>表示する商品がありません</p>
            @endforelse
        </div>
    </div>
    <div class="content-area mylist-content">
        @if(Auth::check())
            <div class="item-grid">
                @forelse($myListItems as $item)
                    <div class="item-card">
                        <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
                        <p class="item-name">{{ $item->name }}</p>
                        @if($item->is_sold) 
                        <span class="sold-label">Sold</span> 
                        @endif
                    </div>
                @empty
                    <p>いいねした商品はまだありません</p>
                @endforelse
            </div>
        @else
            <p class="login-alert">マイリストを見るにはログインが必要です。</p>
        @endif
    </div>
</div>
@endsection