@extends('layouts.app')

@section('title', '漫画一覧')

@section('content')
    @include('components.filters', ['indexRoute' => 'books.index'])

    <div x-data="infiniteScroll({{ $books->hasMorePages() ? 'true' : 'false' }})">
        {{-- 動画グリッド --}}
        <ul x-ref="container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 list-none p-0 m-0">
            @include('books._book_items', ['books' => $books])
        </ul>

        {{-- 読み込み検知用ターゲット --}}
        <div x-ref="loadMore" class="py-10 text-center">
            <div x-show="loading" class="animate-spin text-3xl text-blue-500 inline-block">↻</div>
            <p x-show="!hasMore && !loading" class="text-gray-500">すべての漫画を読み込みました</p>
        </div>
    </div>
@endsection
