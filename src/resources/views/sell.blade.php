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
                    <button type="button" class="btn-remove-image" id="btn-remove-image" style="display: none;">&times;</button>
                    <img id="preview-img" src="{{ !empty($item->img_url) ? asset('storage/' . $item->img_url) : '' }}" alt="商品画像" style="display: none;">
                    <label for="image_url" class="btn-select-image" id="btn-select-image">画像を選択する</label>
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
                    <label class="category-btn">
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                            {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }}>
                        <span class="category-label">{{ $category->name }}</span>
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
// DOM要素の取得
const fileInput = document.getElementById('image_url');
const previewImg = document.getElementById('preview-img');
const btnSelect = document.getElementById('btn-select-image');
const btnRemove = document.getElementById('btn-remove-image');

// 1. 画像が選択されたとき
fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();

    reader.onload = function(e) {
        // 画像をセットして表示
        previewImg.src = e.target.result;
        previewImg.style.display = 'block';

        // 「画像を選択する」ボタンを隠し、「×」ボタンを表示
        btnSelect.style.display = 'none';
        btnRemove.style.display = 'flex';
    };

    reader.readAsDataURL(file);
});

// 2. 「×」ボタンが押されたとき（削除）
btnRemove.addEventListener('click', function() {
    // 選択値をリセット
    fileInput.value = '';

    // 画像を非表示
    previewImg.src = '';
    previewImg.style.display = 'none';

    // 「×」ボタンを隠し、「画像を選択する」ボタンを再表示
    btnRemove.style.display = 'none';
    btnSelect.style.display = 'inline-block';
});
</script>
@endsection