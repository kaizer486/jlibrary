@extends('layouts.app')

{{-- IDE Helper - Removes yellow lines --}}
@php
/** @var \Illuminate\Database\Eloquent\Collection|\App\Models\ChatSession[] $sessions */
/** @var \App\Models\ChatSession|null $currentSession */
@endphp

@section('content')
<div class="flex h-screen bg-gray-50">
    
    <!-- SIDEBAR - Chat History -->
    <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-5 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                    <i class="ti ti-robot text-white text-sm"></i>
                </div>
                <h1 class="font-semibold text-gray-800">JLIBRARY AI</h1>
            </div>
        </div>
        
        <div class="p-3">
            <button id="newChatBtn" class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-xl py-2.5 text-sm font-medium transition flex items-center justify-center gap-2">
                <i class="ti ti-plus"></i>
                New chat
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto px-2 space-y-1">
            <p class="text-xs text-gray-400 px-3 py-2">RECENT CHATS</p>
            <div id="chatHistoryList">
                @forelse($sessions as $session)
                <div data-session-id="{{ $session->id }}" class="chat-history-item group flex items-center justify-between px-3 py-2 rounded-lg hover:bg-gray-100 cursor-pointer transition">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <i class="ti ti-message-circle-2 text-gray-400 text-sm"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700 truncate">{{ $session->title }}</p>
                            <p class="text-xs text-gray-400">{{ $session->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <button class="delete-session text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition p-1" data-id="{{ $session->id }}">
                        <i class="ti ti-trash text-sm"></i>
                    </button>
                </div>
                @empty
                <div class="text-center text-gray-400 text-sm py-4">
                    <i class="ti ti-message-circle-2 text-2xl"></i>
                    <p class="mt-1">No chats yet</p>
                </div>
                @endforelse
            </div>
        </div>
        
        <div class="border-t border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="ti ti-user text-purple-600 text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MAIN CHAT AREA -->
    <div class="flex-1 flex flex-col">
        
        <div class="bg-white border-b border-gray-100 px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">AI Assistant</h2>
                    <p class="text-xs text-gray-400">Powered by Google Gemini 2.5 Flash</p>
                </div>
                <button id="clearChatBtn" class="text-gray-400 hover:text-red-500 transition p-2 rounded-lg hover:bg-gray-100">
                    <i class="ti ti-trash text-lg"></i>
                </button>
            </div>
        </div>
        
        <div id="messages" class="flex-1 overflow-y-auto px-8 py-6">
            <div class="max-w-3xl mx-auto">
                
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-2xl mb-4">
                        <i class="ti ti-message-circle-2 text-purple-600 text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800">Ready when you are.</h2>
                    <p class="text-gray-400 mt-1">Ask me anything about books, learning, or any topic</p>
                </div>
                
                <div id="chatMessages">
                    @if($currentSession && $currentSession->messages)
                        @foreach($currentSession->messages as $msg)
                        <div class="flex gap-3 mb-4 {{ $msg['role'] == 'user' ? 'justify-end' : '' }}">
                            @if($msg['role'] == 'assistant')
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-robot text-purple-600 text-sm"></i>
                            </div>
                            @endif
                            <div class="{{ $msg['role'] == 'user' ? 'bg-purple-600 text-white rounded-2xl rounded-tr-none' : 'bg-white border border-gray-100 rounded-2xl rounded-tl-none shadow-sm' }} px-4 py-2 max-w-[70%]">
                                <p class="text-sm">{{ $msg['content'] }}</p>
                            </div>
                            @if($msg['role'] == 'user')
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-user text-purple-600 text-sm"></i>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    @endif
                </div>
                
                <div id="typingIndicator" class="hidden flex gap-3 mb-4">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="ti ti-robot text-purple-600 text-sm"></i>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl rounded-tl-none px-4 py-3 shadow-sm">
                        <div class="flex gap-1">
                            <span class="w-2 h-2 bg-purple-400 rounded-full animate-bounce"></span>
                            <span class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                            <span class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div class="border-t border-gray-100 bg-white p-4">
            <div class="max-w-3xl mx-auto">
                <div class="flex items-center bg-gray-50 rounded-2xl border border-gray-200 focus-within:border-purple-400 focus-within:ring-2 focus-within:ring-purple-100 transition">
                    <button id="attachBtn" class="text-gray-400 hover:text-purple-600 p-3 rounded-l-2xl transition">
                        <i class="ti ti-plus text-xl"></i>
                    </button>
                    <input type="text" id="messageInput" 
                        placeholder="Ask anything"
                        class="flex-1 bg-transparent py-3 px-1 text-gray-700 focus:outline-none">
                    <button id="sendBtn" class="text-gray-400 hover:text-purple-600 p-3 rounded-r-2xl transition">
                        <i class="ti ti-send text-xl"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-400 text-center mt-2">Press Enter to send</p>
            </div>
        </div>
        
    </div>
</div>

<script>
let currentSessionId = {{ $currentSession ? $currentSession->id : 'null' }};
let isSending = false;

const chatMessages = document.getElementById('chatMessages');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const typingIndicator = document.getElementById('typingIndicator');
const clearChatBtn = document.getElementById('clearChatBtn');
const newChatBtn = document.getElementById('newChatBtn');

function addMessage(role, content) {
    const div = document.createElement('div');
    div.className = `flex gap-3 mb-4 ${role === 'user' ? 'justify-end' : ''}`;
    
    if (role === 'user') {
        div.innerHTML = `
            <div class="bg-purple-600 text-white rounded-2xl rounded-tr-none px-4 py-2 max-w-[70%]">
                <p class="text-sm">${escapeHtml(content)}</p>
            </div>
            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="ti ti-user text-purple-600 text-sm"></i>
            </div>
        `;
    } else {
        div.innerHTML = `
            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="ti ti-robot text-purple-600 text-sm"></i>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl rounded-tl-none px-4 py-2 shadow-sm max-w-[70%]">
                <p class="text-sm text-gray-700">${escapeHtml(content)}</p>
            </div>
        `;
    }
    
    chatMessages.appendChild(div);
    chatMessages.scrollIntoView({ behavior: 'smooth', block: 'end' });
}

function showTyping() {
    typingIndicator.classList.remove('hidden');
}

function hideTyping() {
    typingIndicator.classList.add('hidden');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function clearChat() {
    if (confirm('Clear all messages?')) {
        chatMessages.innerHTML = '';
        currentSessionId = null;
    }
}

async function sendMessage() {
    const message = messageInput.value.trim();
    if (!message || isSending) return;
    
    addMessage('user', message);
    messageInput.value = '';
    
    isSending = true;
    sendBtn.disabled = true;
    showTyping();
    
    try {
        const response = await fetch('{{ route("ai.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message, session_id: currentSessionId })
        });
        
        const data = await response.json();
        hideTyping();
        
        if (data.success) {
            addMessage('assistant', data.response);
            if (data.session_id) {
                currentSessionId = data.session_id;
            }
        } else {
            addMessage('assistant', 'Sorry, something went wrong.');
        }
    } catch (error) {
        hideTyping();
        addMessage('assistant', 'Network error.');
    }
    
    isSending = false;
    sendBtn.disabled = false;
    messageInput.focus();
}

async function newChat() {
    try {
        const response = await fetch('{{ route("ai.new") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        const data = await response.json();
        if (data.success) {
            window.location.href = '{{ route("ai.chat") }}?chat_session=' + data.session_id;
        }
    } catch (error) {
        alert('Could not create new chat');
    }
}

sendBtn.addEventListener('click', sendMessage);
clearChatBtn.addEventListener('click', clearChat);
newChatBtn.addEventListener('click', newChat);
messageInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') { e.preventDefault(); sendMessage(); } });

document.querySelectorAll('.chat-history-item').forEach(item => {
    item.addEventListener('click', (e) => {
        if (!e.target.closest('.delete-session')) {
            window.location.href = '{{ route("ai.chat") }}?chat_session=' + item.dataset.sessionId;
        }
    });
});

document.querySelectorAll('.delete-session').forEach(btn => {
    btn.addEventListener('click', async (e) => {
        e.stopPropagation();
        if (confirm('Delete this chat?')) {
            await fetch('/ai/session/' + btn.dataset.id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            location.reload();
        }
    });
});

messageInput.focus();
</script>

<style>
@keyframes bounce {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-6px); }
}
.animate-bounce { animation: bounce 1s infinite ease-in-out; }
#messages::-webkit-scrollbar { width: 5px; }
#messages::-webkit-scrollbar-track { background: #f1f1f1; }
#messages::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 3px; }
</style>
@endsection