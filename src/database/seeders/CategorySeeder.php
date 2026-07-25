<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => '家電'],
            ['name' => 'キッチン'],
            ['name' => 'ファッション'],
            ['name' => '本'],
            ['name' => 'スポーツ'],
            ['name' => 'ゲーム'],
            ['name' => 'おもちゃ'],
            ['name' => 'インテリア'],
            ['name' => 'コスメ'],
            ['name' => 'ハンドメイド'],
            ['name' => 'アクセサリー'],
            ['name' => 'ベビー・キッズ'],
            ['name' => 'レディース'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate($category);
        }
    }
}
