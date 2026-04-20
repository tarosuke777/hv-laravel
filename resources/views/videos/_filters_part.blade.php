    <div x-data="{ open: false }" class="mb-8 border p-4 bg-gray-50 rounded-lg shadow-sm">

        {{-- ヘッダー：ここをクリックしない限りタイトル一覧は見えない --}}
        <div class="flex items-center justify-between cursor-pointer group" @click="open = !open">
            <div class="flex flex-col">
                <div class="mt-1 flex items-center">
                    <span class="text-base font-bold text-black">
                        @if ($selectedTitle)
                            {{ $selectedTitle }}
                        @else
                            タイトルで絞り込む
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- タイトル一覧：初期表示は x-show='false' (openの初期値) なので閉じている --}}
        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-300">
            <div class="flex flex-wrap gap-2 max-h-60 overflow-y-auto p-1">
                <a href="{{ route($indexRoute) }}"
                    class="px-3 py-1 text-sm rounded-full transition {{ !$selectedTitle ? 'bg-blue-600 text-white font-bold' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    全て ({{ count($uniqueTitles) }})
                </a>

                @foreach ($uniqueTitles as $title)
                    <a href="{{ route($indexRoute, ['title' => $title]) }}"
                        class="px-3 py-1 text-sm rounded-full transition {{ $selectedTitle === $title ? 'bg-blue-600 text-white font-bold' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        {{ $title }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- 名前での絞り込み：タイトルが選択されていれば、タイトル一覧の開閉に関わらず「常に表示」 --}}
        @if ($selectedTitle && count($uniqueNames) > 0)
            <div class="mt-6 pt-4 border-t border-dashed border-gray-300">
                <p class="text-xs font-bold text-gray-500 mb-3 uppercase tracking-wider">
                    表示名でさらに絞り込む
                </p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route($indexRoute, ['title' => $selectedTitle]) }}"
                        class="px-4 py-1.5 text-sm rounded-full transition {{ !$selectedName ? 'bg-indigo-600 text-white font-bold shadow-md' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                        全て
                    </a>

                    @foreach ($uniqueNames as $name)
                        <a href="{{ route($indexRoute, ['title' => $selectedTitle, 'name' => $name]) }}"
                            class="px-4 py-1.5 text-sm rounded-full transition {{ $selectedName === $name ? 'bg-indigo-600 text-white font-bold shadow-md' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                            {{ $name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
