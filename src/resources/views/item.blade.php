@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="item-container">
    <div class="item__image-box">
        <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
        @if($item->status === 'sold')
            <div class="sold-label">sold</div>
        @endif
    </div>
    <div class="item__info-box">
        <div class="item__header">
            <h1 class="item-name">{{ $item->name }}</h1>
            <p class="item-brand">{{ $item->brand ?? 'ブランド名なし' }}</p>
            <p class="item-price">
                <span class="currency">¥</span>{{ number_format($item->price) }} <span class="tax-included">(税込)</span>
            </p>
            <div class="item__stats">
                <div class="stat-item like-container">
                    <button class="btn-like" data-item-id="{{ $item->id }}">
                    @if(Auth::check() && Auth::user()->likes()->where('item_id', $item->id)->exists())
                        <img src="{{ asset('img/ハートロゴ_ピンク.png') }}" class="like-icon" alt="いいね済">
                    @else
                        <img src="{{ asset('img/ハートロゴ_デフォルト.png') }}" class="like-icon" alt="未いいね">
                    @endif
                    </button>
                    <span class="like-count">{{ $item->likedUsers()->count() }}</span>
                </div>
                <div class="stat-item comment-container">
                    <img src="{{ asset('img/comment_logo.png') }}" class="comment-icon" alt="コメント">
                    <span class="comment-count">{{ $item->comments->count() ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="item__actions">
            @if($item->status === 'sold')
                <button class="btn-purchase btn-purchase--disabled" disabled>売り切れました</button>
            @else
                <a href="{{ route('purchase', ['item_id' => $item->id])}}" class="btn-purchase btn-purchase--active">
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
                            <span class="category-tag">ファッション</span>
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
                            @error('content')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const likeBtn = document.querySelector('.btn-like');

    if (likeBtn) {
        // ボタンがクリックされたときの動き
        likeBtn.addEventListener('click', function () {
            const itemId = this.dataset.itemId;

            // サーバーへいいねの切り替えをリクエスト
            fetch(`/items/${itemId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // 未ログイン時はログインページへリダイレクト
                if (response.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (!data) return; // データがない場合は何もしない
               
                // いいねの数字をその場で書き換え
                const likeCountSpan = document.querySelector('.like-count');
                if (likeCountSpan) {
                    likeCountSpan.textContent = data.likeCount;
                }

                // ハートの画像をその場で切り替え
                const likeImg = likeBtn.querySelector('.like-icon');
                if (likeImg) {
                    if (data.isLiked) {
                        likeImg.src = "{{ asset('img/ハートロゴ_ピンク.png') }}";
                        likeImg.alt = "いいね済み";
                    } else {
                        likeImg.src = "{{ asset('img/ハートロゴ_デフォルト.png') }}";
                        likeImg.alt = "未いいね";
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
});
</script>
@endsection