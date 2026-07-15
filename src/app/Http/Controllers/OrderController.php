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
            Stripe::setApiKey(env('STRIPE_SECRET'));
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
                'success_url' => route('purchase.success', ['item_id' => $item->id]),
                'cancel_url' => route('purchase.cancel', ['item_id' => $item->id]), // 👈 キャンセル用ルートに変更
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

    public function success($item_id)
    {
        $item = Item::findOrFail($item_id);
        
        // 連打・リロード対策
        if ($item->status === 'sold') {
            return redirect()->route('index')->with('success', 'この商品はすでに購入済みです');
        }

        DB::transaction(function () use ($item) {
            Order::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'payment_method' => 'card',
                'delivery_postal_code' => Auth::user()->postal_code,
                'delivery_address' => Auth::user()->address,
                'delivery_building' => Auth::user()->building,
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