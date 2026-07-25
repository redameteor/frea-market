@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>商品の出品</h1>
    <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="image">商品画像</label>
            <input type="file" name="image_url" id="image" accept="image/*">
            @error('image_url')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <h2>商品の詳細</h2>
        <div class="form-group">
            <label>カテゴリー</label>
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
            <label for="condition_id">商品の状態</label>
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
            <label for="name">商品名</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label for="brand_name">ブランド名</label>
            <input type="text" name="brand" id="brand_name" value="{{ old('brand') }}">
        </div>
        <div class="form-group">
            <label for="description">商品の説明</label>
            <textarea name="description" id="description" rows="5">{{ old('description') }}</textarea>
            @error('description')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label for="price">販売価格</label>
            <input type="number" name="price" id="price" value="{{ old('price') }}" placeholder="300">
            @error('price')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="btn-primary">出品する</button>
    </form>
</div>
@endsection