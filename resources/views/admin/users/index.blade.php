@extends('layouts.app')
@section('title','Manage Users')
@section('content')
  <div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-semibold mb-4">User Management</h2>

    @if(session('status'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
    @endif

    <div class="bg-white shadow rounded">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">#</th>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Name</th>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Email</th>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Role</th>
            <th class="px-4 py-2 text-right text-sm font-medium text-gray-500">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @foreach($users as $user)
            <tr>
              <td class="px-4 py-2 text-sm text-gray-700">{{ $loop->iteration + ($users->currentPage()-1)*$users->perPage() }}</td>
              <td class="px-4 py-2 text-sm text-gray-700">{{ $user->name }}</td>
              <td class="px-4 py-2 text-sm text-gray-700">{{ $user->email }}</td>
              <td class="px-4 py-2 text-sm text-gray-700">{{ $user->role }}</td>
              <td class="px-4 py-2 text-sm text-right">
                <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                @if(auth()->id() !== $user->id)
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Delete user?')">
                  @csrf
                  @method('DELETE')
                  <button class="text-red-600 hover:text-red-900">Delete</button>
                </form>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $users->links() }}
    </div>
  </div>
@endsection
