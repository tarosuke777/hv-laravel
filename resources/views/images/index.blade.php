{{-- resources/views/videos/index.blade.php --}}

{{-- ★ 1. layouts/app.blade.php を継承する ★ --}}
@extends('layouts.app') 

{{-- ★ 2. ページタイトルを定義する (app.blade.phpの@yield('title')に挿入される) ★ --}}
@section('title', '画像一覧')

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
                <a href="{{ route('images.index') }}" 
                class="px-3 py-1 text-sm rounded-full transition duration-150 
                         {{ $selectedTitle ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-blue-600 text-white font-bold' }}">
                    全ての画像 ({{ count($uniqueTitles) }})
                </a>

                {{-- 2. 重複のないタイトルごとのリンク --}}
                @foreach ($uniqueTitles as $title)
                    {{-- リンクURL: /images?title=【URLエンコードされたタイトル】 --}}
                    <a href="{{ route('images.index', ['title' => $title]) }}"
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
                        <a href="{{ route('images.index', ['title' => $selectedTitle]) }}" 
                        class="px-3 py-1 text-sm rounded-full transition {{ !$selectedName ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            全ての「{{ $selectedTitle }}」
                        </a>

                        @foreach ($uniqueNames as $name)
                            <a href="{{ route('images.index', ['title' => $selectedTitle, 'name' => $name]) }}"
                            class="px-3 py-1 text-sm rounded-full transition {{ $selectedName === $name ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif


        </div>
    </div>

    @if ($images->count() > 0)
        <div class="mb-8">
            {{ $images->appends(request()->query())->links() }}
        </div>

        {{-- Tailwind CSS のクラスで横3列のグリッドレイアウトを定義 --}}
        <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 list-none p-0 m-0">
            @foreach ($images as $image)
                <li class="bg-white shadow-xl rounded-xl overflow-hidden p-5 flex flex-col items-center">
                    
                    {{-- 画像タイトル --}}
                    <strong class="text-lg font-semibold mb-3">{{ $image->full_title }}</strong>
                    
                    <div class="relative aspect-video bg-gray-100 rounded-lg overflow-hidden mb-5 shadow-inner flex items-center justify-center">
                        <a href="{{ $image->external_url }}?external=true" target="_blank" class="block w-full h-full">    
                            <img src="{{ $image->external_url }}" 
                                        alt="{{ $image->full_title }}" 
                                        class="w-full h-full object-contain transition-transform duration-300 hover:scale-105">
                        </a>
                    </div>

                    <div x-data="{ 
                            name: '{{ $image->name ?? '' }}', 
                            loading: false, 
                            showSuccess: false,
                            errorMessage: '',
                            async updateImageName() {
                                this.loading = true;
                                this.errorMessage = '';
                                try {
                                    let response = await fetch('{{ route('images.updateName', $image->id) }}', {
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
                                @keydown.enter="updateImageName()"
                                placeholder="表示名を入力"
                                class="flex-1 px-3 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                                :class="{ 'bg-gray-100': loading }">
                            
                            <button @click="updateImageName()" 
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
                </li>
            @endforeach
        </ul>

        <div class="mt-8">
            {{ $images->appends(request()->query())->links() }}
        </div>
    @else
        <p class="text-gray-500">
            @if($selectedTitle)
                「{{ $selectedTitle }}」の動画は見つかりませんでした。
            @else
                画像 ファイルが見つかりません。
            @endif
        </p>
    @endif

@endsection