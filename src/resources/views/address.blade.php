@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-form__heading">
    <h1>住所変更</h1>
</div>
<div Class="address-form__content">
    <form class="form" action="/address" method="post">
    @csrf
    <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">郵便番号</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}">
                </div>
                <div class="form__error">
                    @error('postal_code')
                        <p>{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">住所</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="address" value="{{ old('address') }}">
                </div>
                <div class="form__error">
                    @error('address')
                        <p>{{ $message }}</p>
                    @enderror
                </div>    
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">建物名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="building_name" value="{{ old('building_name') }}">
                </div>
                <div class="form__error">
                    @error('building_name')
                        <p>{{ $message }}</p>
                    @enderror
                </div>    
            </div>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection