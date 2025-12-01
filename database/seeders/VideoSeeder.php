<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // DBファサードを使用
use Carbon\Carbon; // タイムスタンプの操作にCarbonを使用

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 投入するテストデータ
        $videos = [
            [
                'title' => 'Laravel入門：マイグレーションとEloquent',
                'name' => '山田 太郎',
                'file_name' => 'laravel_migration_01.mp4',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Vue.jsで始めるフロントエンド開発',
                'name' => '佐藤 花子',
                'file_name' => 'vuejs_frontend_02.mp4',
                'created_at' => Carbon::now()->subDay(1), // 1日前のデータ
                'updated_at' => Carbon::now()->subDay(1),
            ],
            [
                'title' => 'データベース設計の基本',
                'name' => '山田 太郎', // 投稿者名が重複してもOK
                'file_name' => 'db_design_03.mp4',
                'created_at' => Carbon::now()->subDays(3), // 3日前のデータ
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'PHP基礎から実用まで',
                'name' => '田中 次郎',
                'file_name' => 'php_beginner_04.mp4',
                'created_at' => Carbon::now()->subWeek(), // 1週間前のデータ
                'updated_at' => Carbon::now()->subWeek(),
            ],
            // 検索テスト用に、タイトルに「Laravel」を含むデータをもう一つ追加
            [
                'title' => '実践Laravel 10 REST API開発',
                'name' => '佐藤 花子',
                'file_name' => 'laravel10_api_05.mp4',
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(12),
            ],
        ];

        // データをテーブルに挿入
        DB::table('videos')->insert($videos);
    }
}