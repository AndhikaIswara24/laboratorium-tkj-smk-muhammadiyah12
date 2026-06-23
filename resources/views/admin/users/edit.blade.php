@extends('layouts.app')
@section('title','Edit User')
@section('content')
  <div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-semibold mb-4">Edit User</h2>

    <div class="bg-white shadow rounded p-4">
      <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <div class="mt-1">{{ $user->name }}</div>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <div class="mt-1">{{ $user->email }}</div>
        </div>

        <div class="mb-4">
          <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
          <div class="mt-1">
            <select name="role" id="role" class="block w-full rounded border-gray-300">
              @foreach($roles as $r)
                <option value="{{ $r }}" {{ $user->role === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
          <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection
