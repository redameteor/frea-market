<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(PurchaseRequest $request, $item_id){
    
        $item = Item::findOrFail($item_id);
        if ($item->is_sold){
            return redirect()->back()->with('error', 'この商品はすでに購入されています。');
        }

        DB::transaction(function () use ($request, $item){

            Order::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'payment_method' => $request->payment_method,
                'delivery_postal_code' => $request->delivery_postal_code,
                'delivery_address' => $request->delivery_address,
                'delivery_building' => $request->delivery_building,
            ]);

            $item->update(['is_sold' => true]);
        });

        return redirect()->route('index')->with('success', '購入が完了しました。');
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

        return redirect()->route('purch', ['item_id' => $item_id])->with('success', '住所情報を更新しました');
    }
}
