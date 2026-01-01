{{-- 
    SECURITY: XSS Protection
    ========================
    Semua output dalam view ini menggunakan {{ }} syntax yang otomatis meng-escape HTML entities.
    Ini mencegah XSS (Cross-Site Scripting) attacks dimana attacker mencoba inject JavaScript/HTML.
    
    Contoh: Jika nama produk = "<script>alert('XSS')</script>"
    Output akan di-escape menjadi: "&lt;script&gt;alert('XSS')&lt;/script&gt;"
    Sehingga ditampilkan sebagai text biasa, BUKAN dieksekusi sebagai JavaScript.
--}}
<div>
{{-- 
    SECURITY: XSS Protection
    ========================
    Semua output dalam view ini menggunakan {{ }} syntax yang otomatis meng-escape HTML entities.
    Ini mencegah XSS. JANGAN gunakan {!! !!} kecuali data sudah di-sanitize!
--}}
<div class="px-4 sm:px-0">
    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-600">Daftar Produk</h2>
            <p class="text-slate-500 mt-1">Kelola inventory produk Anda dengan mudah.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <!-- Search Bar -->
            <div class="relative w-full md:w-64">
                <input type="text" 
                       wire:model.live="search"
                       placeholder="Cari produk..."
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <a href="{{ route('products.create') }}" 
               wire:navigate
               class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-200 transition-all duration-200 hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Produk
            </a>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($products as $product)
            <div class="group bg-white rounded-2xl p-5 shadow-[0_2px_8px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_24px_rgba(0,0,0,0.08)] border border-slate-100 transition-all duration-300 hover:-translate-y-1">
                <!-- Card Header -->
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->qty > 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $product->qty > 10 ? 'In Stock' : 'Low Stock' }}
                    </span>
                </div>

                <!-- Product Info -->
                <div class="space-y-1 mb-4">
                    <h3 class="text-lg font-bold text-slate-800 line-clamp-1" title="{{ $product->name }}">
                        {{-- XSS PROTECTION: Auto-escaped --}}
                        {{ $product->name }}
                    </h3>
                    <p class="text-slate-500 text-xs">ID: #{{ $product->id }}</p>
                </div>

                <!-- Price & Qty -->
                <div class="flex items-center justify-between py-3 border-t border-slate-50 mb-4">
                    <div>
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Harga</p>
                        <p class="text-indigo-600 font-bold">Rp {{ number_format($product->amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Stok</p>
                        <p class="text-slate-700 font-semibold">{{ $product->qty }} <span class="text-xs font-normal text-slate-400">unit</span></p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <a href="{{ route('products.edit', $product->id) }}" 
                       wire:navigate
                       class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-sm font-medium rounded-lg transition-colors duration-200">
                        Edit
                    </a>
                    <button wire:click="confirmDelete({{ $product->id }})" 
                            class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg transition-colors duration-200">
                        Hapus
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-dashed border-slate-300">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900">Tidak ada produk ditemukan</h3>
                <p class="mt-1 text-slate-500">Coba kata kunci lain atau tambahkan produk baru.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-data="{ show: @entangle('confirmingProductDeletion') }"
         x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/75 transition-opacity" aria-hidden="true" @click="show = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-semibold text-slate-900" id="modal-title">
                                Hapus Produk?
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500">
                                    Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan dan data akan hilang permanen.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" 
                            wire:click="delete"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors cursor-pointer">
                        Ya, Hapus
                    </button>
                    <button type="button" 
                            wire:click="cancelDelete"
                            @click="show = false"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors cursor-pointer">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
