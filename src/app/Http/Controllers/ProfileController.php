<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('prof-edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'img_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'postal_code' => 'nullable|string|max:8',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

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
