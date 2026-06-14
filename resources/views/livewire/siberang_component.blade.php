<div>
    <nav class="bg-[#F8F9FA]/70 backdrop-blur-xl sticky top-0 z-50 border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3 cursor-pointer" wire:click="setCategory(null)">
                    <span class="text-lg font-bold text-[#1A3A32]">Siberang</span>
                </div>
                <a href="{{ url('/admin/login') }}" class="px-5 py-2.5 text-xs font-bold text-white bg-[#1A3A32] rounded-full">Login</a>
            </div>
        </div>
    </nav>

    <header class="max-w-5xl mx-auto px-6 text-center pt-20 pb-16">
        <h1 class="text-4xl font-extrabold text-[#1A3A32]">Sistem Proteksi Logistik</h1>
        
        <div class="mt-10 max-w-xl mx-auto relative">
            <input 
                wire:model.live.debounce.500ms="search" 
                type="text" 
                placeholder="Cari laporan atau barang temuan..." 
                class="w-full pl-12 pr-4 py-4 border border-gray-200 rounded-2xl"
            >
        </div>
    </header>

    <section id="pusat-informasi" class="bg-[#1A3A32] text-white py-24 rounded-t-[3rem]">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold mb-10">Hasil Informasi</h2>

            @if($posts->isEmpty())
                <p class="text-center text-gray-400">Tidak ada data ditemukan untuk "{{ $search }}"</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($posts as $post)
                        <article wire:key="post-{{ $post->id }}" class="bg-[#21443B] p-6 rounded-3xl">
                            <h3 class="text-lg font-bold">{{ $post->title }}</h3>
                            <p class="text-sm text-gray-300 mt-2">{{ Str::limit(strip_tags($post->content), 100) }}</p>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>