<aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-gray-800 text-gray-100 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out z-40">
  <div class="h-full overflow-y-auto">
    <div class="px-4 py-6">
      <nav class="space-y-1">
        @if(Route::has('dashboard'))
          <a href="{{ route('dashboard') }}" class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">Dashboard</a>
        @endif
        @if(Route::has('assets.index'))
          <a href="{{ route('assets.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('assets.*') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">Data Aset</a>
        @endif
        @if(Route::has('kondisi.index'))
          <a href="{{ route('kondisi.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('kondisi.*') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">Kondisi Fisik</a>
        @endif
        @if(Route::has('pemeliharaan.index'))
          <a href="{{ route('pemeliharaan.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('pemeliharaan.*') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">Pemeliharaan</a>
        @endif
        @if(Route::has('efisiensi.index'))
          <a href="{{ route('efisiensi.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('efisiensi.*') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">Efisiensi</a>
        @endif
        @if(Route::has('variabel.index'))
          <a href="{{ route('variabel.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('variabel.*') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">Variabel Eksternal</a>
        @endif
        @if(Route::has('prediksi.index'))
          <a href="{{ route('prediksi.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('prediksi.*') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">Prediksi Naive Bayes</a>
        @endif
        @if(Route::has('laporan.index'))
          <a href="{{ route('laporan.index') }}" class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('laporan.*') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">Laporan</a>
        @endif
          @if(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.users.index') }}" class="block rounded-md px-3 py-2 mt-3 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">Users</a>
          @endif
      </nav>
    </div>
  </div>
</aside>

<!-- overlay for mobile when sidebar open -->
<div id="sidebarBackdrop" class="fixed inset-0 bg-black opacity-25 hidden z-30 md:hidden"></div>
