<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request, $item_id){
    
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
}
