{{-- resources/views/videos/index.blade.php --}}

{{-- ★ 1. layouts/app.blade.php を継承する ★ --}}
@extends('layouts.app') 

{{-- ★ 2. ページタイトルを定義する (app.blade.phpの@yield('title')に挿入される) ★ --}}
@section('title', '動画一覧')

{{-- ★ 3. メインコンテンツを定義する (app.blade.phpの@yield('content')に挿入される) ★ --}}
@section('content')    
    {{-- フィルター --}}
    @include('videos._filters_part')
    
    @if ($videos->count() > 0)
        <div class="mb-8">
            {{ $videos->appends(request()->query())->links() }}
        </div>

        {{-- 動画グリッド --}}
        <ul id="video-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 list-none p-0 m-0">
            @include('videos._video_items', ['videos' => $videos])
        </ul>

        <div class="mt-8">
            {{ $videos->appends(request()->query())->links() }}
        </div>
    @else
        <p class="text-gray-500">
            @if($selectedTitle)
                「{{ $selectedTitle }}」の動画は見つかりませんでした。
            @else
                MP4 ファイルが見つかりません。
            @endif
        </p>
    @endif

@endsection