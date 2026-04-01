{{-- resources/views/videos/index.blade.php --}}

{{-- ★ 1. layouts/app.blade.php を継承する ★ --}}
@extends('layouts.app') 

{{-- ★ 2. ページタイトルを定義する (app.blade.phpの@yield('title')に挿入される) ★ --}}
@section('title', 'MP4 動画一覧')

{{-- ★ 3. メインコンテンツを定義する (app.blade.phpの@yield('content')に挿入される) ★ --}}
@section('content')    
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
                <a href="{{ route('videos.index') }}" 
                class="px-3 py-1 text-sm rounded-full transition duration-150 
                         {{ $selectedTitle ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-blue-600 text-white font-bold' }}">
                    全ての動画 ({{ count($uniqueTitles) }})
                </a>

                {{-- 2. 重複のないタイトルごとのリンク --}}
                @foreach ($uniqueTitles as $title)
                    {{-- リンクURL: /videos?title=【URLエンコードされたタイトル】 --}}
                    <a href="{{ route('videos.index', ['title' => $title]) }}"
                    class="px-3 py-1 text-sm rounded-full transition duration-150 
                             {{ $selectedTitle === $title ? 'bg-blue-600 text-white font-bold' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        {{ $title }}
                    </a>
                @endforeach
            </div>
    
            {{-- ★ 追加：名前での絞り込みリスト --}}
            @if($selectedTitle && count($uniqueNames) > 0)
                <div class="mt-6 pt-4 border-t border-dashed border-gray-300">
                    <p class="text-xs font-bold text-gray-500 mb-2">表示名でさらに絞り込む</p>
                    <div class="flex flex-wrap gap-2">
                        {{-- 全て表示（名前の絞り込み解除） --}}
                        <a href="{{ route('videos.index', ['title' => $selectedTitle]) }}" 
                        class="px-3 py-1 text-sm rounded-full transition {{ !$selectedName ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            全ての「{{ $selectedTitle }}」
                        </a>

                        @foreach ($uniqueNames as $name)
                            <a href="{{ route('videos.index', ['title' => $selectedTitle, 'name' => $name]) }}"
                            class="px-3 py-1 text-sm rounded-full transition {{ $selectedName === $name ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif


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
                    <!-- <strong class="text-lg font-semibold mb-3">{{ $video->file_name }}</strong> -->
                    <!-- <strong class="text-lg font-semibold mb-3">{{ $video->name ?? $video->file_name }}</strong> -->
                    <strong class="text-lg font-semibold mb-3">{{ $video->full_title }}</strong>
                    
                    {{-- 動画プレーヤー --}}
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

                    <div x-data="{ 
                            name: '{{ $video->name ?? '' }}', 
                            loading: false, 
                            showSuccess: false,
                            errorMessage: '',
                            async updateVideoName() {
                                this.loading = true;
                                this.errorMessage = '';
                                try {
                                    let response = await fetch('{{ route('videos.updateName', $video->id) }}', {
                                        method: 'PATCH',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ name: this.name })
                                    });
                                    
                                    let result = await response.json();

                                    if (response.ok) {
                                        this.showSuccess = true;
                                        setTimeout(() => this.showSuccess = false, 2000); // 2秒で消す
                                    } else {
                                        {{-- ★サーバー側でバリデーションエラー(422)が起きた場合 --}}
                                        this.errorMessage = result.message || '更新に失敗しました';
                                    }
                                } catch (e) {
                                    this.errorMessage = '通信エラーが発生しました';
                                }
                                this.loading = false;
                            }
                        }" 
                        class="w-full mb-4 px-2">

                        <div class="relative flex gap-2">
                            <input type="text" 
                                x-model="name"
                                @keydown.enter="updateVideoName()"
                                placeholder="表示名を入力"
                                class="flex-1 px-3 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                                :class="{ 'bg-gray-100': loading }">
                            
                            <button @click="updateVideoName()" 
                                    :disabled="loading"
                                    class="shrink-0 px-4 py-2 bg-gray-800 text-white text-xs font-bold rounded hover:bg-black transition flex items-center justify-center min-w-[64px] disabled:opacity-50">
                                <span x-show="!loading">更新</span>
                                <span x-show="loading" class="animate-spin text-lg">↻</span>
                            </button>

                            <p x-show="errorMessage" x-text="errorMessage" class="text-red-500 text-[10px] text-left mt-1"></p>

                            <div x-show="showSuccess" 
                                x-transition 
                                class="absolute -top-8 left-0 right-0 text-center text-green-600 text-xs font-bold bg-green-50 rounded py-1 border border-green-200">
                                ✅ 保存しました
                            </div>
                        </div>
                    </div>

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