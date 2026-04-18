{{-- resources/views/videos/index.blade.php --}}

{{-- ★ 1. layouts/app.blade.php を継承する ★ --}}
@extends('layouts.app')

{{-- ★ 2. ページタイトルを定義する (app.blade.phpの@yield('title')に挿入される) ★ --}}
@section('title', '動画一覧')

{{-- ★ 3. メインコンテンツを定義する (app.blade.phpの@yield('content')に挿入される) ★ --}}
@section('content')
    {{-- フィルター --}}
    @include('videos._filters_part')

    <div x-data="infiniteScroll()" x-init="init()">
        {{-- 動画グリッド --}}
        <ul id="video-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 list-none p-0 m-0">
            @include('videos._video_items', ['videos' => $videos])
        </ul>

        {{-- 読み込み検知用ターゲット --}}
        <div x-ref="loadMore" class="py-10 text-center">
            <div x-show="loading" class="animate-spin text-3xl text-blue-500 inline-block">↻</div>
            <p x-show="!hasMore && !loading" class="text-gray-500">すべての動画を読み込みました</p>
        </div>
    </div>

    <script>
        function infiniteScroll() {
            return {
                page: 1,
                loading: false,
                hasMore: {{ $videos->hasMorePages() ? 'true' : 'false' }},

                init() {
                    // Intersection Observerで画面下部を監視
                    const observer = new IntersectionObserver((entries) => {
                        if (entries[0].isIntersecting && !this.loading && this.hasMore) {
                            this.fetchNextPage();
                        }
                    }, {
                        threshold: 0.1
                    });

                    observer.observe(this.$refs.loadMore);
                },

                async fetchNextPage() {
                    this.loading = true;
                    this.page++;

                    // 現在のURL（検索パラメータ含む）にpage番号を付与
                    const url = new URL(window.location.href);
                    url.searchParams.set('page', this.page);

                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest' // これでLaravelの $request->ajax() が true になる
                            }
                        });

                        const html = await response.text();

                        if (html.trim() === '') {
                            this.hasMore = false;
                        } else {
                            // 取得したHTMLをリストの最後に追加
                            document.getElementById('video-list').insertAdjacentHTML('beforeend', html);

                            // 動画の初回フレーム表示スクリプトが必要ならここで再実行
                            // ※ loadeddataイベントは追加された要素でも自動で発火します
                        }
                    } catch (error) {
                        console.error('読み込みに失敗しました', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>

@endsection
