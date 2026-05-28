@props(['item', 'type' => 'book', 'size' => 'md'])

@php
    $isBookmarked = method_exists($item, 'isBookmarkedByUser') ? $item->isBookmarkedByUser() : false;
    
    $sizeClasses = [
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-10 h-10 text-base',
        'lg' => 'w-12 h-12 text-lg'
    ];
    
    $iconSizes = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-xl'
    ];
@endphp

<button 
    type="button"
    onclick="toggleBookmark(this, {{ $item->id }}, '{{ $type }}')"
    data-id="{{ $item->id }}"
    data-type="{{ $type }}"
    class="bookmark-btn {{ $sizeClasses[$size] }} rounded-full flex items-center justify-center transition-all duration-200 {{ $isBookmarked ? 'bg-purple-500 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
>
    <i class="ti {{ $isBookmarked ? 'ti-bookmark-filled' : 'ti-bookmark' }} {{ $iconSizes[$size] }}"></i>
</button>

@push('scripts')
<script>
function toggleBookmark(btn, id, type) {
    const originalIcon = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader animate-spin"></i>';
    
    fetch('{{ route("bookmark.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            bookmarkable_id: id,
            bookmarkable_type: type
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.bookmarked) {
            btn.classList.remove('bg-gray-100', 'text-gray-500', 'hover:bg-gray-200');
            btn.classList.add('bg-purple-500', 'text-white');
            btn.innerHTML = '<i class="ti ti-bookmark-filled"></i>';
            
            // Show success message (optional)
            if (typeof showToast === 'function') {
                showToast('Added to bookmarks', 'success');
            }
        } else {
            btn.classList.remove('bg-purple-500', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-500', 'hover:bg-gray-200');
            btn.innerHTML = '<i class="ti ti-bookmark"></i>';
            
            if (typeof showToast === 'function') {
                showToast('Removed from bookmarks', 'info');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.innerHTML = originalIcon;
        if (typeof showToast === 'function') {
            showToast('Something went wrong', 'error');
        }
    })
    .finally(() => {
        btn.disabled = false;
    });
}
</script>
@endpush