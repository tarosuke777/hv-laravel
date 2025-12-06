{{-- resources/views/videos/index.blade.php --}}

{{-- ★ 1. layouts/app.blade.php を継承する ★ --}}
@extends('layouts.app') 

{{-- ★ 2. ページタイトルを定義する (app.blade.phpの@yield('title')に挿入される) ★ --}}
@section('title', 'MP4 動画一覧')

{{-- ★ 3. メインコンテンツを定義する (app.blade.phpの@yield('content')に挿入される) ★ --}}
@section('content')

    <h1 class="text-3xl font-bold text-gray-900 mb-6">🎬 MP4 ファイル一覧</h1>
    
    <div x-data="{ open: false }" class="mb-8 border p-4 bg-gray-50 rounded-lg">
        <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
            <h2 class="text-xl font-semibold">
                タイトルで絞り込む
                @if ($selectedTitle)
                    <span class="text-sm font-normal text-blue-600 ml-2"> (現在: {{ $selectedTitle }})</span>
                @endif
            </h2>
            
            {{-- 展開アイコン（openの状態に応じて回転） --}}
            <svg class="w-5 h-5 transition-transform duration-300" 
                 :class="{ 'rotate-180': open, 'rotate-0': !open }" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        <div x-show="open" x-collapse.duration.300ms class="mt-4 pt-4 border-t border-gray-300">
            <div class="flex flex-wrap gap-2">

                {{-- 1. 全て表示リンク --}}
                <a href="{{ route('videos.indexV2') }}" 
                class="px-3 py-1 text-sm rounded-full transition duration-150 
                         {{ $selectedTitle ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-blue-600 text-white font-bold' }}">
                    全ての動画 ({{ count($uniqueTitles) }})
                </a>

                {{-- 2. 重複のないタイトルごとのリンク --}}
                @foreach ($uniqueTitles as $title)
                    {{-- リンクURL: /videos?title=【URLエンコードされたタイトル】 --}}
                    <a href="{{ route('videos.indexV2', ['title' => $title]) }}"
                    class="px-3 py-1 text-sm rounded-full transition duration-150 
                             {{ $selectedTitle === $title ? 'bg-blue-600 text-white font-bold' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        {{ $title }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if ($videos->count() > 0)
        <div class="mb-8">
            {{ $videos->appends(request()->query())->links() }}
        </div>

        {{-- Tailwind CSS のクラスで横3列のグリッドレイアウトを定義 --}}
        <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 list-none p-0 m-0">
            @foreach ($videos as $video)
                <li class="bg-white shadow-xl rounded-xl overflow-hidden p-5 flex flex-col items-center">
                    
                    {{-- 動画タイトル --}}
                    {{-- 修正: 配列アクセスからプロパティアクセスへ --}}
                    <strong class="text-lg font-semibold mb-3">{{ $video->file_name }}</strong>
                    
                    {{-- 動画プレーヤー --}}
                    {{-- 修正: $video['url'] から $video->external_url へ --}}
                    <video controls class="w-full h-auto rounded-lg mb-4 video-preview-target" preload="metadata">
                        <source src="{{ $video->external_url }}" type="video/mp4">
                        お使いのブラウザは動画タグに対応していません。
                    </video>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const videoElements = document.querySelectorAll('.video-preview-target');

                            videoElements.forEach(function(videoElement) {
                                // 動画のデータが十分ロードされ、再生準備が整ったときに実行
                                videoElement.addEventListener('loadeddata', function() {
                                    // readyStateが2 (HAVE_CURRENT_DATA) 以上であることを確認
                                    if (videoElement.readyState >= 2) {
                                        // 再生を一時停止し、最初のフレームを表示
                                        videoElement.currentTime = 0; // 念のため0秒にシーク
                                        videoElement.pause();
                                    }
                                });
                            });
                        });
                    </script>

                    {{-- 再生リンク --}}
                    {{-- 修正: $video['url'] から $video->external_url へ --}}
                    <a href="{{ $video->external_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium transition duration-150">
                        別タブで再生
                    </a>
                </li>
            @endforeach
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