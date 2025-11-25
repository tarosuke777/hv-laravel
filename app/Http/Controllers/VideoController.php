<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator; 
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $directory = 'videos';
        $allFiles = Storage::disk('public')->files($directory);

        $mp4Files = array_filter($allFiles, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'mp4';
        });

        $perPage = 6;
        $page = $request->get('page', 1);

        $items = Collection::make($mp4Files);

        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

        $videoPaginator = new LengthAwarePaginator(
            $currentPageItems,
            $items->count(), // 全アイテム数
            $perPage,
            $page,
            ['path' => $request->url()] // ページネーションリンクの基本URLを設定
        );

        $videoList = array_map(function ($filePath) {

            $url = Storage::disk('public')->url($filePath); // ★ URLの値を $url 変数に格納

            Log::info('Generated Video URL', [
                'file_path' => $filePath, // 実ファイルの相対パス
                'public_url' => $url       // Webアクセス可能なURL
            ]);

            return [
                'name' => basename($filePath),
                'url' => $url,
            ];
        }, $videoPaginator->items());

        return view('videos.index', ['videoList' => $videoList, 'directory' => $directory, 'videos' => $videoPaginator]);

    }
}
