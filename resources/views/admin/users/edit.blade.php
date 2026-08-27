@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
  <div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Edit User</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Ubah informasi nama, email, dan peran (role) pengguna.</p>
      </div>
      <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 dark:hover:bg-slate-700 transition">
        <i class="fa-solid fa-arrow-left"></i> Kembali
      </a>
    </div>

    <div class="bg-white dark:bg-slate-900 shadow-sm rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
      <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
        @csrf
        @method('PATCH')

        {{-- Name --}}
        <div>
          <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Nama Lengkap <span class="text-red-500">*</span>
          </label>
          <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                 class="block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('name') border-red-500 @enderror">
          @error('name')
            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
        </div>

        {{-- Email --}}
        <div>
          <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Alamat Email <span class="text-red-500">*</span>
          </label>
          <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                 class="block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('email') border-red-500 @enderror">
          @error('email')
            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
        </div>

        {{-- Role --}}
        <div>
          <label for="role" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Peran (Role) <span class="text-red-500">*</span>
          </label>
          <select name="role" id="role" required
                  class="block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('role') border-red-500 @enderror">
            @foreach($roles as $r)
              <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>
                {{ ucfirst($r) }}
              </option>
            @endforeach
          </select>
          @error('role')
            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
        </div>

        {{-- Password (Optional) --}}
        <div>
          <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Password Baru <span class="text-xs text-slate-400 font-normal">(Kosongkan jika tidak ingin mengubah password)</span>
          </label>
          <input type="password" name="password" id="password" placeholder="••••••••"
                 class="block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('password') border-red-500 @enderror">
          @error('password')
            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
        </div>

        {{-- Action buttons --}}
        <div class="pt-4 flex items-center gap-3">
          <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl shadow-sm transition">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
          </button>
          <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium text-sm rounded-xl transition">
            Batal
          </a>
        </div>
      </form>
    </div>
  </div>
@endsection
