@extends('layouts.app')

@section('title', 'Search Results: ' . $query)
@section('page-title', 'Search Results')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Search Results for "{{ $query }}"
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Found {{ $totalResults }} result(s)
        </p>
    </div>

    @if($totalResults > 0)
        <div class="grid gap-4">
            @foreach($groupedResults as $type => $items)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-3 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">
                            @php
                                $typeLabels = [
                                    'book' => '📚 Books',
                                    'chat' => '💬 AI Chats',
                                    'certificate' => '🎓 Certificates',
                                    'quiz' => '📝 Quizzes',
                                    'group' => '👥 Community Groups',
                                    'marketplace' => '🛒 Marketplace',
                                    'document' => '📄 Documents',
                                    'transaction' => '💰 Transactions',
                                    'referral' => '🎁 Referrals',
                                    'conversion' => '🔄 File Conversions',
                                    'institution' => '🏛️ Institutions',
                                    'shelf' => '📚 Shelves',
                                    'category' => '🏷️ Categories',
                                    'borrowing' => '📖 Borrowings',
                                    'purchase' => '🛍️ Purchases',
                                    'notification' => '🔔 Notifications',
                                    'withdrawal' => '💳 Withdrawals',
                                    'author' => '✍️ Authors'
                                ];
                            @endphp
                            {{ $typeLabels[$type] ?? ucfirst($type) . 's' }}
                            <span class="ml-2 text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">
                                {{ count($items) }}
                            </span>
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($items as $item)
                            <a href="{{ $item['url'] }}" 
                               class="flex items-center gap-4 px-6 py-4 hover:bg-purple-50/50 transition group">
                                <div class="w-10 h-10 bg-gradient-to-br 
                                    @if($type == 'book') from-purple-500 to-indigo-500
                                    @elseif($type == 'quiz') from-indigo-500 to-blue-500
                                    @elseif($type == 'group') from-blue-500 to-cyan-500
                                    @elseif($type == 'marketplace') from-orange-500 to-red-500
                                    @elseif($type == 'institution') from-violet-500 to-purple-500
                                    @elseif($type == 'certificate') from-pink-500 to-rose-500
                                    @elseif($type == 'document') from-cyan-500 to-teal-500
                                    @elseif($type == 'notification') from-red-500 to-pink-500
                                    @else from-gray-400 to-gray-500
                                    @endif
                                    rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <i class="{{ $item['icon'] ?? 'ti ti-search' }} text-white text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 group-hover:text-purple-600 transition">
                                        {{ $item['title'] }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $item['subtitle'] ?? '' }}
                                    </p>
                                </div>
                                @if(isset($item['badge']))
                                    <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full flex-shrink-0">
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                                <i class="ti ti-chevron-right text-gray-300 group-hover:text-purple-500 transition text-sm flex-shrink-0"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ti ti-search-off text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Results Found</h3>
            <p class="text-gray-500 max-w-md mx-auto">
                We couldn't find any results for "<strong>{{ $query }}</strong>". 
                Try searching for books, quizzes, groups, or institutions.
            </p>
            <div class="mt-6 flex flex-wrap gap-3 justify-center">
                <a href="{{ route('library.index') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                    Browse Books →
                </a>
                <a href="{{ route('community.index') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                    Explore Groups →
                </a>
                <a href="{{ route('quizzes.index') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                    Take Quizzes →
                </a>
            </div>
        </div>
    @endif
</div>
@endsection