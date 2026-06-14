<div
    class="bg-[#F8F9FA] min-h-screen font-sans text-slate-900 antialiased selection:bg-emerald-100 selection:text-emerald-900">

    <!-- Navbar -->
    <nav
        class="bg-[#F8F9FA]/70 backdrop-blur-xl sticky top-0 z-50 border-b border-gray-200/50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">

                <!-- Logo -->
                <div class="flex items-center gap-3 cursor-pointer group" wire:click="setCategory(null)">
                    <div
                        class="p-1.5 bg-white rounded-xl shadow-sm border border-gray-100 group-hover:scale-105 transition-transform duration-300">
                        <img src="{{ asset('images/logo-STTNF.png') }}" alt="Logo Siberang"
                            class="h-10 w-auto object-contain">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-bold text-[#1A3A32] tracking-tight leading-tight">Siberang</span>
                        <span class="text-[10px] text-slate-500 tracking-wider uppercase font-semibold">STT-NF
                            Security</span>
                    </div>
                </div>

                <!-- Right Side: Menu + Login -->
                <div class="flex items-center gap-6">
                    <!-- Navigasi Kategori (Dipindahkan ke samping Login) -->
                    <div
                        class="hidden md:flex items-center space-x-1.5 bg-gray-200/50 p-1 rounded-full border border-gray-200/30">
                        <button wire:click="setCategory(null)"
                            class="text-xs font-semibold px-4 py-2 rounded-full transition-all duration-300 {{ is_null($activeCategory) ? 'bg-[#1A3A32] text-white shadow-sm' : 'text-slate-600 hover:text-[#1A3A32] hover:bg-white/50' }}">
                            Semua Informasi
                        </button>
                        @foreach($categories as $cat)
                            <button wire:click="setCategory({{ $cat->id }})"
                                class="text-xs font-semibold px-4 py-2 rounded-full transition-all duration-300 {{ $activeCategory == $cat->id ? 'bg-[#1A3A32] text-white shadow-sm' : 'text-slate-600 hover:text-[#1A3A32] hover:bg-white/50' }}">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Login Button -->
                    <a href="{{ url('/admin/login') }}"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-bold text-white bg-[#1A3A32] hover:bg-[#112621] rounded-full shadow-md shadow-emerald-950/10 hover:shadow-lg transition-all duration-300">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header Hero -->
    <header class="max-w-5xl mx-auto px-6 text-center pt-20 pb-16 relative overflow-hidden">
        <!-- Shadow Hijau di belakang Badge -->
        <div class="relative inline-block mb-6">
            <div class="absolute inset-0 bg-emerald-500 blur-2xl opacity-80 scale-150"></div>
            <span
                class="relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-white text-emerald-700 text-xs font-bold tracking-wide uppercase rounded-full border border-emerald-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Sistem Proteksi Logistik Kampus
            </span>
        </div>

        <h1 class="text-4xl sm:text-6xl font-extrabold text-[#1A3A32] tracking-tight max-w-4xl mx-auto leading-[1.12]">
            The Future of Security with <br>
            <span
                class="bg-gradient-to-r from-emerald-600 to-[#1A3A32] bg-clip-text text-transparent font-serif italic font-normal">Latest
                Campus System</span>
        </h1>

        <p class="mt-6 text-base text-slate-500 max-w-2xl mx-auto leading-relaxed">
            Platform terpadu pelaporan kehilangan, penemuan barang, dan panduan keamanan untuk mewujudkan lingkungan
            akademik STT-NF yang transparan, jujur, dan kondusif.
        </p>

        <!-- Search Input -->
        <div class="mt-10 max-w-xl mx-auto relative">
            <input wire:model.live.debounce.500ms="search" type="text" placeholder="Cari laporan atau barang temuan..."
                class="w-full pl-12 pr-4 py-4 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#1A3A32]">
            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
        </div>
    </header>

    <section class="max-w-7xl mx-auto px-6 lg:px-8 py-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 auto-rows-[220px]">

            <div
                class="md:col-span-4 rounded-3xl bg-slate-200 overflow-hidden relative shadow-sm group border border-gray-100">
                <div
                    class="absolute inset-0 bg-gradient-to-t from-[#1A3A32]/60 to-transparent z-10 opacity-60 group-hover:opacity-40 transition-opacity duration-300">
                </div>
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS5hd0eNcbCjThfiUEYfIM7C8F1qPgraGkK9Yo0tP_qW8Y1sAIHi_ilqAM&s=10"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                    alt="Campus Security">
                <div class="absolute bottom-5 left-5 z-20 text-white">
                    <span
                        class="text-[10px] font-bold uppercase tracking-wider bg-white/20 backdrop-blur-md px-2 py-1 rounded-md">Sarpras
                        Terpadu</span>
                </div>
            </div>

            <div
                class="md:col-span-4 rounded-3xl bg-gradient-to-br from-[#1A3A32] to-[#112621] p-7 text-white flex flex-col justify-between shadow-md relative overflow-hidden group border border-emerald-950/20 transform hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -right-6 -bottom-6 text-white/[0.03] transform -rotate-12 pointer-events-none">
                    <svg class="w-44 h-44" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
                <div class="flex justify-between items-start">
                    <div class="text-4xl font-extrabold tracking-tight">100+</div>
                    <div class="p-2 bg-white/10 rounded-xl backdrop-blur-md">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-white">Barang Logistik Kembali</h4>
                    <p class="text-xs text-emerald-200/70 mt-1 leading-relaxed">Berbagai aset pribadi civitas akademika
                        yang sukses dilacak dan dikembalikan ke pemiliknya.</p>
                </div>
            </div>

            <div
                class="md:col-span-4 rounded-3xl bg-white border border-gray-200/70 p-7 flex flex-col justify-between shadow-sm transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div
                        class="w-11 h-11 rounded-xl bg-slate-50 flex items-center justify-center text-[#1A3A32] border border-gray-100 shadow-inner">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span
                        class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                        Sistem On
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-800 tracking-tight">24 / 7 Monitoring</div>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">Pengawasan kotak aduan kehilangan dan
                        inventarisasi temuan divalidasi berkala oleh tim Siberang NF.</p>
                </div>
            </div>

        </div>
    </section>

    <section class="bg-[#1A3A32] text-white mt-20 py-24 rounded-t-[3rem] shadow-2xl shadow-inner relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            <div class="flex flex-col md:flex-row md:items-end justify-between mb-14 gap-4">
                <div>
                    <p class="text-emerald-400 font-bold tracking-widest text-[10px] uppercase">Pusat Informasi &
                        Transparansi</p>
                    <h2 class="text-3xl font-bold mt-2 tracking-tight">Edukasi Keamanan & Berita Logistik</h2>
                </div>

                @if($activeCategory)
                    <div
                        class="flex items-center gap-2 bg-white/5 border border-white/10 px-3 py-1.5 rounded-xl backdrop-blur-md">
                        <span class="text-xs text-gray-400">Penyaringan:</span>
                        <span class="text-xs font-bold text-emerald-400">
                            {{ $categories->firstWhere('id', $activeCategory)?->name ?? 'Kategori' }}
                        </span>
                        <button wire:click="setCategory(null)"
                            class="text-xs text-rose-400 hover:text-rose-300 ml-1.5 transition-colors font-bold">×</button>
                    </div>
                @endif
            </div>

            @if($posts->isEmpty())
                <div class="text-center py-20 bg-white/[0.02] rounded-3xl border border-white/5 backdrop-blur-sm">
                    <svg class="w-12 h-12 text-white/10 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4M12 4v16"></path>
                    </svg>
                    <p class="text-gray-400 text-sm font-medium">Belum ada postingan atau validasi laporan di kategori ini.
                    </p>
                </div>
            @else
                <div class="flex gap-6 overflow-x-auto pb-8 snap-x"
                    style="scrollbar-width: none; -ms-overflow-style: none; -webkit-overflow-scrolling: touch;">
                    @foreach($posts as $post)
                        <article
                            class="flex-none w-[320px] md:w-[350px] snap-start bg-[#21443B] rounded-3xl overflow-hidden hover:shadow-2xl hover:shadow-emerald-950/30 transition-all duration-300 flex flex-col border border-white/[0.06] group transform hover:-translate-y-1">

                            <div class="relative aspect-[16/10] w-full overflow-hidden bg-[#162F29]">
                                @if($post->image)
                                    <img src="{{ asset('storage/' . $post->image) }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white/10">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="absolute top-4 left-4">
                                    <span
                                        class="px-2.5 py-1 bg-white/10 backdrop-blur-md text-white text-[9px] font-extrabold uppercase tracking-widest rounded-lg border border-white/10">
                                        {{ $post->postCategory?->name ?? 'Umum' }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-6 flex flex-col flex-1">
                                <h3
                                    class="text-base font-bold text-white leading-snug group-hover:text-emerald-300 transition-colors line-clamp-2">
                                    <a href="/blog/{{ $post->slug }}">{{ $post->title }}</a>
                                </h3>

                                <p class="text-gray-300/80 text-xs mt-3 line-clamp-3 flex-1 leading-relaxed">
                                    {!! Str::limit(strip_tags($post->content), 100) !!}
                                </p>

                                <div
                                    class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between text-[11px] text-gray-400">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>{{ $post->created_at->format('d M Y') }}</span>
                                    </div>
                                    <a href="/blog/{{ $post->slug }}"
                                        class="text-emerald-400 font-bold hover:text-emerald-300 flex items-center gap-1 group/btn">
                                        Selengkapnya
                                        <svg class="w-3 h-3 transform group-hover/btn:translate-x-0.5 transition-transform"
                                            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="bg-white py-20 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                <div class="lg:col-span-4 space-y-4">
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider rounded-md">
                        Pusat Bantuan Fisik
                    </span>
                    <h3 class="text-2xl font-extrabold text-[#1A3A32] tracking-tight">Pos Keamanan Kampus A dan Kampus B
                    </h3>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Jika Anda memerlukan validasi langsung, klaim kepemilikan barang logistik yang ditemukan,
                        silakan kunjungi kantor sekretariat / Security STT Terpadu Nurul Fikri.
                    </p>
                    <div class="pt-2 text-xs text-slate-400 space-y-1.5">
                        <p class="flex items-center gap-2"><strong class="text-slate-700">Alamat:</strong>
                        <ul class="list-disc list-inside">
                            <li>Jl. Situ Indah No. 116, Kelurahan Tugu, Kecamatan Cimanggis, Kota Depok, Jawa Barat</li>
                            <li>Jl. Raya Lenteng Agung No.20-21, RT.4/RW.1, Srengseng Sawah, Kecamatan Jagakarsa,
                                Jakarta Selatan</li>
                        </ul>
                        </p>
                        <p class="flex items-center gap-2"><strong class="text-slate-700">Waktu Operasional:</strong>
                            Senin - Sabtu (08:00 - 17:00 WIB)</p>
                    </div>
                </div>

                <div
                    class="lg:col-span-8 rounded-3xl overflow-hidden shadow-xl shadow-slate-200/60 border border-gray-200/80 h-[360px] relative group">
                    <div wire:ignore>
                        <iframe
                            src="https://maps.google.com/maps?q=STT%20Nurul%20Fikri&t=&z=13&ie=UTF8&iwloc=&output=embed"
                            width="100%" height="500" style="border:0;" loading="lazy">
                            Downs
                        </iframe>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <footer class="bg-[#11231E] text-slate-400 py-16 border-t border-emerald-950">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 pb-8 border-b border-white/[0.04]">
                <div class="flex items-center gap-2">
                    <div
                        class="w-6 h-6 rounded-lg bg-white/10 flex items-center justify-center font-bold text-white text-xs">
                        S</div>
                    <span class="text-white font-extrabold tracking-tight text-sm">Siberang <span
                            class="text-emerald-400 font-light">System</span></span>
                </div>
                <div class="flex gap-8 text-xs font-semibold">
                    <a href="#" class="hover:text-white transition-colors duration-200">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-white transition-colors duration-200">Panduan Pengamanan</a>
                    <a href="https://nurulfikri.ac.id" target="_blank"
                        class="hover:text-emerald-400 text-emerald-500 transition-colors duration-200 flex items-center gap-1">
                        Portal Resmi STT-NF
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-[11px] text-slate-500">
                <p>© 2026 Project pembuatan sistem informasi kelompok 8</p>
                <p class="font-mono tracking-wider text-slate-600">v2.1.0-STABLE</p>
            </div>

            <a href="https://wa.me/6285715118015" target="_blank"
                class="fixed bottom-6 right-6 z-[9999] flex items-center justify-center w-14 h-14 bg-[#25D366] text-white rounded-full shadow-lg hover:scale-110 transition-transform duration-300 group animate-bounce">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.955c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
            </a>
        </div>
    </footer>

</div>