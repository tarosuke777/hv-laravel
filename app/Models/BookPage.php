<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookPage extends Model
{
    protected $fillable = [
        'page_number',
        'file_path',
    ];

    public function book()
    {
        // 「BookPageはひとつのBookに属している」という定義
        return $this->belongsTo(Book::class);
    }

    // --- Attributes (アクセサ / 算出プロパティ) ---

    /**
     * 外部配信URLを取得
     * $video->external_url
     */
    protected function externalUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                // ベースURLを取得
                $baseUrl = config('services.video_host', 'http://192.168.10.11/');

                // 親(Book)がロードされていればそのタイトルを、なければ'unknown'などを使用
                // ※ $this を使うことでロード済みのリレーションを参照できます
                $title = $this->book ? $this->book->title : 'unknown';

                // ファイル名を取得
                $fileName = basename($attributes['file_path']);

                // URLを組み立て
                return Str::finish($baseUrl, '/books/').rawurlencode($title).'/'.rawurlencode($fileName);
            }
        );
    }
}
