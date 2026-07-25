<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([CategorySeeder::class]);

        $user = User::firstOrCreate([
            'email' => 'test123@example.com',
        ], [
            'name' => 'テストユーザー',
            'password' => bcrypt('password'),
        ]);

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'img_url' => 'MensClock_coachtech.jpg',
                'condition' => '良好',
                'status' => 'available',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'img_url' => 'HDD_coachtech.jpg',
                'condition' => '目立った傷や汚れなし',
                'status' => 'available',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'img_url' => 'onion_coachtech.jpg',
                'condition' => 'やや傷や汚れあり',
                'status' => 'available',
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'img_url' => 'LeatherShoes_coachtech.jpg',
                'condition' => '状態が悪い',
                'status' => 'available',
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'img_url' => 'Laptop_coachtech.jpg',
                'condition' => '良好',
                'status' => 'available',
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => null,
                'description' => '高音質のレコーディング用マイク',
                'img_url' => 'Mic_coachtech.jpg',
                'condition' => '目立った傷や汚れなし',
                'status' => 'available',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'img_url' => 'bag_coachtech.jpg',
                'condition' => 'やや傷や汚れあり',
                'status' => 'available',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => null,
                'description' => '使いやすいタンブラー',
                'img_url' => 'Tumbler_coachtech.jpg',
                'condition' => '状態が悪い',
                'status' => 'available',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'img_url' => 'コーヒーミル_coachtech.jpg',
                'condition' => '良好',
                'status' => 'available',
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'img_url' => 'メイクセット_coachtech.jpg',
                'condition' => '目立った傷や汚れなし',
                'status' => 'available',
            ],
        ];

        foreach ($items as $item) {
            $item['user_id'] = $user->id;
            Item::create($item);
        }
    }
}