<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');
        $currentTab = $request->query('tab');

        $recommendQuery = Item::query();
        if ($keyword) {
            $recommendQuery->where('name', 'like', "%{$keyword}%");
        }
        $recommendItems = $recommendQuery->latest()->get();

        $mylistItems = [];
        
        if ($currentTab === 'mylist' && Auth::check()){
            $mylistQuery = Item::query();
            if ($keyword) {
                $mylistQuery->where('name', 'like', "%{$keyword}%");
            }
        
        $mylistItems = $mylistQuery->latest()->get();
        }
        return view('index', compact('recommendItems', 'mylistItems', 'currentTab'));
    }

    public function show($item_id)
   {
        $item = Item::findOrFail($item_id);
        return view('item', compact('item'));
    } 
}
