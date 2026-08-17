@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purch.css') }}">
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="purchase-container">
    <form action="{{ route('purchase.store', ['item_id' => $item->id]) }}" method="POST" class="purchase-form">
        @csrf
        <div class="purchase__main">
            <div class="purchase__section item-info">
                <div class="item-info__image">
                    <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
                </div>
                <div class="item-info__detail">
                    <h2 class="item-name">{{ $item->name }}</h2>
                    <p class="item-price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>
            <div class="purchase__section payment-method">
                <h3 class="section-title">支払い方法</h3>
                <div class="form-group">
                    <select name="payment_method" class="select-payment" required>
                        <option value="" disabled selected {{ old('payment_method') == '' ? 'selected' : '' }}>選択してください</option>
                        <option value="konbini" {{ old('payment_method') == 'konbini' ? 'selected' : '' }}>コンビニ払い</option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>クレジットカード</option>
                    </select>
                    @error('payment_method')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="purchase__section delivery-address">
                <div class="delivery-address__header">
                    <h3 class="section-title">配送先</h3>
                    <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}" class="btn-change-address">変更する</a>
                </div>
                <div class="delivery-address__content">
                    <p class="postal-code">〒 {{ $user->postal_code ?? '未登録' }}</p>
                    @error('delivery_postal_code')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                    <p class="address">{{ $user->address ?? '住所を登録してください' }} {{ $user->building ?? '' }}</p>
                    @error('delivery_address')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                    <input type="hidden" name="delivery_postal_code" value="{{ old('delivery_postal_code', $user->postal_code) }}">
                    <input type="hidden" name="delivery_address" value="{{ old('delivery_address', $user->address) }}">
                    <input type="hidden" name="delivery_building" value="{{ old('delivery_building', $user->building) }}">
                </div>
            </div>
        </div>
        <div class="purchase__sidebar">
            <table class="summary-table">
                <tr>
                    <th>商品代金</th>
                    <td>¥{{ number_format($item->price) }}</td>
                </tr>
                <tr class="total-row">
                    <th>支払い金額</th>
                    <td class="total-price">¥{{ number_format($item->price) }}</td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    <td id="selected-payment-display">選択してください</td>
                </tr>
            </table>

            <button type="submit" class="btn-submit-purchase" {{ empty($user->address) || $item->status !== 'available' ? 'disabled' : '' }}>
                購入する
            </button>
            @if(empty($user->address))
                <p class="error-text">※配送先住所を設定してください。</p>
            @endif
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentSelect = document.querySelector('.select-payment');
        const paymentDisplay = document.getElementById('selected-payment-display');

        function updateDisplay() {
            if (paymentSelect.selectedIndex > 0) {
                paymentDisplay.textContent = paymentSelect.options[paymentSelect.selectedIndex].text;
            } else {
                paymentDisplay.textContent = '未選択';
            }
        }

        // 変更時イベント
        paymentSelect.addEventListener('change', updateDisplay);

        // ページ読み込み時（old値で初期選択されている場合の反映）
        updateDisplay();
    });
</script>
@endsection