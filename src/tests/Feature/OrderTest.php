<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Stripe\Checkout\Session;
use Tests\TestCase;
use Mockery;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown():void
    {
        Mockery::close();
        parent::tearDown();
    }

    /*
     * 　商品購入機能
     */

    // 「購入する」ボタンを押すと購入が完了する（カード払い・stripe経由）
    public function test_user_can_compleate_item_perchase_with_card_stripe():void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'available']);
        
        // stripe APIのモック作成
        $mockSession = Mockery::mock('alias:' . Session::class);
        $mockSession->shouldReceive('create')->once()
            ->andReturn((object) ['url' => 'https://checkout.stripe.com/pay/cs_test_123']);
        
        $orderData = [
            'payment_method' => 'card',
            'delivery_postal_code' => '123-4567',
            'delivery_address' => '東京都渋谷区1-1-1',
            'delivery_building' => '❍❍ビル102',
        ];

        // 購入リクエスト（stripeの決済画面にリダイレクト）
        $response = $this->actingAs($user)
            ->post(route('purchase.store', ['item_id' => $item->id]), $orderData);
        $response->assertRedirect('https://checkout.stripe.com/pay/cs_test_123');
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'pending',
        ]);

        // 決済成功時
        $mockSession->shouldReceive('retrieve')->once()->with('cs_test_123')
            ->andReturn((object) [
                'metadata' => (object) [
                    'delivery_postal_code' => '123-4567',
                    'delivery_address' => '東京都渋谷区1-1-1',
                    'delivery_building' => '❍❍ビル102',
                ]
            ]);
        $successResponse = $this->actingAs($user)->get(route('purchase.success', [
            'item_id' => $item->id,
            'session_id' => 'cs_test_123',
        ]));
        $successResponse->assertRedirect(route('index'));

        // DBに注文情報が保存、商品ステータスが sold に
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);

    }

    // 「購入する」ボタンを押すと購入が完了する（コンビニ払い・stripe経由なし）
    public function test_user_can_compleate_item_perchase_with_convenience_store():void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'available']);

        $response = $this->actingAs($user)
            ->get(route('purchase',['item_id' => $item->id]));
        $response->assertStatus(200);

        $orderData = [
            'payment_method' => 'convenience',
            'delivery_postal_code' => '123-4567',
            'delivery_address' => '東京都渋谷区1-1-1',
            'delivery_building' => '❍❍ビル102'
        ];
        $response = $this->actingAs($user)
            ->post(route('purchase.store',['item_id' => $item->id]), $orderData);
        $response->assertRedirect(route('index'));

        $this->assertDatabaseHas('orders',[
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'convenience',
        ]);
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);
    }

    // 購入した商品は商品一覧画面にて「sold」と表示される
    public function test_purchase_item_show_sold_label_on_item_list():void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'available']);

        $this->actingAs($user)->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment_method' => 'convenience',
            'delivery_postal_code' => '123-4567',
            'delivery_address' => '東京都渋谷区1-1-1',
            'delivery_building' => '❍❍ビル102',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('sold');
    }

    // 「プロフィール/購入した商品一覧」に追加されている
    public function test_purchased_item_is_added_to_user_profile_buy_list():void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'status' => 'available',
        ]);

        $this->actingAs($user)->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment_method' => 'convenience',
            'delivery_postal_code' => '123-4567',
            'delivery_address' => '東京都渋谷区1-1-1',
            'delivery_building' => 'テストビル101',
        ]);

        $response = $this->actingAs($user)->get('/mypage?page=buy');
        $response->assertStatus(200);

        $response->assertSee('テスト商品');
    }

    /*
    *    支払方法選択機能
    */

    // 小計画面で変更が反映される
    public function test_user_can_select_payment_method():void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'status' => 'available'
        ]);

        $response = $this->actingAs($user)
            ->post(route('purchase.store', ['item_id' => $item->id]),[
                'payment_method' => 'convenience',
                'delivery_postal_code' => '123-4567',
                'delivery_address' => '東京都渋谷区1-1-1',
                'delivery_building' => '❍❍ビル102',
            ]);

        $response->assertStatus(302);
    }

    /*
    *    配送先変更機能
    */

    // 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
    public function test_updated_address_is_reflected_on_purchase_page():void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'status' => 'available'
        ]);

        $newAddressData = [
            'postal_code' => '987-6543', 
            'address' => '宮城県仙台市青葉区花京院1-1-1', 
            'building' => '❍❍マンション303',
        ];

        $this->actingAs($user)
            ->post(route('purchase.address.update', ['item_id' => $item->id]), $newAddressData);

        $user->refresh();

        $response = $this->actingAs($user)
            ->get(route('purchase', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('987-6543');
        $response->assertSee('宮城県仙台市青葉区花京院1-1-1');
        $response->assertSee('❍❍マンション303');
    }
    
    // 購入した商品に送付先住所が紐づいて登録される
    public function test_purchase_item_is_linked_with_delivery_address():void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'status' => 'available'
        ]);

                $newAddressData = [
            'postal_code' => '987-6543', 
            'address' => '宮城県仙台市青葉区花京院1-1-1', 
            'building' => '❍❍マンション303',
        ];

        $this->actingAs($user)
            ->post(route('purchase.address.update', ['item_id' => $item->id]), $newAddressData);

        $response = $this->actingAs($user)
            ->post(route('purchase.store', ['item_id' => $item->id]), [
                'payment_method' => 'convenience',
                'delivery_postal_code' => '987-6543',
                'delivery_address' => '宮城県仙台市青葉区花京院1-1-1',
                'delivery_building' => '❍❍マンション303',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'delivery_postal_code' => '987-6543',
            'delivery_address' => '宮城県仙台市青葉区花京院1-1-1',
            'delivery_building' => '❍❍マンション303',
        ]);
    }
}
