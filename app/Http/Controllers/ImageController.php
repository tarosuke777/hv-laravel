<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $selectedTitle = $request->input('title');
        $selectedName = $request->input('name');

        $query = Image::search($request->only(['title', 'name']));
        $images = $query->orderBy('created_at', 'asc')
            ->paginate(9);

        $uniqueTitles = Image::getUniqueTitles();
        $uniqueNames = Image::getUniqueNamesByTitle($selectedTitle);

        return view('images.index', compact('images', 'selectedTitle', 'uniqueTitles', 'selectedName', 'uniqueNames'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_name' => 'required|string|unique:videos,file_name',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // $request->all() に created_at が含まれていれば、それが優先して保存されます
        $image = Image::create($request->all());

        return response()->json([
            'message' => 'Image registered with custom timestamps',
            'data' => $image,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImageRequest $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        //
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
        $maxDate = Image::getMaxCreated();

        return response()->json([
            'status' => 'success',
            'max_created_at' => $maxDate,
        ]);
    }

    public function updateName(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $image = Image::findOrFail($id);
            $image->name = $request->name;
            $image->save();

            return response()->json([
                'success' => true,
                'message' => '保存しました！',
                'new_name' => $image->name,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}
