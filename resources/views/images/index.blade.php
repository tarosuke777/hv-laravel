{{-- resources/views/videos/index.blade.php --}}

{{-- ★ 1. layouts/app.blade.php を継承する ★ --}}
@extends('layouts.app')

{{-- ★ 2. ページタイトルを定義する (app.blade.phpの@yield('title')に挿入される) ★ --}}
@section('title', '画像一覧')

{{-- ★ 3. メインコンテンツを定義する (app.blade.phpの@yield('content')に挿入される) ★ --}}
@section('content')
    {{-- フィルター --}}
    @include('components.filters', ['indexRoute' => 'images.index'])

    <div x-data="infiniteScroll({{ $images->hasMorePages() ? 'true' : 'false' }})">
        {{-- 動画グリッド --}}
        <ul x-ref="container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 list-none p-0 m-0">
            @include('images._image_items', ['images' => $images])
        </ul>

        {{-- 読み込み検知用ターゲット --}}
        <div x-ref="loadMore" class="py-10 text-center">
            <div x-show="loading" class="animate-spin text-3xl text-blue-500 inline-block">↻</div>
            <p x-show="!hasMore && !loading" class="text-gray-500">すべての画像を読み込みました</p>
        </div>
    </div>
@endsection
