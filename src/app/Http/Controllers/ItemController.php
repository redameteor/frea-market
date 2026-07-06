<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    // 商品一覧画面を表示する処理
    public function index(Request $request)
    {
        // 検索キーワード（keyword）と現在のタブ（tab）を取得
        $keyword = $request->query('keyword');
        $currentTab = $request->query('tab');

        $recommendQuery = Item::query();
        if ($keyword) {
            $recommendQuery->where('name', 'like', "%{$keyword}%");
        }
        $recommendItems = $recommendQuery->latest()->get();

        //  マイリストはnullでもOK
        $mylistItems = [];
        
        // もし選ばれているタブが「mylist」で、かつユーザーがログインしている場合だけ実行
        if ($currentTab === 'mylist' && Auth::check()){
            $mylistQuery = Auth::user()->likes();
            if ($keyword) {
                $mylistQuery->where('name', 'like', "%{$keyword}%");
            }
        
            $mylistItems = $mylistQuery->latest()->get();
        }
        return view('index', compact('recommendItems', 'mylistItems', 'currentTab'));
    }

    // 商品の詳細画面を表示する処理
    public function show($item_id)
   {
        $item = Item::findOrFail($item_id);
        return view('item', compact('item'));
    } 

    // 商品のお気に入りの処理
    public function like($item_id)
{
    $user = Auth::user();
    $item = Item::findOrFail($item_id);

    // いいねの状態を切り替える（あったら消す、なかったら作る）
    $user->likes()->toggle($item->id);

    // 最新のいいね数と、自分がいいねしているかの状態を取得
    $likeCount = $item->likedUsers()->count();
    $isLiked = $user->likes()->where('item_id', $item_id)->exists();
    
    // 画面をリロードしないので、データを返します
    return response()->json([
        'isLiked' => $isLiked,
        'likeCount' => $likeCount,
    ]);
}
}
