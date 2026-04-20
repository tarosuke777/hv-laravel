{{-- resources/views/videos/index.blade.php --}}

{{-- ★ 1. layouts/app.blade.php を継承する ★ --}}
@extends('layouts.app')

{{-- ★ 2. ページタイトルを定義する (app.blade.phpの@yield('title')に挿入される) ★ --}}
@section('title', '画像一覧')

{{-- ★ 3. メインコンテンツを定義する (app.blade.phpの@yield('content')に挿入される) ★ --}}
@section('content')
    {{-- フィルター --}}
    @include('components.filters', ['indexRoute' => 'images.index'])

    @if ($images->count() > 0)
        <div class="mb-8">
            {{ $images->appends(request()->query())->links() }}
        </div>

        {{-- Tailwind CSS のクラスで横3列のグリッドレイアウトを定義 --}}
        <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 list-none p-0 m-0">
            @include('images._image_items', ['images' => $images])
        </ul>

        <div class="mt-8">
            {{ $images->appends(request()->query())->links() }}
        </div>
    @else
        <p class="text-gray-500">
            @if ($selectedTitle)
                「{{ $selectedTitle }}」の動画は見つかりませんでした。
            @else
                画像 ファイルが見つかりません。
            @endif
        </p>
    @endif

@endsection
