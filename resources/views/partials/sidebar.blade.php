<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
  <div class="position-sticky pt-3">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="page" href="{{ route('dashboard') }}">
          Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}" href="{{ route('assets.index') }}">
          Data Aset
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kondisi.*') ? 'active' : '' }}" href="{{ route('kondisi.index') }}">
          Kondisi Fisik
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('pemeliharaan.*') ? 'active' : '' }}" href="{{ route('pemeliharaan.index') }}">
          Pemeliharaan
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('efisiensi.*') ? 'active' : '' }}" href="{{ route('efisiensi.index') }}">
          Efisiensi
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('variabel.*') ? 'active' : '' }}" href="{{ route('variabel.index') }}">
          Variabel Eksternal
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('prediksi.*') ? 'active' : '' }}" href="{{ route('prediksi.index') }}">
          Prediksi Naive Bayes
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
          Laporan
        </a>
      </li>
    </ul>
  </div>
</nav>
