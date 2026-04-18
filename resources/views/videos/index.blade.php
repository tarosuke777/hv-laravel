{{-- resources/views/videos/index.blade.php --}}

{{-- ★ 1. layouts/app.blade.php を継承する ★ --}}
@extends('layouts.app')

{{-- ★ 2. ページタイトルを定義する (app.blade.phpの@yield('title')に挿入される) ★ --}}
@section('title', '動画一覧')

{{-- ★ 3. メインコンテンツを定義する (app.blade.phpの@yield('content')に挿入される) ★ --}}
@section('content')
    {{-- フィルター --}}
    @include('videos._filters_part')

    <div x-data="infiniteScroll({{ $videos->hasMorePages() ? 'true' : 'false' }})">
    {{-- 動画グリッド --}}
        <ul x-ref="container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 list-none p-0 m-0">
            @include('videos._video_items', ['videos' => $videos])
        </ul>

        {{-- 読み込み検知用ターゲット --}}
        <div x-ref="loadMore" class="py-10 text-center">
            <div x-show="loading" class="animate-spin text-3xl text-blue-500 inline-block">↻</div>
            <p x-show="!hasMore && !loading" class="text-gray-500">すべての動画を読み込みました</p>
        </div>
    </div>
@endsection
