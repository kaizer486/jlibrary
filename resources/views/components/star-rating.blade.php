@props(['rating' => 0, 'readonly' => false, 'bookId' => null, 'size' => 'md'])

@php
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    
    $sizes = [
        'sm' => 'w-3.5 h-3.5 text-sm',
        'md' => 'w-5 h-5 text-base',
        'lg' => 'w-6 h-6 text-lg',
        'xl' => 'w-7 h-7 text-xl'
    ];
    
    $starClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="flex items-center gap-0.5">
    @if($readonly)
        {{-- Read-only stars --}}
        @for($i = 1; $i <= 5; $i++)
            @if($i <= $fullStars)
                <i class="ti ti-star-filled text-yellow-400 {{ $starClass }}"></i>
            @elseif($i == $fullStars + 1 && $halfStar)
                <i class="ti ti-star-half-filled text-yellow-400 {{ $starClass }}"></i>
            @else
                <i class="ti ti-star text-gray-300 {{ $starClass }}"></i>
            @endif
        @endfor
    @else
        {{-- Interactive stars for rating --}}
        <div class="flex items-center gap-0.5" id="star-rating-{{ $bookId }}">
            @for($i = 1; $i <= 5; $i++)
                <button type="button" 
                        data-rating="{{ $i }}"
                        onclick="submitRating({{ $bookId }}, {{ $i }})"
                        class="star-btn transition-all hover:scale-110 focus:outline-none">
                    <i class="ti ti-star text-gray-300 hover:text-yellow-400 {{ $starClass }}"></i>
                </button>
            @endfor
        </div>
    @endif
</div>

@push('scripts')
<script>
function submitRating(bookId, rating) {
    const btn = event.currentTarget;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    
    fetch(`/books/${bookId}/rate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ rating: rating })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update stars display
            updateStarsDisplay(bookId, rating);
            
            // Update average display
            const avgElement = document.getElementById(`avg-rating-${bookId}`);
            if (avgElement) {
                avgElement.innerHTML = data.average;
            }
            
            // Update count display
            const countElement = document.getElementById(`rating-count-${bookId}`);
            if (countElement) {
                countElement.innerHTML = `(${data.count} ratings)`;
            }
            
            // Show success message
            alert(data.message);
            
            // Reload to show updated UI
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong. Please try again.');
    })
    .finally(() => {
        btn.disabled = false;
    });
}

function updateStarsDisplay(bookId, rating) {
    const container = document.getElementById(`star-rating-${bookId}`);
    if (!container) return;
    
    const stars = container.querySelectorAll('.star-btn i');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('ti-star', 'text-gray-300');
            star.classList.add('ti-star-filled', 'text-yellow-400');
        } else {
            star.classList.remove('ti-star-filled', 'text-yellow-400');
            star.classList.add('ti-star', 'text-gray-300');
        }
    });
}
</script>
@endpush