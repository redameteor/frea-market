<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/search', [ItemController::class, 'index'])->name('search');
Route::get('/items/{item_id}', [ItemController::class, 'show'])->name('item.show');

Route::middleware(['auth', 'verified'])->group(function () {
    // プロフィール画面関連のルート
    Route::get('/mypage', [ProfileController::class, 'index'])->name('profile');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('prof-edit');
    Route::put('/mypage', [ProfileController::class, 'update'])->name('profile.update');
    // コメント送信
    Route::post('/items/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');
    // 商品購入関連のルート
    Route::get('/purchase/{item_id}', [OrderController::class, 'create'])->name('purchase');
    Route::post('/purchase/{item_id}', [OrderController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/address/{item_id}', [OrderController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [OrderController::class, 'updateAddress'])->name('purchase.address.update');
    // 商品出品関連のルート
    Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('item.store');
});
