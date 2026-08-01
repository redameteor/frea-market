@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>商品の出品</h1>
    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label class="form-label">商品画像</label>
            <div class="item-image-group">
                <div class="image-preview">
                    @if(isset($item) && $item->img_url)
                        <img src="{{ asset('storage/' . $item->img_url) }}" alt="商品画像">
                    @endif
                    <label for="image_url" class="btn-select-image">画像を選択する</label>
                </div>
                <input type="file" name="img_url" id="image_url" accept="image/*" class="hidden-file-input">
            </div>
            @error('img_url')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <h2>商品の詳細</h2>
        <div class="form-group">
            <label class="form-label">カテゴリー</label>
            <div class="category-checkboxes">
                @foreach($categories as $category)
                    <label>
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                            {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }}>
                        {{ $category->name }}
                    </label>
                @endforeach
            </div>
            @error('category_ids')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="condition_id">商品の状態</label>
            <select name="condition" id="condition_id">
                <option value="">選択してください</option>
                @foreach($conditions as $condition)
                    <option value="{{ $condition }}" {{ old('condition') == $condition ? 'selected' : '' }}>
                        {{ $condition }}
                    </option>
                @endforeach
            </select>
            @error('condition')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <h2>商品名と説明</h2>
        <div class="form-group">
            <label class="form-label" for="name">商品名</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="brand_name">ブランド名</label>
            <input type="text" name="brand" id="brand_name" value="{{ old('brand') }}">
        </div>
        <div class="form-group">
            <label class="form-label" for="description">商品の説明</label>
            <textarea name="description" id="description" rows="5">{{ old('description') }}</textarea>
            @error('description')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="price">販売価格</label>
            <input type="number" name="price" id="price" value="{{ old('price') }}" placeholder="300">
            @error('price')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="btn-primary">出品する</button>
    </form>
</div>

<script>
    // 画像ファイル選択用の input 要素（id="image_url"）の変化（change）を監視
document.getElementById('image_url').addEventListener('change', function(e) {
    // ユーザーが選択したファイル情報を取得（1つ目のファイル）
    const file = e.target.files[0];
    // ファイルが選択されなかった（キャンセルされた）場合は処理を中断
    if (!file) return;
    // FileReader オブジェクトを作成して、画像ファイルを読み込む
    const reader = new FileReader();

    // 読み込みが完了したときの処理
    reader.onload = function(e) {
        // 画像を表示する div 要素を取得
        const previewDiv = document.querySelector('.image-preview');
        
        // 既存のimgタグが存在するか確認あれば削除
        let img = previewDiv.querySelector('img');
        if (!img) {
            img = document.createElement('img');
            previewDiv.insertBefore(img, previewDiv.firstChild);
        }
        
        // 選択した画像をセット
        img.src = e.target.result;
        img.style.maxWidth = '100%';
        img.style.maxHeight = '100%';
        img.style.objectFit = 'contain';
        img.style.position = 'absolute';
    };
    reader.readAsDataURL(file);
});
</script>
@endsection