<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator; 
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

use App\Models\Video;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $directory = 'videos';
        $allFiles = Storage::disk('public')->files($directory);

        Log::info('Storage All Files List', [
            'count' => count($allFiles),
            // 全リストをログに出力（リストが非常に長い場合は注意）
            'files' => $allFiles 
        ]);

        $mp4Files = array_filter($allFiles, function ($file) {

            $fileName = basename($file);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $isMp4 = ($extension === 'mp4');

            Log::info('MP4 Filter Check', [
                'fileName' => $fileName,
                'fullPath' => $file,
                'extension' => $extension,
                'isMp4' => $isMp4 ? 'PASS' : 'FAIL'
            ]);

            return $isMp4;
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

    /**
     * 動画一覧を表示し、検索機能を提供します。
     */
    public function indexV2(Request $request)
    {

        $selectedTitle = $request->input('title');
        $selectedName = $request->input('name');

        $query = Video::search($request->only(['title', 'name']));
        $videos = $query->orderBy('created_at', 'asc')
                        ->paginate(9);

        $uniqueTitles = Video::uniqueTitles()->pluck('title')->toArray();
        $uniqueNames = Video::getUniqueNamesByTitle($selectedTitle);

        return view('videos.indexV2', compact('videos', 'selectedTitle', 'uniqueTitles', 'selectedName', 'uniqueNames'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'file_name' => 'required|string|unique:videos,file_name',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // $request->all() に created_at が含まれていれば、それが優先して保存されます
        $video = Video::create($request->all());

        return response()->json([
            'message' => 'Video registered with custom timestamps',
            'data' => $video
        ], 201);
    }

    /**
     * Videoモデル全体の最新作成日時をJSONで取得する
     * * @return \Illuminate\Http\JsonResponse
     */
    public function fetchMaxTimestamp()
    {
        // scopeGetMaxCreatedを使用して最大値を取得
        // スコープを呼び出す際は「scope」を除いたキャメルケースで記述します
        $maxDate = Video::getMaxCreated();

        return response()->json([
            'status' => 'success',
            'max_created_at' => $maxDate,
        ]);
    }

    // VideoController.php

    public function updateName(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $video = Video::findOrFail($id);
            $video->name = $request->name;
            $video->save();

            return response()->json([
                'success' => true,
                'message' => '保存しました！',
                'new_name' => $video->name
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}
