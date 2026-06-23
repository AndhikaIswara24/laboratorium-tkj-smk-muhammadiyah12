<header class="bg-white border-b shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <div class="flex items-center gap-3">
        <button id="sidebarToggle" class="md:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none" aria-label="Toggle sidebar">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-gray-800">Inventaris Lab TKJ</a>
      </div>

      <div class="flex items-center gap-4">
        @auth
          <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded">Logout</button>
          </form>
        @else
          <a class="text-sm text-gray-700" href="{{ route('login') }}">Login</a>
        @endauth
      </div>
    </div>
  </div>
</header>
