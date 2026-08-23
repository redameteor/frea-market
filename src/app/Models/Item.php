<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'condition',
        'description',
        'price',
        'img_url',
        'status',
    ];

    // この商品に投稿されたコメント一覧を取得するリレーション
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // この商品の注文情報を取得するリレーション
    public function order()
    {
        return $this->hasOne(Order::class);
    }

    // この商品をいいねしたユーザー一覧を取得するリレーション
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    // この商品に設定されているカテゴリー一覧を取得するリレーション（中間テーブル名 item_categories）
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_categories');
    }

    // この商品の出品者を取得するリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
