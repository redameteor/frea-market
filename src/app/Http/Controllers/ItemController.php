<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    // 商品一覧画面を表示する処理
    public function index(Request $request)
    {
        // 検索キーワード（keyword）と現在のタブ（tab）を取得
        $keyword = $request->query('keyword');
        $currentTab = $request->query('tab');

        $recommendQuery = Item::query();

        if (Auth::check()) {
            // ログインしている場合は、ユーザーが出品した商品を除外
            $recommendQuery->where('user_id', '!=', Auth::id());
        }

        if ($keyword) {
            $recommendQuery->where('name', 'like', "%{$keyword}%");
        }
        $recommendItems = $recommendQuery->latest()->get();

        //  マイリストの初期値はnull
        $mylistItems = collect();
        
        // もし選ばれているタブが「mylist」で、かつユーザーがログインしている場合だけ実行
        if (Auth::check()){
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
        $item = Item::with(['categories', 'comments.user'])
            ->withCount('likedUsers', 'comments')
            ->findOrFail($item_id);
            
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
    // 出品画面の表示
    public function create()
    {
        $categories = Category::all();

        // 商品の状態（condition）の選択肢を定義
        $conditions = ['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];

        return view('sell', compact('categories', 'conditions'));
    }

    // 出品商品の保存処理
    public function store(ExhibitionRequest $request)
    {
        // データの整合性確保
        DB::transaction(function () use ($request) {
            // 画像の保存 (storage/app/public/items 配下に保存)
            $imagePath = $request->file('img_url')->store('items', 'public');

            // 商品の作成
            $item = Item::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'brand' => $request->brand, // カラム名: brand
                'price' => $request->price,
                'description' => $request->description,
                'condition' => $request->condition, // 文字列を直接保存
                'img_url' => $imagePath, // カラム名: img_url
                'status' => 'available',
            ]);

        // カテゴリーの中間テーブル紐付け
        if ($request->has('category_ids')) {
            $item->categories()->attach($request->category_ids);
            }
        });
        return redirect()->route('index')->with('success', '商品を出品しました');
    }

}
