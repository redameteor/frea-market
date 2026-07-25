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
        'price',
        'description',
        'price',
        'image_url',
        'status',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    // カテゴリーとのリレーション（中間テーブル名 item_categories）
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_categories');
    }

    // 出品者（User）とのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
