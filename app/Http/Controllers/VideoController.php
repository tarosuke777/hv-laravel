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

        $extractTitle = function ($filePath) {
            $fileName = basename($filePath, '.mp4');

            // タイムスタンプ部分を削除してタイトルを抽出
            $title = preg_replace('/\s\d{4}-\d{2}-\d{2}\s\d{2}-\d{2}-\d{2}$/', '', $fileName);
            
            // ★★★ ログ埋め込み箇所 1: タイトル抽出の確認 ★★★
            Log::info('Title Extraction Check', [
                'filePath' => $filePath,
                'extractedTitle' => $title
            ]);
            
            return $title;
        };

        $uniqueTitles = collect($mp4Files)
            ->map($extractTitle) // タイトルを抽出
            ->unique()           // 重複を除去
            ->sort()             // タイトル順にソート
            ->values();          // キーをリセット

        $selectedTitle = $request->query('title'); // URLの ?title=xxx を取得

        if ($selectedTitle) {
            $mp4Files = array_filter($mp4Files, function ($filePath) use ($selectedTitle, $extractTitle) {
                $fileTitle = $extractTitle($filePath); // タイトルを取得
                $isMatch = ($fileTitle === $selectedTitle); // 比較結果

                // ★★★ ログ埋め込み箇所 2: フィルタリング判定の確認 ★★★
                Log::info('Filtering Check', [
                    'file' => basename($filePath),
                    'fileTitle' => $fileTitle,
                    'selectedTitle' => $selectedTitle,
                    'isMatch' => $isMatch ? 'PASS' : 'FAIL'
                ]);

                // ファイルのタイトルが選択されたタイトルと一致するか比較
                return $isMatch;
            });
        }


        $perPage = 6;
        $page = $request->get('page', 1);

        $items = Collection::make($mp4Files);

        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

        $videoPaginator = new LengthAwarePaginator(
            $currentPageItems,
            $items->count(), // 全アイテム数
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()] // ページネーションリンクの基本URLを設定
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

        return view('videos.index', [
            'videoList' => $videoList, 
            'directory' => $directory, 
            'videos' => $videoPaginator,
            'uniqueTitles' => $uniqueTitles,
            'selectedTitle' => $selectedTitle,
        ]);

    }
}
