<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'name',
        'file_name',
        'created_at', 
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['external_url'];

    // --- Scopes (クエリの部品) ---

    /**
     * 汎用検索フィルタ
     */
    public function scopeSearch($query, array $params)
    {
        return $query
            ->when($params['title'] ?? null, fn($q, $title) => $q->where('title',$title))
            ->when($params['name'] ?? null, fn($q, $name) => $q->where('name', $name));
    }

    /**
     * 名前が有効（NULLでも空文字でもない）なデータに絞り込む
     */
    public function scopeHasName($query)
    {
        return $query->whereNotNull('name')->where('name', '!=', '');
    }

    /**
     * 最新の作成日時を取得する
     */
    public function scopeGetMaxCreated($query)
    {
        return $query->max('created_at');
    }

    // --- Static Methods (最終的なデータ取得) ---
    
    /**
     * 重複のないタイトル一覧を取得
     */
    public static function getUniqueTitles(): array
    {
        return self::distinct()->pluck('title')->toArray();
    }

    /**
     * 特定のタイトルに紐づく、重複のない名前一覧を取得
     */
    public static function getUniqueNamesByTitle(?string $title): array
    {
        if (empty($title)) return [];

        return self::search(['title' => $title])
                    ->hasName()
                    ->distinct()
                    ->pluck('name')
                    ->toArray();
    }

    // --- Attributes (アクセサ / 算出プロパティ) ---

    /**
     * 外部配信URLを取得
     * $video->external_url
     */
    protected function externalUrl(): Attribute
    {
        // ベースURLを定義。ベストプラクティスとしてconfig()から取得することを推奨
        // 今回はハードコード（直書き）しますが、プロジェクトの設定に合わせて変更してください
        $baseUrl = config('services.video_host', 'http://192.168.10.11/');

        return Attribute::make(
            // get: アクセスされたときに実行されるロジック
            get: fn (mixed $value, array $attributes) => $baseUrl . $attributes['file_name'],
        );
    }

    /**
     * タイトルと名前を結合したフルタイトルを取得
     * $video->full_title
     */
    protected function fullTitle(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => 
                $attributes['name'] 
                    ? "{$attributes['title']} - {$attributes['name']}" 
                    : $attributes['title']
        );
    }
}
