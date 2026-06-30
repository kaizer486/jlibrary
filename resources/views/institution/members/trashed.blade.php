@extends('layouts.librarian')

@section('title', 'Trashed Members')
@section('page-title', '🗑️ Trashed Members')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-12 text-center">
        <h3 class="text-xl font-semibold text-white mb-2">Trash View - Working</h3>
        <p class="text-slate-400">If you see this, the view is loading correctly.</p>
        <p class="text-slate-400 mt-4">Total trashed: {{ isset($stats['trashed']) ? $stats['trashed'] : 0 }}</p>
        <p class="text-slate-400">Total active: {{ isset($stats['total']) ? $stats['total'] : 0 }}</p>
        <a href="{{ route('institution.members.index') }}" class="inline-block mt-4 bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg transition">
            <i class="ti ti-arrow-left"></i> Back to Members
        </a>
    </div>
</div>
@endsection