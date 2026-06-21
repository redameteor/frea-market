@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/prof-edit.css') }}">
@endsection

@section('content')
<div class="prof-edit__heading">
    <h1>プロフィール設定</h1>
</div>
<form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label class="form-label">プロフィール画像</label>
        <div class="profile-image-group">
            <div class="image-preview">
                @if($user->img_url)
                    <img src="{{ asset('storage/' . $user->img_url) }}" alt="プロフィール画像">
                @else
                    <div class="no-image"></div>
                @endif
            </div>
            <label for="img_url" class="btn-select-image">画像を選択する</label>
            <input type="file" id="img_url" name="img_url" accept="image/*" class="hidden-file-input">
        </div>
        @error('img_url') 
            <p class="error-message">{{ $message }}</p> 
        @enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="name">名前</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-input">
        @error('name') 
            <p class="error-message">{{ $message }}</p> 
        @enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="postal_code">郵便番号</label>
        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="form-input">
        @error('postal_code') 
            <p class="error-message">{{ $message }}</p> 
        @enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="address">住所</label>
        <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}" class="form-input">
        @error('address') 
            <p class="error-message">{{ $message }}</p> 
        @enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="building">建物名</label>
        <input type="text" id="building" name="building" value="{{ old('building', $user->building) }}" class="form-input">
        @error('building') 
            <p class="error-message">{{ $message }}</p> 
        @enderror
    </div>
    <button type="submit" class="btn">更新する</button>
</form>
