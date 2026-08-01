@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="item-page-container">

    <div class="profile-header">
        <div class="profile-info">
            <div class="profile-avatar">
                @if($user->img_url)
                    <img src="{{ asset('storage/' . $user->img_url) }}" alt="ユーザー画像">
                @else
                    <div class="default-avatar"></div>
                @endif
            </div>
            <h1 class="profile-name">{{ $user->name }}</h1>
        </div>
        <a href="{{ route('prof-edit') }}" class="btn-prof-edit">プロフィールを編集</a>
    </div>
    <div class="tab-menu">
        <a href="{{ route('profile', ['page' => 'sell']) }}" class="tab-item {{ $currentTab === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('profile', ['page' => 'buy']) }}" class="tab-item {{ $currentTab === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>
    <hr class="tab-line">
    @if ($currentTab === 'buy')
    <div class="content-area buy-content">
        <div class="item-grid">
            @forelse($buyItems as $item)
                <a href="{{ route('item.show', ['item_id' => $item->id]) }}" class="item-card">
                    <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
                    <p class="item-name">{{ $item->name }}</p>
                    <span class="sold-label">Sold</span>
                </a>
            @empty
                <p class="no-items">購入した商品はまだありません</p>
            @endforelse
        </div>
    </div>
    @else
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
    @endif
</div>
@endsection