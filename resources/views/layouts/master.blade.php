@php
    $layout = 'layouts.app'; // default for users
    
    if (auth()->check()) {
        if (auth()->user()->hasRole('super_admin')) {
            $layout = 'layouts.super-admin';
        } elseif (auth()->user()->hasRole('institution_admin')) {
            $layout = 'layouts.institution';  // ← CHANGED: use dedicated institution layout
        } elseif (auth()->user()->hasRole('admin')) {
            $layout = 'layouts.admin';
        } elseif (auth()->user()->hasRole('instructor')) {
            $layout = 'layouts.instructor';
        } elseif (auth()->user()->hasRole('librarian')) {
            $layout = 'layouts.librarian';
        } else {
            $layout = 'layouts.app';
        }
    }
@endphp

@extends($layout)

@section('page-content')
    @yield('content')
@endsection