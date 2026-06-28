@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="item-container">
    <div class="item__image-box">
        <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
        @if($item->order)
            <div class="sold-label">Sold</div>
        @endif
    </div>
    <div class="item__info-box">
        <div class="item__header">
            <h1 class="item-name">{{ $item->name }}</h1>
            <p class="item-brand">{{ $item->brand ?? 'ブランド名なし' }}</p>
            <p class="item-price">
                <span class="currency">¥</span>{{ number_format($item->price) }} <span class="tax-included">(税込)</span>
            </p>
        </div>
        <div class="item__actions">
            @if($item->order)
                <button class="btn-purchase btn-purchase--disabled" disabled>売り切れました</button>
            @else
                <a href="#" class="btn-purchase btn-purchase--active">
                    購入手続きへ
                </a>
            @endif
        </div>
        <div class="item__section">
            <h2 class="section-title">商品説明</h2>
            <div class="section-content">
                <p class="item-description">{!! nl2br(e($item->description)) !!}</p>
            </div>
        </div>
        <div class="item__section">
            <h2 class="section-title">商品の情報</h2>
            <div class="section-content">
                <table class="detail-table">
                    <tr>
                        <th>カテゴリー</th>
                        <td>
                            <span class="category-tag">メンズ</span>
                            <span class="category-tag">トップス</span>
                        </td>
                    </tr>
                    <tr>
                        <th>商品の状態</th>
                        <td>{{ $item->condition ?? '目立った傷や汚れなし' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="item__section comment-section">
            <h2 class="section-title">コメント ({{ $item->comments->count() ?? 0 }})</h2>
            <div class="section-content">
                <div class="comment-list">
                    @forelse($item->comments ?? [] as $comment)
                        <div class="comment-item">
                            <div class="comment-user">
                                <div class="comment-user__avatar">
                                    @if($comment->user->img_url)
                                        <img src="{{ asset('storage/' . $comment->user->img_url) }}" alt="ユーザー画像">
                                    @else
                                        <div class="default-avatar"></div>
                                    @endif
                                </div>
                                <span class="comment-user__name">{{ $comment->user->name }}</span>
                            </div>
                            <div class="comment-body">
                                <p class="comment-text">{!! nl2br(e($comment->content)) !!}</p>
                                <span class="comment-date">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="no-comment">まだコメントはありません。</p>
                    @endforelse
                </div>
                <div class="comment-form-area">
                    @if(Auth::check())
                        <form action="{{ route('comment.store', ['item_id' => $item->id]) }}" method="POST" class="comment-form">
                            @csrf
                            <label for="comment-textarea" class="form-label">商品へのコメント</label>
                            <textarea id="comment-textarea" name="content" rows="4" class="comment-textarea" required></textarea>
                            <button type="submit" class="btn-comment-submit">コメントを送信する</button>
                        </form>
                    @else
                        <div class="comment-login-alert">
                            <p>コメントを投稿するには<a href="{{ route('login') }}">ログイン</a>してください。</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection