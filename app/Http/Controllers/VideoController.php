<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    /**
     * 動画一覧を表示し、検索機能を提供します。
     */
    public function index(Request $request)
    {

        $selectedTitle = $request->input('title');
        $selectedName = $request->input('name');

        $query = Video::search($request->only(['title', 'name']));
        $videos = $query->orderBy('created_at', 'asc')
            ->paginate(9);

        if ($request->ajax()) {
            return view('videos._video_items', compact('videos'))->render();
        }

        $uniqueTitles = Video::getUniqueTitles();
        $uniqueNames = Video::getUniqueNamesByTitle($selectedTitle);

        return view('videos.index', compact('videos', 'selectedTitle', 'uniqueTitles', 'selectedName', 'uniqueNames'));
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
            'data' => $video,
        ], 201);
    }

    /**
     * Videoモデル全体の最新作成日時をJSONで取得する
     *
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
                'new_name' => $video->name,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}
