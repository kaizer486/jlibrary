@extends('layouts.super-admin')

@section('title', 'Manage Users')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="ti ti-users text-purple-600"></i>
                Manage Users
            </h1>
            <p class="text-gray-500 text-sm mt-1">Manage all users across the platform</p>
        </div>
        <a href="{{ route('super-admin.users.create') }}" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg transition flex items-center gap-2 font-semibold">
            <i class="ti ti-plus"></i> Add New User
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Users</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-red-500 shadow-sm">
        <p class="text-gray-500 text-sm">Super Admins</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($superAdminCount) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm">
        <p class="text-gray-500 text-sm">Admins</p>
        <p class="text-2xl font-bold text-purple-600">{{ number_format($adminCount) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-blue-500 shadow-sm">
        <p class="text-gray-500 text-sm">Institution Admins</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($institutionAdminCount) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-gray-500 shadow-sm">
        <p class="text-gray-500 text-sm">Regular Users</p>
        <p class="text-2xl font-bold text-gray-600">{{ number_format($userCount) }}</p>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('super-admin.users.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <i class="ti ti-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <select name="role" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                <option value="">All Roles</option>
                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>👑 Super Admin</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>🛡️ Admin</option>
                <option value="institution_admin" {{ request('role') == 'institution_admin' ? 'selected' : '' }}>🏢 Institution Admin</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>👤 User</option>
            </select>
        </div>
        <div>
            <select name="institution_id" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                <option value="">All Institutions</option>
                @foreach($institutions as $inst)
                    <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">🔍 Filter</button>
        </div>
        <div>
            <a href="{{ route('super-admin.users.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block">Clear</a>
        </div>
    </form>
</div>

<!-- Users Table -->
@if($users->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Institution</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ substr($user->full_name, 0, 1) }}</span>
                            </div>
                            <span class="font-medium text-gray-800">{{ $user->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @if($user->isSuperAdmin())
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">👑 Super Admin</span>
                        @elseif($user->isAdmin())
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">🛡️ Admin</span>
                        @elseif($user->isAnyInstitutionAdmin())
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">🏢 Institution Admin</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">👤 User</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->institution->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('super-admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('super-admin.users.edit', $user) }}" class="text-green-600 hover:text-green-800" title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            @if(auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('super-admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete {{ $user->full_name }} permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $users->appends(request()->query())->links() }}
</div>

@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-users text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Users Found</h3>
    <p class="text-gray-500">Click "Add New User" to get started.</p>
</div>
@endif
@endsection