            @foreach ($images as $image)
                <li class="bg-white shadow-xl rounded-xl overflow-hidden p-5 flex flex-col items-center">

                    {{-- 画像タイトル --}}
                    <strong class="text-lg font-semibold mb-3">{{ $image->full_title }}</strong>

                    <div
                        class="relative aspect-video bg-gray-100 rounded-lg overflow-hidden mb-5 shadow-inner flex items-center justify-center">
                        <a href="{{ $image->external_url }}?external=true" target="_blank" class="block w-full h-full">
                            <img src="{{ $image->external_url }}" alt="{{ $image->full_title }}"
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
                    }" class="w-full mb-4 px-2">

                        <div class="relative flex gap-2">
                            <input type="text" x-model="name" @keydown.enter="updateImageName()" placeholder="表示名を入力"
                                class="flex-1 px-3 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                                :class="{ 'bg-gray-100': loading }">

                            <button @click="updateImageName()" :disabled="loading"
                                class="shrink-0 px-4 py-2 bg-gray-800 text-white text-xs font-bold rounded hover:bg-black transition flex items-center justify-center min-w-[64px] disabled:opacity-50">
                                <span x-show="!loading">更新</span>
                                <span x-show="loading" class="animate-spin text-lg">↻</span>
                            </button>

                            <p x-show="errorMessage" x-text="errorMessage" class="text-red-500 text-[10px] text-left mt-1">
                            </p>

                            <div x-show="showSuccess" x-transition
                                class="absolute -top-8 left-0 right-0 text-center text-green-600 text-xs font-bold bg-green-50 rounded py-1 border border-green-200">
                                ✅ 保存しました
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
