<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'name',
        'file_name',
    ];

    public function scopeSearchByTitle($query, $title)
    {
        // $search が空でなければ検索条件を適用
        if ($title) {
            $query->where('title', 'LIKE', "%{$title}%");
        }
        return $query;
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
}
