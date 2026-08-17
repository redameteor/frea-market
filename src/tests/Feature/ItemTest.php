<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /*
    * 　商品一覧取得
    */

    // すべての商品が表示される
    public function test_can_view_all_items_on_item_page(): void
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');
        $response->assertStatus(200);
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    // 購入済み商品に「sold」ラベルが表示される
    public function test_sold_label_is_displayed_for_purchased_items(): void
    {
        $item = Item::factory()->create(['status' => 'sold']);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('sold');
    }

    // 自分が出品した商品は一覧に表示されない
    public function test_items_list_does_not_include_user_own_items(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id, 'name' => 'User Item']);

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertDontSee($item->name);
    }

    /*
    * 　マイリスト一覧取得
    */

    // いいねした商品だけが表示される
    public function test_user_can_see_only_liked_items_in_mylist(): void
    {
        $user = User::factory()->create();
        $likedItem = Item::factory()->create(['name' => 'Liked Item']);
        $unlikedItem = Item::factory()->create(['name' => 'Unliked Item']);

        $user->likes()->attach($likedItem->id);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        // 画面全体ではなく、mylist-content内に絞って確認
        $response->assertSeeInOrder(['mylist-content', 'Liked Item']);

        // mylist-contentの中にUnliked Itemが含まれていないことを確認
        $content = $response->getContent();
        preg_match('/<div class="content-area mylist-content">(.*?)<\/div>/s', $content, $matches);
        $mylistHtml = $matches[1] ?? '';

        $this->assertStringContainsString('Liked Item', $mylistHtml);
        $this->assertStringNotContainsString('Unliked Item', $mylistHtml);
    }

    // 購入済み商品は「sold」ラベルが表示される
    public function test_sold_label_is_displayed_for_purchased_items_in_mylist(): void
    {
        $user = User::factory()->create();
        $soldItem = Item::factory()->create(['status' => 'sold']);
        $user->likes()->attach($soldItem->id);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee('sold');
    }

    // 未認証の場合は何も表示されない
    public function test_guest_user_sees_nothing_in_mylist(): void
    {
        $item = Item::factory()->create(['name' => 'Test Item']);

        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);

        $content = $response->getContent();
        preg_match('/<div class="content-area mylist-content">(.*?)<\/div>/s', $content, $matches);
        $mylistHtml = $matches[1] ?? '';

        $this->assertStringNotContainsString($item->name, $mylistHtml);
    }

    /*
    * 　商品検索機能
    */

    // 「商品名」で部分一致検索ができる
    public function test_items_can_be_searched_by_name(): void
    {
        $item1 = Item::factory()->create(['name' => 'UniqueItem']);
        $item2 = Item::factory()->create(['name' => 'AnotherItem']);
        // 検索キーワードに一致する商品だけが表示される
        $response = $this->get('/?keyword=' . urlencode('Uni'));

        $response->assertStatus(200);
        $response->assertSee('UniqueItem');
        $response->assertDontSee('AnotherItem');
    }

    // 検索状態がマイリストでも保持される
    public function test_search_keyword_is_retained_in_mylist_tab(): void
    {
        $user = User::factory()->create();

        $matchItem = Item::factory()->create(['name' => 'UniqueItem']);
        $unmatchItem = Item::factory()->create(['name' => 'AnotherItem']);
        $user->likes()->attach([$matchItem->id, $unmatchItem->id]);

        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=' . urlencode('Uni'));
        $response->assertStatus(200);

        $content = $response->getContent();
        preg_match('/<div class="content-area mylist-content">(.*?)<\/div>/s', $content, $matches);
        $mylistHtml = $matches[1] ?? '';

        $this->assertStringContainsString('UniqueItem', $mylistHtml);
        $this->assertStringNotContainsString('AnotherItem', $mylistHtml);
        // 検索キーワードがフォームに保持されているか確認
        $response->assertSee('value="Uni"', false); 
    }

    /*
    * 　商品詳細画面取得
    */

    // 必要な情報がすべて表示される
    public function test_item_detail_page_displays_all_necessary_information(): void
    {
        $seller = User::factory()->create(['name' => 'Seller']);
        $commenter = User::factory()->create(['name' => 'Commenter']);

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'Test Item',
            'brand' => 'Test Brand',
            'condition' => 'New',
            'price' => 1000,
            'description' => 'This is a test item.',
            'status' => 'available',
        ]);

        $liker = User::factory()->create();
        $liker->likes()->attach($item->id);

        Comment::create([
            'item_id' => $item->id,
            'user_id' => $commenter->id,
            'content' => 'This is a comment.',
        ]);

        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        $response->assertSee('Test Item');
        $response->assertSee('Test Brand');
        $response->assertSee('New');
        $response->assertSee('1,000');
        $response->assertSee('This is a test item.');
        $response->assertSee('This is a comment.');
        $response->assertSee('1');
    }

    // 複数選択されたカテゴリーが表示される
    public function test_item_detail_page_displays_multiple_categories(): void
    {
        $category1 = Category::create(['name' => 'メンズ']);
        $category2 = Category::create(['name' => 'ファッション']);

        $item = Item::factory()->create(['name' => 'Test Item']);
        $item->categories()->sync([$category1->id, $category2->id]);
        $item->unsetRelation('categories'); // キャッシュをクリアして再取得

        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        $response->assertSee('メンズ');
        $response->assertSee('ファッション');
    }

    /*
    *　 いいね機能
    */

    // いいねアイコンを押すことによって、いいねした商品として登録する
    public function test_user_can_like_an_item(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        // いいねアイコンを押す（POSTリクエスト）
        $response = $this->actingAs($user)->postJson("/items/{$item->id}/like");
        $response->assertStatus(200)->assertJson([
            'isLiked' => true,
            'likeCount' => 1,
        ]);

        // データベースにいいねが登録されているか確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $detailResponse = $this->actingAs($user)->get("/item/{$item->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('1');
        $detailResponse->assertSee('ハートロゴ_ピンク.png');
    }

    // 追加済みのアイコンは色が変化する
    public function test_like_icon_changes_color_when_liked(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $user->likes()->attach($item->id);

        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $response->assertStatus(200);
        $response->assertSee('ハートロゴ_ピンク.png');
        $response->assertSee('いいね済み');
    }

    // 再度いいねアイコンを押すことによって、いいねを解除できる
    public function test_user_can_unlike_an_item(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $user->likes()->attach($item->id);

        $response = $this->actingAs($user)->postJson("/items/{$item->id}/like");
        $response->assertStatus(200)->assertJson([
            'isLiked' => false,
            'likeCount' => 0,
        ]);

        // データベースからいいねが削除されているか確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $detailResponse = $this->actingAs($user)->get("/item/{$item->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('0');
        $detailResponse->assertSee('ハートロゴ_デフォルト.png');
    }

    /*
    * 　コメント送信機能
    */

    // ログイン済みのユーザーはコメントを送信できる
    public function test_authenticated_user_can_post_comment(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        // コメントを送信する（POSTリクエスト）
        $response = $this->actingAs($user)->post("/items/{$item->id}/comment", [
            'content' => 'This is a comment.',
        ]);
        // コメントがデータベースに保存されているか確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'This is a comment.',
        ]);

        $detailResponse = $this->get("/item/{$item->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('This is a comment.');
        $detailResponse->assertSee($user->name);
        $detailResponse->assertSee('1');
    }

    // 未認証のユーザーはコメントを送信できない
    public function test_guest_user_cannot_post_comment(): void
    {
        $item = Item::factory()->create();
        $response = $this->post("/items/{$item->id}/comment", [
            'content' => 'This is a comment.',
        ]);
        $response->assertRedirect('/login');
        // データベースにコメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'This is a comment.',
        ]);
    }

    // コメントが入力されていない場合、バリデーションメッセージが表示される
    public function test_comment_content_is_required(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $response = $this->actingAs($user)
            ->from("/item/{$item->id}")
            ->post("/items/{$item->id}/comment", [
            'content' => '',
        ]);

        $response->assertRedirect("/item/{$item->id}");
        $response->assertSessionHasErrors(['content']);
    }

    // コメントが255文字を超える場合、バリデーションメッセージが表示される
    public function test_comment_max_length_validation(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $longContent = str_repeat('a', 256); // 256文字の文字列を作成

        $response = $this->actingAs($user)
            ->from("/item/{$item->id}")
            ->post("/items/{$item->id}/comment", [
                'content' => $longContent,
            ]);

        $response->assertRedirect("/item/{$item->id}");
        $response->assertSessionHasErrors(['content']);
    }

    /*
    *    出品商品情報登録
    */

    // 商品出品画面にて必要な情報が保存できること
    //（カテゴリー・商品の状態・商品名・ブランド名・商品の説明・販売価格）
    public function test_user_can_create_item_with_required_information():void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $category = Category::create(['name' =>'ファッション']);

        $response = $this->actingAs($user)->get(route('sell.create'));
        $response->assertStatus(200);

        $dummyImage = UploadedFile::fake()->create('test_item.jpg', 100, 'image/jpeg');
        $itemData = [
            'name' => 'テスト商品',
            'brand' => 'テストbrand',
            'description' => 'テスト商品説明',
            'price' => 5000,
            'condition' => '良好',
            'category_ids' => [$category->id],
            'img_url' => $dummyImage,
        ];

        $storeResponse = $this->actingAs($user)->post(route('sell.store'), $itemData);
        $storeResponse->assertRedirect(route('index'));

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'brand' => 'テストbrand',
            'description' => 'テスト商品説明',
            'price' => 5000,
            'condition' => '良好',
            'status' => 'available',
        ]);

        $item = Item::where('name', 'テスト商品')->first();
        $this->assertNotNull($item);
        $this->assertTrue($item->categories->contains($category->id));

        Storage::disk('public')->assertExists($item->img_url);

    }
}