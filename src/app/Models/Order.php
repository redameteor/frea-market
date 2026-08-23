<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'delivery_postal_code',
        'delivery_address',
        'delivery_building',
    ];

    // この注文をした購入者を取得するリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // この注文で購入された商品を取得するリレーション
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
