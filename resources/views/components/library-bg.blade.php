@php
    $hasBgImage = file_exists(public_path('images/library-bg.jpg'));
@endphp

<div class="library-bg {{ $hasBgImage ? '' : 'bg-gradient-to-br from-amber-900 via-amber-800 to-amber-950' }}" 
     @if($hasBgImage) style="background-image: url('{{ asset('images/library.png') }}');" @endif>
    <div class="content-wrapper">
        {{ $slot }}
    </div>
</div>