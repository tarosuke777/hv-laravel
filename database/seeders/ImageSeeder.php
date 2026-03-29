<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // 投入するテストデータ
        $images = [
            [
                'title' => 'Laravel入門：マイグレーションとEloquent',
                'name' => '山田 太郎',
                'file_name' => 'laravel_migration_01.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        // データをテーブルに挿入
        DB::table('images')->insert($images);
    
    }
}
