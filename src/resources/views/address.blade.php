@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/prof-edit.css') }}">
@endsection

@section('content')

<div class="container">
    <div class="prof-edit__heading">
        <h1>住所の変更</h1>
    </div>
    <form action="{{ route('purchase.address.update', ['item_id' => $item_id]) }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label" for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" class="form-input" value="{{ old('postal_code', $user->postal_code) }}">
            @error('postal_code')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="address">住所</label>
            <input type="text" name="address" id="address" class="form-input" value="{{ old('address', $user->address) }}">
            @error('address')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="building">建物名</label>
            <input type="text" name="building" id="building" class="form-input" value="{{ old('building', $user->building) }}">
            @error('building')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="btn">更新する</button>
    </form>
</div>

@endsection