@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('documents.index') }}" class="text-jlibrary-600 hover:text-jlibrary-700">
            <i class="ti ti-arrow-left"></i> Back to Documents
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Document Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
            <div class="flex items-center gap-3">
                <i class="ti ti-file-text text-3xl"></i>
                <div>
                    <h1 class="text-2xl font-bold">{{ $document->title }}</h1>
                    <p class="text-blue-100 text-sm">
                        Uploaded {{ $document->created_at->format('F d, Y') }} • 
                        {{ number_format($document->file_size / 1024, 2) }} KB
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Chat with Document -->
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Ask Questions About This Document</h2>
            
            <div id="chat-messages" class="h-96 overflow-y-auto border rounded-lg p-4 mb-4 bg-gray-50">
                <div class="text-center text-gray-500 py-8">
                    <i class="ti ti-message-circle-2 text-4xl mb-2 block"></i>
                    <p>Ask a question about "{{ $document->title }}"</p>
                    <p class="text-sm mt-1">The AI will answer based on the document content</p>
                </div>
            </div>
            
            <div class="flex gap-2">
                <input type="text" id="question-input" 
                       placeholder="e.g., What is the main topic? Summarize this document. What are the key points?"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500">
                <button id="ask-button" class="bg-jlibrary-600 text-white px-6 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                    <i class="ti ti-send"></i> Ask
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const messagesContainer = document.getElementById('chat-messages');
    const questionInput = document.getElementById('question-input');
    const askButton = document.getElementById('ask-button');
    
    function addMessage(question, answer) {
        // Add user question
        const userDiv = document.createElement('div');
        userDiv.className = 'flex justify-end mb-3';
        userDiv.innerHTML = `
            <div class="max-w-[80%] bg-jlibrary-600 text-white rounded-lg px-4 py-2">
                <p class="text-sm">${escapeHtml(question)}</p>
            </div>
        `;
        messagesContainer.appendChild(userDiv);
        
        // Add AI answer
        const aiDiv = document.createElement('div');
        aiDiv.className = 'flex justify-start mb-3';
        aiDiv.innerHTML = `
            <div class="max-w-[80%] bg-white border rounded-lg px-4 py-2">
                <div class="flex items-center gap-2 mb-1">
                    <i class="ti ti-robot text-jlibrary-600"></i>
                    <span class="text-xs font-semibold text-jlibrary-600">Document Analysis</span>
                </div>
                <p class="text-sm whitespace-pre-line">${escapeHtml(answer)}</p>
            </div>
        `;
        messagesContainer.appendChild(aiDiv);
        
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    async function askQuestion() {
        const question = questionInput.value.trim();
        if (!question) return;
        
        addMessage(question, 'Analyzing document...');
        questionInput.value = '';
        askButton.disabled = true;
        
        try {
            const response = await fetch('{{ route("documents.ask", $document) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ question: question })
            });
            
            const data = await response.json();
            
            // Remove the loading message and add the real answer
            messagesContainer.lastChild.remove();
            addMessage(question, data.answer);
            
        } catch (error) {
            messagesContainer.lastChild.remove();
            addMessage(question, 'Error processing your question. Please try again.');
        }
        
        askButton.disabled = false;
    }
    
    askButton.addEventListener('click', askQuestion);
    questionInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') askQuestion();
    });
</script>
@endsection