@extends('layouts.admin')

@section('content')

<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">👥 User Management</h1>
            <p class="text-gray-500 text-sm mt-1">Manage users, assign roles, and monitor activity</p>
        </div>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.users.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
            <i class="ti ti-plus"></i> Add New User
        </a>
        @endif
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Users</p>
        <p class="text-2xl font-bold text-gray-900">{{ $totalUsers ?? $users->total() }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-red-500 shadow-sm">
        <p class="text-gray-500 text-sm">Super Admins</p>
        <p class="text-2xl font-bold text-red-600">{{ $superAdminCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm">
        <p class="text-gray-500 text-sm">Admins</p>
        <p class="text-2xl font-bold text-purple-600">{{ $adminCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-blue-500 shadow-sm">
        <p class="text-gray-500 text-sm">Regular Users</p>
        <p class="text-2xl font-bold text-blue-600">{{ $userCount ?? 0 }}</p>
    </div>
</div>

<!-- Search and Filter Form -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <i class="ti ti-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                <option value="">All Roles</option>
                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>👑 Super Admin</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>🛡️ Admin</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>👤 User</option>
            </select>
        </div>
        <div>
            <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>📅 Latest First</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>📅 Oldest First</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>🔤 Name A-Z</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>🔤 Name Z-A</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition w-full">
                🔍 Filter
            </button>
        </div>
        <div>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block text-center w-full">
                Clear
            </a>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Books</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Certificates</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wallet</th>
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
                            <span class="text-sm font-medium text-gray-900">{{ $user->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @if($user->isSuperAdmin())
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                👑 Super Admin
                            </span>
                        @elseif($user->isAdmin())
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                🛡️ Admin
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                👤 User
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->books_count ?? $user->books()->count() }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->certificates_count ?? $user->certificates()->count() }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${{ number_format($user->wallet_balance ?? 0, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <!-- View Button -->
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            
                            <!-- Edit Button - Only if user can be edited -->
                            @if(auth()->user()->canManageUser($user))
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-green-600 hover:text-green-800" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                            @endif
                            
                            <!-- Toggle Role Button - Only Super Admin can change roles -->
                            @if(auth()->user()->isSuperAdmin() && !$user->isSuperAdmin() && auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('admin.users.toggle-role', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-purple-600 hover:text-purple-800" title="Toggle Role">
                                        <i class="ti ti-shield"></i>
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Delete Button - Only if user can be deleted -->
                            @if(auth()->user()->canDeleteUser($user) && auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete {{ $user->full_name }} permanently? All their data will be lost.')">
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
    <p class="text-gray-500">Try changing your search filters.</p>
</div>
@endif
@endsection