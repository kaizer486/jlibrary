@extends('layouts.librarian')

@section('title', $member->full_name)
@section('page-title', '👤 ' . $member->full_name)

@section('content')

<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('librarian.members.index') }}" class="text-purple-400 hover:text-purple-300 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Members
        </a>
    </div>

    <!-- Member Profile - Dark Glassmorphism -->
    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 px-6 py-4 border-b border-slate-700">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($member->full_name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $member->full_name }}</h1>
                    <p class="text-slate-400 text-sm">{{ $member->email }}</p>
                    <span class="inline-block mt-1 text-xs px-2.5 py-1 rounded-full font-medium
                        @if($member->hasRole('librarian')) bg-purple-500/20 text-purple-300 border border-purple-500/20
                        @elseif($member->hasRole('institution_admin')) bg-blue-500/20 text-blue-300 border border-blue-500/20
                        @else bg-slate-700 text-slate-300 border border-slate-600 @endif">
                        {{ $member->getRoleLabel() ?? '👤 Member' }}
                    </span>
                </div>
                <div class="ml-auto">
                    @if($member->email_verified_at)
                        <span class="badge-approved">✅ Active</span>
                    @else
                        <span class="badge-pending">⏳ Pending</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Info -->
                <div>
                    <h3 class="font-semibold text-white text-lg mb-4 flex items-center gap-2">
                        <i class="ti ti-user text-purple-400"></i> Personal Information
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Full Name</span>
                            <span class="font-medium text-white">{{ $member->full_name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Email</span>
                            <span class="font-medium text-white">{{ $member->email }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Role</span>
                            <span class="font-medium text-white">{{ $member->getRoleLabel() ?? 'Member' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Status</span>
                            <span class="font-medium text-white">
                                @if($member->email_verified_at)
                                    ✅ Verified
                                @else
                                    ⏳ Pending Verification
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-700">
                            <span class="text-slate-400">Joined</span>
                            <span class="font-medium text-white">{{ $member->created_at->format('F d, Y') }}</span>
                        </div>
                        @if($member->location)
                            <div class="flex justify-between py-2 border-b border-slate-700">
                                <span class="text-slate-400">Location</span>
                                <span class="font-medium text-white">{{ $member->location }}</span>
                            </div>
                        @endif
                        @if($member->occupation)
                            <div class="flex justify-between py-2">
                                <span class="text-slate-400">Occupation</span>
                                <span class="font-medium text-white">{{ $member->occupation }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Stats & Activity -->
                <div>
                    <h3 class="font-semibold text-white text-lg mb-4 flex items-center gap-2">
                        <i class="ti ti-chart-bar text-purple-400"></i> Activity
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-purple-300">{{ $member->books()->count() ?? 0 }}</p>
                            <p class="text-xs text-slate-400">📚 Books Read</p>
                        </div>
                        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-purple-300">{{ $member->bookmarks()->count() ?? 0 }}</p>
                            <p class="text-xs text-slate-400">🔖 Bookmarks</p>
                        </div>
                        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-purple-300">{{ $member->certificates()->count() ?? 0 }}</p>
                            <p class="text-xs text-slate-400">🎓 Certificates</p>
                        </div>
                        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-purple-300">TSh {{ number_format($member->wallet_balance ?? 0, 2) }}</p>
                            <p class="text-xs text-slate-400">💰 Wallet Balance</p>
                        </div>
                    </div>

                    <!-- Role Management -->
                    @if(auth()->user()->canManageUser($member) && auth()->user()->id !== $member->id)
                        <div class="mt-4 p-4 bg-slate-800 border border-slate-700 rounded-xl">
                            <h4 class="font-semibold text-white text-sm mb-3">Manage Role</h4>
                            <form method="POST" action="{{ route('librarian.members.update-role', $member) }}" class="flex gap-3">
                                @csrf
                                <select name="role" class="flex-1 search-bar">
                                    <option value="user" {{ $member->hasRole('user') ? 'selected' : '' }}>👤 Member</option>
                                    <option value="librarian" {{ $member->hasRole('librarian') ? 'selected' : '' }}>📚 Librarian</option>
                                    <option value="institution_admin" {{ $member->hasRole('institution_admin') ? 'selected' : '' }}>🏢 Admin</option>
                                </select>
                                <button type="submit" class="btn-library">
                                    <i class="ti ti-device-floppy"></i> Update
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Books -->
            @if($member->books()->count() > 0)
                <div class="mt-6 pt-6 border-t border-slate-700">
                    <h3 class="font-semibold text-white text-lg mb-4 flex items-center gap-2">
                        <i class="ti ti-books text-purple-400"></i> Recently Read Books
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($member->books()->latest()->limit(4)->get() as $book)
                            <a href="{{ route('librarian.books.show', $book) }}" class="bg-slate-800 border border-slate-700 rounded-xl p-3 text-center hover:bg-slate-700 transition">
                                <div class="w-full h-20 bg-slate-700 rounded-lg flex items-center justify-center mb-2">
                                    @if($book->cover_image)
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-full w-full object-cover rounded-lg">
                                    @else
                                        <i class="ti ti-book text-2xl text-purple-400"></i>
                                    @endif
                                </div>
                                <p class="text-xs font-medium text-white truncate">{{ $book->title }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-6 pt-6 border-t border-slate-700 flex gap-3">
                <a href="mailto:{{ $member->email }}" class="btn-library">
                    <i class="ti ti-mail"></i> Send Email
                </a>
                @if(auth()->user()->canDeleteUser($member) && auth()->user()->id !== $member->id)
                    <form method="POST" action="{{ route('librarian.members.destroy', $member) }}" 
                          onsubmit="return confirm('Remove this member from the library?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg transition">
                            <i class="ti ti-user-minus"></i> Remove Member
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection