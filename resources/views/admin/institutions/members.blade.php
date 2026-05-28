@extends('layouts.admin')

@section('title', 'Institution Members')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">👥 {{ $institution->name }} - Members</h1>
            <p class="text-gray-500 text-sm mt-1">Manage users belonging to this institution</p>
        </div>
        <a href="{{ route('admin.institutions.members.create', $institution) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
            <i class="ti ti-plus"></i> Add Member
        </a>
    </div>
</div>

@if($members->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($members as $member)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                            <span class="text-white text-xs font-bold">{{ substr($member->full_name, 0, 1) }}</span>
                        </div>
                        <span class="font-medium text-gray-900">{{ $member->full_name }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $member->email }}</td>
                <td class="px-6 py-4">
                    @if($member->isInstitutionAdmin())
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">👑 Institution Admin</span>
                    @elseif($member->isLibrarian())
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">📚 Librarian</span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">👤 Member</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $member->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4 text-sm">
                    <form method="POST" action="{{ route('admin.institutions.members.destroy', [$institution, $member]) }}" class="inline" onsubmit="return confirm('Remove this member?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">Remove</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $members->links() }}</div>
@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-users text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Members Found</h3>
    <p class="text-gray-500">Click "Add Member" to add users to this institution.</p>
</div>
@endif
@endsection