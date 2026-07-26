@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<div class="mb-6">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold flex items-center gap-2">
                    <i class="ti ti-users"></i> Manage Users
                </h1>
                <p class="text-indigo-200 text-sm">Manage platform users and their roles</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="bg-white text-purple-600 px-4 py-2 rounded-lg hover:shadow-lg transition font-medium flex items-center gap-2 text-sm">
                <i class="ti ti-user-plus"></i> Add New User
            </a>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-purple-500">
        <p class="text-gray-500 text-xs font-medium">Total Users</p>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($totalUsers ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
        <p class="text-gray-500 text-xs font-medium">Super Admins</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($superAdminCount ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-purple-500">
        <p class="text-gray-500 text-xs font-medium">Admins</p>
        <p class="text-2xl font-bold text-purple-600">{{ number_format($adminCount ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
        <p class="text-gray-500 text-xs font-medium">Institution Admins</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($institutionAdminCount ?? 0) }}</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" placeholder="Search users..." value="{{ request('search') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
        </div>
        
        <div class="w-40">
            <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white">
                <option value="all">All Roles</option>
                @foreach(['user', 'admin', 'super_admin', 'institution_admin', 'librarian', 'instructor', 'author', 'researcher', 'bookseller'] as $role)
                    <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
            <i class="ti ti-search"></i> Filter
        </button>
        
        @if(request()->has('search') || request()->has('role'))
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">
                <i class="ti ti-x"></i> Clear
            </a>
        @endif
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Institution</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <p class="font-medium text-gray-800">{{ $user->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-3 py-1 rounded-full font-medium {{ $user->getRoleBadgeClass() }}">
                            {{ $user->getRoleLabel() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($user->institution)
                            {{ $user->institution->name }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                <i class="ti ti-edit"></i>
                            </a>
                            @if(auth()->user()->isSuperAdmin() && auth()->id() !== $user->id && !$user->hasRole('super_admin'))
                                <form method="POST" action="{{ route('admin.users.toggle-role', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800 text-sm" 
                                            onclick="return confirm('Toggle role for {{ $user->full_name }}?')">
                                        <i class="ti ti-shield"></i>
                                    </button>
                                </form>
                            @endif
                            @if(auth()->user()->canDeleteUser($user) && auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete {{ $user->full_name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                        <i class="ti ti-users text-3xl block mb-2"></i>
                        <p>No users found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
</div>
@endsection