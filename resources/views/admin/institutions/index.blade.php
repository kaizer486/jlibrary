@extends('layouts.admin')

@section('title', 'Manage Institutions')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="ti ti-building text-indigo-600"></i>
                Manage Institutions
            </h1>
            <p class="text-gray-500 text-sm mt-1">Manage all institutions across the platform</p>
        </div>
        <a href="{{ route('admin.institutions.create') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg transition flex items-center gap-2 font-semibold">
            <i class="ti ti-plus"></i> Add Institution
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-indigo-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Institutions</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalInstitutions) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm">
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ number_format($pendingInstitutions) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-green-500 shadow-sm">
        <p class="text-gray-500 text-sm">Approved</p>
        <p class="text-2xl font-bold text-green-600">{{ number_format($approvedInstitutions) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-red-500 shadow-sm">
        <p class="text-gray-500 text-sm">Suspended</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($suspendedInstitutions) }}</p>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.institutions.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <i class="ti ti-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" placeholder="Search by name, email, or city..." value="{{ request('search') }}" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>⚠️ Suspended</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>❌ Inactive</option>
            </select>
        </div>
        <div>
            <select name="type" class="px-4 py-2 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Types</option>
                <option value="school" {{ request('type') == 'school' ? 'selected' : '' }}>🏫 School</option>
                <option value="college" {{ request('type') == 'college' ? 'selected' : '' }}>🎓 College</option>
                <option value="university" {{ request('type') == 'university' ? 'selected' : '' }}>🏛️ University</option>
                <option value="library" {{ request('type') == 'library' ? 'selected' : '' }}>📚 Library</option>
                <option value="bookstore" {{ request('type') == 'bookstore' ? 'selected' : '' }}>📖 Bookstore</option>
                <option value="publisher" {{ request('type') == 'publisher' ? 'selected' : '' }}>📰 Publisher</option>
                <option value="research_center" {{ request('type') == 'research_center' ? 'selected' : '' }}>🔬 Research Center</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">🔍 Filter</button>
        </div>
        <div>
            <a href="{{ route('admin.institutions.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block">Clear</a>
        </div>
    </form>
</div>

<!-- Institutions Table -->
@if($institutions->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Institution</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Users</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($institutions as $inst)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <i class="ti ti-building text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ Str::limit($inst->name, 30) }}</p>
                                <p class="text-xs text-gray-500">{{ $inst->city ?? 'No city' }}{{ $inst->city && $inst->region ? ', ' : '' }}{{ $inst->region ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">{{ $inst->type_label }}</td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-800">{{ $inst->email }}</p>
                        <p class="text-xs text-gray-500">{{ $inst->phone ?? 'No phone' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($inst->status === 'approved')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Approved</span>
                        @elseif($inst->status === 'pending')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                        @elseif($inst->status === 'suspended')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">⚠️ Suspended</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">❌ Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $inst->users_count ?? 0 }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                            {{ $inst->subscription_tier === 'premium' ? 'bg-purple-100 text-purple-700' : 
                               ($inst->subscription_tier === 'enterprise' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                            {{ ucfirst($inst->subscription_tier) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.institutions.show', $inst) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('admin.institutions.edit', $inst) }}" class="text-green-600 hover:text-green-800" title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            @if($inst->status === 'pending')
                                <form method="POST" action="{{ route('admin.institutions.approve', $inst) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800" title="Approve">
                                        <i class="ti ti-check"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.institutions.reject', $inst) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Reject">
                                        <i class="ti ti-x"></i>
                                    </button>
                                </form>
                            @endif
                            @if($inst->status === 'approved')
                                <form method="POST" action="{{ route('admin.institutions.suspend', $inst) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-orange-600 hover:text-orange-800" title="Suspend">
                                        <i class="ti ti-ban"></i>
                                    </button>
                                </form>
                            @endif
                            @if($inst->users_count == 0)
                                <form method="POST" action="{{ route('admin.institutions.destroy', $inst) }}" class="inline" onsubmit="return confirm('Delete {{ $inst->name }} permanently?')">
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
    {{ $institutions->appends(request()->query())->links() }}
</div>

@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-building text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Institutions Found</h3>
    <p class="text-gray-500">Click "Add Institution" to get started.</p>
</div>
@endif
@endsection