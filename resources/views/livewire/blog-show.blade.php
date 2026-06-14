<div class="bg-slate-50 min-h-screen py-12 font-sans text-slate-800 selection:bg-emerald-100 selection:text-emerald-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        
        <div class="mb-10">
            <a href="{{ url('/') }}" class="inline-flex items-center text-sm font-semibold text-emerald-600 hover:text-emerald-700 gap-2 transition-all hover:-translate-x-1">
                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-emerald-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </span>
                Kembali ke Beranda
            </a>
        </div>

        <article class="bg-white rounded-3xl shadow-xl shadow-slate-300/60 border border-slate-100 overflow-hidden">
            
            <header class="pt-12 pb-8 px-6 sm:px-12 text-center">
                <span class="inline-block px-4 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-[0.2em] rounded-full">
                    {{ $post->postCategory?->name ?? 'Informasi' }}
                </span>
                
                <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 mt-6 leading-[1.1] tracking-tight">
                    {{ $post->title }}
                </h1>
                
                <div class="mt-8 flex items-center justify-center gap-4 text-sm text-slate-500 font-medium">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $post->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </header>

            @if($post->image)
                <div class="mx-6 sm:mx-12 my-6 rounded-2xl overflow-hidden shadow-2xl shadow-emerald-900/10">
                    <img src="{{ asset('storage/' . $post->image) }}" class="w-full aspect-[21/9] object-cover hover:scale-105 transition-transform duration-700" alt="{{ $post->title }}">
                </div>
            @endif

            <div class="px-6 sm:px-20 pb-16 pt-8">
                <div class="prose prose-lg prose-slate prose-emerald max-w-none 
                            prose-headings:font-bold prose-headings:text-slate-900
                            prose-p:leading-relaxed prose-img:rounded-xl">
                    {!! $post->content !!}
                </div>
            </div>

            <div class="bg-green-50 px-6 sm:px-20 py-8 border-t border-slate-100 text-center">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest">
                    Terima kasih telah membaca informasi dari Siberang STT-NF
                </p>
            </div>
        </article>

        <footer class="mt-12 text-center text-sm text-slate-400">
            &copy; {{ date('Y') }} Siberang STT-NF
        </footer>
    </div>
</div>