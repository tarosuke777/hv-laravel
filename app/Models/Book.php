<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'total_pages',
    ];

    public function pages()
    {
        // ページ番号順に並んだ状態で取得できるようにする
        return $this->hasMany(BookPage::class)->orderBy('page_number');
    }
}
