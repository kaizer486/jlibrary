<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JLIBRARY AI Assistant</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: #0a0e27;
            min-height: 100vh;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
        
        @keyframes bounce {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-4px); }
        }
        .animate-bounce { animation: bounce 1s infinite ease-in-out; }
        
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #7c3aed; border-radius: 10px; }
        
        .chat-item {
            margin-bottom: 10px;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .chat-item:last-child {
            margin-bottom: 0;
        }
        
        .menu-dots {
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .chat-item:hover .menu-dots {
            opacity: 1;
        }
        
        .chat-menu-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            background: #1a1f3e;
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
            min-width: 130px;
            z-index: 50;
            border: 1px solid rgba(255,255,255,0.1);
            overflow: hidden;
        }
        .chat-menu-dropdown button {
            width: 100%;
            text-align: left;
            padding: 8px 14px;
            font-size: 12px;
            color: #e0e0e0;
            transition: all 0.15s ease;
        }
        .chat-menu-dropdown button:hover {
            background: #2d2f5e;
            color: white;
        }
        .chat-menu-dropdown button.delete:hover {
            background: #7f1d1d;
            color: #fca5a5;
        }
        
        .gradient-title {
            background: linear-gradient(135deg, #a78bfa, #f472b6, #c084fc);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
    </style>
</head>
<body class="bg-[#0a0e27]">

<div class="flex h-screen overflow-hidden">
    
    <!-- SIDEBAR -->
    <aside class="w-72 bg-[#0f1235] border-r border-white/10 flex flex-col">
        <div class="p-4 border-b border-white/10">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg">
                    <i class="ti ti-robot text-white text-sm"></i>
                </div>
                <div>
                    <h1 class="text-white font-semibold text-base">JLIBRARY AI</h1>
                    <p class="text-purple-300 text-[10px]">Assistant</p>
                </div>
            </div>
        </div>
        
        <div class="p-3">
            <button id="newChatBtn" class="w-full bg-gradient-to-r from-purple-500 to-pink-500 hover:scale-[1.01] transition-all text-white rounded-lg py-2 text-sm shadow-md font-medium">
                + New Chat
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto px-3 py-2">
            <p class="text-[11px] text-purple-300 uppercase tracking-wider px-2 mb-2">Recent Chats</p>
            <div id="chatHistoryList" class="space-y-2">
                @forelse($sessions as $session)
                <div data-session-id="{{ $session->id }}" class="chat-item relative bg-white/5 hover:bg-white/10 transition-all rounded-lg p-2.5 cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-xs truncate">{{ $session->title }}</p>
                            <p class="text-[10px] text-purple-300">{{ $session->updated_at->diffForHumans() }}</p>
                        </div>
                        <div class="menu-dots relative">
                            <button class="chat-menu-btn w-6 h-6 rounded-lg hover:bg-white/10 flex items-center justify-center" data-session-id="{{ $session->id }}" data-session-title="{{ $session->title }}">
                                <i class="ti ti-dots-vertical text-purple-300 text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-purple-300 text-xs py-8">
                    <i class="ti ti-message-circle-2 text-2xl mb-2 block"></i>
                    <p>No chats yet</p>
                    <p class="text-[10px] mt-1">Click New Chat to start</p>
                </div>
                @endforelse
            </div>
        </div>
        
        <div class="border-t border-white/10">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-3 text-purple-300 hover:text-white hover:bg-white/5 transition text-sm">
                <i class="ti ti-arrow-left text-sm"></i>
                <span>Back to Site</span>
            </a>
            <hr class="border-white/10">
            <div class="p-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                        <i class="ti ti-user text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-white text-xs font-medium truncate">{{ Auth::user()->full_name }}</p>
                        <p class="text-purple-300 text-[10px] truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    
    <!-- MAIN CHAT AREA -->
    <main class="flex-1 flex flex-col bg-gradient-to-b from-[#0a0e27] to-[#0f1235]">
        
        <!-- Top Bar -->
        <div class="h-14 px-5 flex items-center justify-center border-b border-white/10">
            <h1 class="text-xl font-bold gradient-title">JLIBRARY AI Assistant</h1>
        </div>
        
        <!-- WELCOME SCREEN - AT THE VERY TOP (no flex-1 pushing it down) -->
        <div id="welcomeScreen" class="flex flex-col items-center justify-start text-center pt-10 pb-10 {{ ($currentSession && $currentSession->messages && count($currentSession->messages) > 0) ? 'hidden' : '' }}">
            
            <!-- Robot Image -->
            <div class="relative mb-3">
                <div class="absolute inset-0 bg-purple-500/20 blur-3xl rounded-full"></div>
                <img src="{{ asset('images/ChatGPT Image May 29, 2026, 08_07_04 PM.png') }}" 
                     alt="AI Robot" 
                     class="relative w-56 h-56 object-contain animate-float drop-shadow-[0_10px_30px_rgba(168,85,247,0.3)]">
            </div>
            
            <!-- Welcome Message -->
            <h1 class="text-2xl font-bold text-white mb-1">
                Hello, {{ Auth::user()->full_name }} 👋
            </h1>
            <p class="text-purple-200 text-sm mb-6">
                What would you like to do today?
            </p>
            
            <!-- Prompt Buttons -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 max-w-3xl w-full">
                <button class="prompt-btn bg-gradient-to-r from-purple-500 to-pink-500 hover:scale-[1.01] transition-all rounded-lg py-2.5 text-white text-xs font-medium shadow-md">
                    📚 Summarize a book
                </button>
                <button class="prompt-btn bg-gradient-to-r from-purple-500 to-pink-500 hover:scale-[1.01] transition-all rounded-lg py-2.5 text-white text-xs font-medium shadow-md">
                    💻 Help with coding
                </button>
                <button class="prompt-btn bg-gradient-to-r from-purple-500 to-pink-500 hover:scale-[1.01] transition-all rounded-lg py-2.5 text-white text-xs font-medium shadow-md">
                    🌍 Translate content
                </button>
                <button class="prompt-btn bg-gradient-to-r from-purple-500 to-pink-500 hover:scale-[1.01] transition-all rounded-lg py-2.5 text-white text-xs font-medium shadow-md">
                    📊 Analyze document
                </button>
            </div>
            
        </div>
        
        <!-- Messages Area (hidden when no messages) -->
        <div id="messagesArea" class="flex-1 overflow-y-auto px-5 py-4 {{ ($currentSession && $currentSession->messages && count($currentSession->messages) > 0) ? 'block' : 'hidden' }}">
            <div class="max-w-3xl mx-auto">
                <div id="chatMessages">
                    @if($currentSession && $currentSession->messages && count($currentSession->messages) > 0)
                        @foreach($currentSession->messages as $msg)
                        <div class="mb-3 {{ $msg['role'] == 'user' ? 'text-right' : '' }}">
                            <div class="inline-block {{ $msg['role'] == 'user' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white' : 'bg-white/10 text-white' }} rounded-xl px-3 py-1.5 max-w-[80%]">
                                <p class="text-xs">{{ $msg['content'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                <div id="typingIndicator" class="hidden mb-3">
                    <div class="inline-block bg-white/10 rounded-xl px-3 py-2">
                        <div class="flex gap-1">
                            <span class="w-1.5 h-1.5 bg-purple-400 rounded-full animate-bounce"></span>
                            <span class="w-1.5 h-1.5 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                            <span class="w-1.5 h-1.5 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- INPUT AREA - Fixed at bottom -->
        <div class="px-5 pb-5 pt-3">
            <div class="max-w-3xl mx-auto">
                <div class="flex items-center bg-white/10 border border-white/15 rounded-xl px-3 py-1.5 focus-within:border-purple-500 focus-within:ring-1 focus-within:ring-purple-500 transition">
                    <button class="text-purple-300 text-lg mr-2 hover:text-white transition">
                        <i class="ti ti-plus"></i>
                    </button>
                    <input type="text" id="messageInput" 
                        placeholder="Ask anything..."
                        class="flex-1 bg-transparent outline-none text-white placeholder:text-purple-300/60 text-sm py-2">
                    <button id="sendBtn" class="w-8 h-8 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 text-white flex items-center justify-center shadow-md hover:scale-105 transition">
                        <i class="ti ti-send text-sm"></i>
                    </button>
                </div>
                <p class="text-purple-300/50 text-[10px] text-center mt-2">Press Enter to send</p>
            </div>
        </div>
        
    </main>
    
</div>

<script>
let currentSessionId = {{ $currentSession ? $currentSession->id : 'null' }};
let isSending = false;
let activeDropdown = null;

const chatMessages = document.getElementById('chatMessages');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const typingIndicator = document.getElementById('typingIndicator');
const welcomeScreen = document.getElementById('welcomeScreen');
const messagesArea = document.getElementById('messagesArea');
const newChatBtn = document.getElementById('newChatBtn');

function addMessage(role, content) {
    // Hide welcome screen, show messages area
    if (welcomeScreen && !welcomeScreen.classList.contains('hidden')) {
        welcomeScreen.classList.add('hidden');
        messagesArea.classList.remove('hidden');
    }
    
    const div = document.createElement('div');
    div.className = `mb-3 ${role === 'user' ? 'text-right' : ''}`;
    
    if (role === 'user') {
        div.innerHTML = `
            <div class="inline-block bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl px-3 py-1.5 max-w-[80%]">
                <p class="text-xs">${escapeHtml(content)}</p>
            </div>
        `;
    } else {
        div.innerHTML = `
            <div class="inline-block bg-white/10 text-white rounded-xl px-3 py-1.5 max-w-[80%]">
                <p class="text-xs">${escapeHtml(content)}</p>
            </div>
        `;
    }
    
    chatMessages.appendChild(div);
    const messagesContainer = document.getElementById('messagesArea');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

function showTyping() {
    typingIndicator.classList.remove('hidden');
    const messagesContainer = document.getElementById('messagesArea');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

function hideTyping() {
    typingIndicator.classList.add('hidden');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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
            addMessage('assistant', 'Sorry, something went wrong. Please try again.');
        }
    } catch (error) {
        hideTyping();
        addMessage('assistant', 'Network error. Please check your connection.');
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

async function deleteChat(sessionId) {
    try {
        await fetch('/ai/session/' + sessionId, { 
            method: 'DELETE', 
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
        });
        location.reload();
    } catch (error) {
        console.error('Delete failed:', error);
    }
}

document.addEventListener('click', function(e) {
    if (activeDropdown && !activeDropdown.contains(e.target)) {
        activeDropdown.remove();
        activeDropdown = null;
    }
});

document.querySelectorAll('.chat-menu-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        
        const sessionId = this.dataset.sessionId;
        const sessionTitle = this.dataset.sessionTitle;
        
        if (activeDropdown) {
            activeDropdown.remove();
            activeDropdown = null;
        }
        
        const dropdown = document.createElement('div');
        dropdown.className = 'chat-menu-dropdown';
        dropdown.innerHTML = `
            <button class="copy-chat" data-title="${escapeHtml(sessionTitle)}">
                <i class="ti ti-copy mr-2"></i> Copy title
            </button>
            <button class="delete-chat delete" data-id="${sessionId}">
                <i class="ti ti-trash mr-2"></i> Delete chat
            </button>
        `;
        
        const rect = btn.getBoundingClientRect();
        dropdown.style.position = 'fixed';
        dropdown.style.top = `${rect.bottom + 5}px`;
        dropdown.style.right = `${window.innerWidth - rect.right + 5}px`;
        document.body.appendChild(dropdown);
        activeDropdown = dropdown;
        
        dropdown.querySelector('.copy-chat').addEventListener('click', function(e) {
            e.stopPropagation();
            navigator.clipboard.writeText(this.dataset.title);
            dropdown.remove();
            activeDropdown = null;
        });
        
        dropdown.querySelector('.delete-chat').addEventListener('click', function(e) {
            e.stopPropagation();
            deleteChat(this.dataset.id);
            dropdown.remove();
            activeDropdown = null;
        });
    });
});

sendBtn.addEventListener('click', sendMessage);
newChatBtn.addEventListener('click', newChat);
messageInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') { e.preventDefault(); sendMessage(); } });

document.querySelectorAll('.chat-item').forEach(item => {
    item.addEventListener('click', (e) => {
        if (!e.target.closest('.menu-dots') && !e.target.closest('.chat-menu-btn')) {
            window.location.href = '{{ route("ai.chat") }}?chat_session=' + item.dataset.sessionId;
        }
    });
});

document.querySelectorAll('.prompt-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const text = btn.textContent.trim().replace(/^[^\w]+/, '');
        messageInput.value = text;
        sendMessage();
    });
});

messageInput.focus();
</script>

</body>
</html>