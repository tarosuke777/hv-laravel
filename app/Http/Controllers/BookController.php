<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Log::info('--- 漫画一覧データ (index) ---');
        $selectedTitle = $request->input('title');

        $query = Book::with('pages'); // Eager Loadで最初の1枚（表紙）などを取得しやすくする

        if ($request->title) {
            $query->where('title', $request->title);
        }

        $books = $query->paginate(12);
        $uniqueTitles = Book::pluck('title')->unique();

        return view('books.index', compact('books', 'selectedTitle', 'uniqueTitles'));
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
    public function store(StoreBookRequest $request)
    {
        try {
            // トランザクション開始
            $result = DB::transaction(function () use ($request) {
                // 1. 本の基本情報を保存
                $book = Book::create([
                    'title' => $request->title,
                    'author' => $request->author,
                    'total_pages' => count($request->pages), // ページ数も自動カウントして保存
                ]);

                // 2. ページ情報を一括保存（リレーション経由）
                $book->pages()->createMany($request->pages);

                return $book->load('pages'); // ページ情報を含めて返す
            });

            return response()->json($result, 201);

        } catch (\Exception $e) {
            // 失敗した場合は自動でロールバックされ、ここに来る
            Log::error('Bookの登録に失敗しました。', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(), // スタックトレース
            ]);

            return response()->json(['error' => '登録に失敗しました'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::findOrFail($id);
        $pages = $book->pages()->orderBy('page_number')->get();

        return view('books.show', compact('book', 'pages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
