@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="item-page-container">

    <div class="profile-header">
        <div class="profile-avatar">
            @if($user->img_url)
                <img src="{{ asset('storage/' . $user->img_url) }}" alt="ユーザー画像">
            @else
                <div class="default-avatar"></div>
            @endif
        </div>
        <h1 class="profile-name">{{ $user->name }}</h1>
        <a href="{{ route('profile.edit') }}" class="btn-profile-edit">プロフィールを編集</a>
    </div>

    <input type="radio" id="tab-sell" name="profile-tab" {{ $currentTab === 'buy' ? '' : 'checked' }}>
    <input type="radio" id="tab-buy" name="profile-tab" {{ $currentTab === 'buy' ? 'checked' : '' }}>
    <div class="tab-menu">
        <label for="tab-sell" class="tab-item">出品した商品</label>
        <label for="tab-buy" class="tab-item">購入した商品</label>
    </div>
    <hr class="tab-line">
    <div class="content-area sell-content">
        <div class="item-grid">
            @forelse($sellItems as $item)
                <a href="{{ route('item.show', ['item_id' => $item->id]) }}" class="item-card">
                    <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
                    <p class="item-name">{{ $item->name }}</p>
                    @if($item->order) 
                        <span class="sold-label">Sold</span>
                    @endif
                </a>
            @empty
                <p class="no-items">出品した商品はまだありません</p>
            @endforelse
        </div>
    </div>
    <div class="content-area buy-content">
        <div class="item-grid">
            @forelse($buyItems as $item)
                <a href="{{ route('item.show', ['item_id' => $item->id]) }}" class="item-card">
                    <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
                    <p class="item-name">{{ $item->name }}</p>
                    @if($item->order) 
                        <span class="sold-label">Sold</span>
                    @endif
                </a>
            @empty
                <p class="no-items">購入した商品はまだありません</p>
            @endforelse
        </div>
    </div>
</div>
@endsection