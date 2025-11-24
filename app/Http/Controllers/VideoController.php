<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    public function index()
    {
        $directory = 'videos';
        
        $allFiles = Storage::disk('public')->files($directory);

        $mp4Files = array_filter($allFiles, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'mp4';
        });

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
        }, $mp4Files);

        return view('videos.index', ['videoList' => $videoList, 'directory' => $directory]);

    }
}
