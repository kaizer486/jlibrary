<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-3">
        <h2 class="text-white font-semibold flex items-center gap-2">
            <i class="ti ti-quote text-xl"></i> Quote of the Day
        </h2>
    </div>
    <div class="p-6" id="quote-container">
        <div class="text-center py-8">
            <div class="animate-pulse">
                <i class="ti ti-loader-2 text-3xl text-purple-600 animate-spin"></i>
                <p class="text-gray-500 mt-2">Loading quote...</p>
            </div>
        </div>
    </div>
</div>

<script>
function loadQuoteOfTheDay() {
    fetch('/api/quote-of-the-day')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.quote) {
                displayQuote(data.quote, data.is_favorited);
            } else {
                document.getElementById('quote-container').innerHTML = `
                    <div class="text-center py-8">
                        <i class="ti ti-quote-off text-3xl text-gray-400"></i>
                        <p class="text-gray-500 mt-2">No quotes available yet.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading quote:', error);
            document.getElementById('quote-container').innerHTML = `
                <div class="text-center py-8">
                    <i class="ti ti-alert-circle text-3xl text-red-400"></i>
                    <p class="text-gray-500 mt-2">Unable to load quote. Please try again.</p>
                </div>
            `;
        });
}

function displayQuote(quote, isFavorited) {
    const heartIcon = isFavorited ? 'ti ti-heart-filled text-red-500' : 'ti ti-heart';
    
    document.getElementById('quote-container').innerHTML = `
        <div class="text-center">
            <i class="ti ti-quote text-4xl text-purple-300 mb-3 block"></i>
            <p class="text-gray-700 text-lg md:text-xl italic leading-relaxed">"${escapeHtml(quote.quote_text)}"</p>
            <p class="text-gray-500 mt-4">— ${quote.author ? escapeHtml(quote.author) : 'Unknown'}</p>
            <div class="flex justify-center gap-4 mt-6">
                <button onclick="toggleFavorite(${quote.id})" class="flex items-center gap-1 text-gray-500 hover:text-red-500 transition">
                    <i class="${heartIcon}"></i> <span id="save-text">${isFavorited ? 'Saved' : 'Save'}</span>
                </button>
                <button onclick="shareQuote(${quote.id})" class="flex items-center gap-1 text-gray-500 hover:text-blue-500 transition">
                    <i class="ti ti-share"></i> Share
                </button>
                <button onclick="nextQuote()" class="flex items-center gap-1 text-gray-500 hover:text-purple-500 transition">
                    <i class="ti ti-refresh"></i> Next
                </button>
                <button onclick="copyQuote('${escapeHtml(quote.quote_text)}', '${escapeHtml(quote.author || 'Unknown')}')" class="flex items-center gap-1 text-gray-500 hover:text-green-500 transition">
                    <i class="ti ti-copy"></i> Copy
                </button>
            </div>
        </div>
    `;
}

function toggleFavorite(quoteId) {
    fetch(`/api/quote/${quoteId}/favorite`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadQuoteOfTheDay();
            }
        });
}

function shareQuote(quoteId) {
    fetch(`/api/quote/${quoteId}/share`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Quote shared!');
            }
        });
}

function nextQuote() {
    fetch('/api/quote/next')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.quote) {
                displayQuote(data.quote, data.is_favorited);
            }
        });
}

function copyQuote(quoteText, author) {
    const text = `"${quoteText}" — ${author}`;
    navigator.clipboard.writeText(text);
    alert('Quote copied to clipboard!');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load quote when page loads
document.addEventListener('DOMContentLoaded', loadQuoteOfTheDay);
</script>