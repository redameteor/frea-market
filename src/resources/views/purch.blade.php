@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purch.css') }}">
@endsection

@section('content')
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
                        <option value="" disabled selected>選択してください</option>
                        <option value="konbini">コンビニ払い</option>
                        <option value="card">クレジットカード</option>
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
                    <p class="postal-code">〒 {{ $user->delivery_postal_code ?? '未登録' }}</p>
                    @error('delivery_postal_code')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                    <p class="address">{{ $user->delivery_address ?? '住所を登録してください' }} {{ $user->delivery_building ?? '' }}</p>
                    @error('delivery_address')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                    <input type="hidden" name="delivery_postal_code" value="{{ $user->delivery_postal_code }}">
                    <input type="hidden" name="delivery_address" value="{{ $user->delivery_address }}">
                    <input type="hidden" name="delivery_building" value="{{ $user->delivery_building }}">
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
                    <td id="selected-payment-display">未選択</td>
                </tr>
            </table>

            <button type="submit" class="btn-submit-purchase" {{ empty($user->address) ? 'disabled' : '' }}>
                購入する
            </button>
            @if(empty($user->address))
                <p class="error-text">※配送先住所を設定してください。</p>
            @endif
        </div>
    </form>
</div>
<script>
    document.querySelector('.select-payment').addEventListener('change', function(e) {
        const text = e.target.options[e.target.selectedIndex].text;
        document.getElementById('selected-payment-display').textContent = text;
    });
</script>
@endsection