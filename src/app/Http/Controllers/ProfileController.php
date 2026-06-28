<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentPage = $request->query('page', 'sell');
        $sellItems = Item::where('user_id', $user->id)->get();
        $buyItems = Item::whereHas('order', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        return view('profile', compact('user', 'sellItems', 'buyItems', 'currentPage'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('prof-edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if ($request->hasFile('img_url')) {
            if($user->img_url){
                Storage::disk('public')->delete($user->img_url);
            }

            $path = $request->file('img_url')->store('profile_images', 'public');
            $user->img_url = $path;
        }

        $user->name = $request->input('name');
        $user->postal_code = $request->input('postal_code');
        $user->address = $request->input('address');
        $user->building = $request->input('building');
        $user->save();

        return redirect('/');
    }
}
