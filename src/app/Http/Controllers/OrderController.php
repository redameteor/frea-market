<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class OrderController extends Controller
{
    // 購入画面の表示
    public function create($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        return view('purch', compact('item', 'user'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        // 同時購入を防ぐため、最初からトランザクション
        $item = DB::transaction(function () use ($item_id) {
            
            // lockForUpdate() で他のユーザーの割り込みをロック
            $item = Item::where('id', $item_id)->lockForUpdate()->firstOrFail();

            if ($item->status !== 'available') {
                return null; // すでに購入手続き中か、売り切れの場合はnullを返す
            }
            $item->update(['status' => 'pending']); // ステータスを「決済中」に変更
            return $item;
        });
            
        if (!$item) {
            return redirect()->back()->with('error', 'この商品はすでに購入手続き中か、売り切れています');
        }
            
        // クレジット払い
        if ($request->payment_method === 'card') {
            Stripe::setApiKey(config('services.stripe.secret'));
            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name,
                        ],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                // 配送先情報をメタデータとして保存
                'metadata' => [
                    'delivery_postal_code' => $request->delivery_postal_code,
                    'delivery_address' => $request->delivery_address,
                    'delivery_building' => $request->delivery_building,
                ],
                // 成功時とキャンセル時のリダイレクトURL 末尾に?session_id={CHECKOUT_SESSION_ID} を追加（Stripeが自動で実際のIDに置き換えてくれる）
                'success_url' => route('purchase.success', ['item_id' => $item->id]) .'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('purchase.cancel', ['item_id' => $item->id]), // キャンセル用ルート
            ]);
            return redirect($checkout_session->url);
        }
        
            // コンビニ払い（Stripeを通さず、その場でDBに保存）
        else {
            DB::transaction(function () use ($request, $item) {
                Order::create([
                    'user_id' => Auth::id(),
                    'item_id' => $item->id,
                    'payment_method' => $request->payment_method,
                    'delivery_postal_code' => $request->delivery_postal_code,
                    'delivery_address' => $request->delivery_address,
                    'delivery_building' => $request->delivery_building,
                ]);
                $item->update(['status' => 'sold']);
            });
            return redirect()->route('index')->with('success', '購入が完了しました');
        }
    }

    public function success(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        
        // 連打・リロード対策
        if ($item->status === 'sold') {
            return redirect()->route('index')->with('success', 'この商品はすでに購入済みです');
        }
        // StripeのセッションIDを取得し、stripeからmetadataを引き出す
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Stripeに「このセッションの詳細ちょうだい」とリクエスト
            $session = Session::retrieve($sessionId);

            // metadata から住所を取り出す（もし空ならログインユーザーのデフォルト住所にする保険付き）
            $postal_code = $session->metadata->delivery_postal_code ?? Auth::user()->postal_code;
            $address = $session->metadata->delivery_address ?? Auth::user()->address;
            $building = $session->metadata->delivery_building ?? Auth::user()->building;
        } else {
            // 万が一 session_id が取れなかった場合のフォールバック（予備処理）
            $postal_code = Auth::user()->postal_code;
            $address = Auth::user()->address;
            $building = Auth::user()->building;
        }

        DB::transaction(function () use ($item, $postal_code, $address, $building) {
            Order::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'payment_method' => 'card',
                'delivery_postal_code' => $postal_code,
                'delivery_address' => $address,
                'delivery_building' => $building,
            ]);
            $item->update(['status' => 'sold']);
        });
        return redirect()->route('index')->with('success', '購入が完了しました');
    }

    // 追加：Stripeの決済画面でキャンセルされたときの処理
    public function cancel($item_id)
    {
        $item = Item::findOrFail($item_id);

        // ステータスを「販売中」に戻して再度買えるようにする
        $item->update(['status' => 'available']);

        return redirect()->route('purchase', ['item_id' => $item->id])->with('error', '決済がキャンセルされました');
    }

    public function editAddress($item_id)
    {
        $user = Auth::user();
        return view('address', compact('user', 'item_id'));
    }

    public function updateAddress(AddressRequest $request, $item_id)
    {
        $user = Auth::user();

        $user->postal_code = $request->input('postal_code');
        $user->address = $request->input('address');
        $user->building = $request->input('building');
        $user->save();

        return redirect()->route('purchase', ['item_id' => $item_id])->with('success', '住所情報を更新しました');
    }
}