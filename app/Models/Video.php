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

    public function scopeSearch($query, $search)
    {
        // $search が空でなければ検索条件を適用
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('titile', 'LIKE', "%{$search}%") // タイトルを部分一致で検索
                      ->orWhere('name', 'LIKE', "%{$search}%");  // 名前（投稿者名など）を部分一致で検索
            });
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
