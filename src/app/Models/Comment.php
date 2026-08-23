<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'content',
    ];

    // このコメントを投稿したユーザーを取得するリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // このコメントが投稿された商品を取得するリレーション
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
