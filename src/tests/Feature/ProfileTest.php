<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     *    ユーザー情報取得
     */

    // 必要な情報が取得できる（プロフ画像・ユーザー名・出品、購入した商品一覧）
    public function test_user_can_view_profile_page_with_required_information():void
    {
        $user = User::factory()->create([
            'name' => '齋藤 道三',
            'img_url' => 'profiles/test_user.jpg',
        ]);

        $soldItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品した商品',
            ]);

        $purchasedItem = Item::factory()->create([
            'name' => '購入した商品',
            ]);

        Order::create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
            'payment_method' => 'convenience',
            'delivery_postal_code' => '123-4567',
            'delivery_address' => '岐阜県岐阜市金華山1-1',
            'delivery_building' => '稲葉山城',
            ]);

        $response = $this->actingAs($user)->get(route('profile'));
        $response->assertStatus(200);

        $response->assertSee('齋藤 道三');
        $response->assertSee('storage/profiles/test_user.jpg');
        $response->assertSee('出品した商品');

        $buyPageResponse = $this->actingAs($user)->get(route('profile', ['page' => 'buy']));
        $buyPageResponse->assertStatus(200);
        $buyPageResponse->assertSee('購入した商品');
    }

    /*
    *    ユーザー情報変更
    */

    // 変更項目が初期値として過去設定されていること（プロフ画像・ユーザー名・郵便番号・住所）
    public function test_profile_edit_page_displays_initial_user_data():void
    {
        $user = User::factory()->create([
            'name' => '齋藤 道三',
            'img_url' => 'profile/test_user.jpg',
            'postal_code' => '123-4567',
            'address' => '岐阜県岐阜市金華山1-1',
            'building' => '稲葉山城',
        ]);

        $response = $this->actingAs($user)->get(route('prof-edit'));
        $response->assertStatus(200);

        $response->assertSee('value="齋藤 道三"', false);
        $response->assertSee('value="123-4567"', false);
        $response->assertSee('value="岐阜県岐阜市金華山1-1"', false);
        $response->assertSee('value="稲葉山城"', false);
    }
}
