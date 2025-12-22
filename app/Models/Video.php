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

    public function scopeSearch($query, array $params)
    {
        return $query
            ->when($params['title'] ?? null, fn($q, $title) => $q->where('title',$title))
            ->when($params['name'] ?? null, fn($q, $name) => $q->where('name', $name));
    }

    public function scopeHasName($query)
    {
        return $query->whereNotNull('name')->where('name', '!=', '');
    }

    /**
     * 一意のタイトルのみを取得するスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUniqueTitles($query)
    {
        return $query->select('title')->distinct();
    }

    /**
     * タイトルに基づいて、重複のない名前リストを配列で取得する
     */
    public static function getUniqueNamesByTitle(?string $title): array
    {
        if (empty($title)) {
            return [];
        }

        return self::search(['title' => $title])
                    ->hasName()
                    ->distinct()
                    ->pluck('name')
                    ->toArray();
    }

    /**
     * 現在のクエリ条件に基づいた最新の作成日時を取得する
     * * @param \Illuminate\Database\Eloquent\Builder $query
     * @return string|null
     */
    public function scopeGetMaxCreated($query)
    {
        return $query->max('created_at');
    }

    /**
     * モデルを配列/JSONに変換する際に追加するカスタム属性
     * @var array
     */
    protected $appends = ['external_url'];

    /**
     * file_name から外部URLを生成するアクセサ
     * ビューで $video->external_url としてアクセス可能
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
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
