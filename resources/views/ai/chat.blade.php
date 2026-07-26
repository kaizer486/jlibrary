<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JLIBRARY AI Assistant</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
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
            position: fixed;
            right: 16px;
            top: auto;
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
        
        .ai-message {
            line-height: 1.6;
        }
       
     
      .ai-message br {
    display: block;
    content: "";
    margin-top: 6px;
}


.ai-message p {
    margin-bottom: 12px;
    line-height: 1.8;
}

.ai-message p:last-child {
    margin-bottom: 0;
}

.ai-message ol {
    list-style: decimal;
    padding-left: 1.5em;
    margin: 8px 0 14px 0;
}

.ai-message ol li {
    margin-bottom: 6px;
    line-height: 1.6;
}

.ai-message ul {
    list-style: disc;
    padding-left: 1.5em;
    margin: 8px 0 14px 0;
}

.ai-message ol > li,
.ai-message ul > li {
    margin-bottom: 10px;
    padding-left: 4px;
}


.ai-message ol ol,
.ai-message ul ul,
.ai-message ol ul,
.ai-message ul ol {
    margin-top: 6px;
    margin-bottom: 6px;
}

.ai-message ul li {
    margin-bottom: 4px;
    line-height: 1.6;
}

.ai-message strong {
    color: #c084fc;
    font-weight: 600;
}


.ai-message li::marker {
    color: #a78bfa;
    font-weight: 600;
}

.ai-message li strong:first-child {
    color: #e9d5ff;
}

/* ========================================== */
/* FIX: DROPDOWN VISIBILITY                   */
/* ========================================== */
select option {
    background: #1a1f3e !important;
    color: #ffffff !important;
    padding: 8px 12px !important;
    font-size: 12px !important;
}

select option:hover,
select option:focus,
select option:checked {
    background: #7c3aed !important;
    color: #ffffff !important;
}

/* For Firefox */
select option {
    background: #1a1f3e;
    color: #ffffff;
}

/* For the select itself - ensure it has a proper background */
#documentSelector {
    background-color: rgba(26, 31, 62, 0.9) !important;
    color: #ffffff !important;
    cursor: pointer;
}

#documentSelector option {
    background-color: #1a1f3e !important;
    color: #ffffff !important;
    padding: 8px 12px;
}

#documentSelector option:hover {
    background-color: #7c3aed !important;
}

/* Dropdown arrow fix */
#documentSelector {
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23999' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    padding-right: 30px;
}

#documentSelector option:checked {
    background: #7c3aed !important;
    color: #ffffff !important;
}
        

        /* Mobile Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 40;
        }
        .sidebar-overlay.active {
            display: block;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -100%;
                top: 0;
                bottom: 0;
                width: 85%;
                max-width: 300px;
                z-index: 50;
                transition: left 0.3s ease;
                border-radius: 0;
            }
            .sidebar.open {
                left: 0;
            }
            
            .sidebar-overlay.active {
                display: block;
            }
            
            .mobile-header {
                display: flex !important;
            }
            
            .desktop-header {
                display: none !important;
            }
            
            .prompt-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 8px !important;
            }
            
            .prompt-btn {
                font-size: 11px !important;
                padding: 10px 8px !important;
            }
            
            .welcome-robot {
                width: 140px !important;
                height: 140px !important;
            }
            
            .welcome-title {
                font-size: 20px !important;
            }
            
            .welcome-subtitle {
                font-size: 14px !important;
            }
        }

        @media (max-width: 480px) {
            .prompt-grid {
                grid-template-columns: 1fr 1fr !important;
            }
            
            .prompt-btn {
                font-size: 10px !important;
                padding: 8px 6px !important;
            }
            
            .welcome-robot {
                width: 100px !important;
                height: 100px !important;
            }
            
            .welcome-title {
                font-size: 18px !important;
            }
            
            .chat-message {
                max-width: 92% !important;
            }
            
            .input-area {
                padding: 8px 10px !important;
            }
        }
    </style>
</head>
<body class="bg-[#0a0e27]">

<!-- MOBILE SIDEBAR OVERLAY -->
<div id="sidebarOverlay" class="sidebar-overlay" onclick="closeSidebar()"></div>

<div class="flex h-screen overflow-hidden">
    
    <!-- SIDEBAR -->
    <aside id="sidebar" class="sidebar w-72 bg-[#0f1235] border-r border-white/10 flex flex-col flex-shrink-0">
        <div class="p-4 border-b border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg">
                    <i class="ti ti-robot text-white text-sm"></i>
                </div>
                <div>
                    <h1 class="text-white font-semibold text-base">JLIBRARY AI</h1>
                    <p class="text-purple-300 text-[10px]">Assistant</p>
                </div>
            </div>
            <!-- Mobile Close Button -->
            <button onclick="closeSidebar()" class="md:hidden text-purple-300 hover:text-white transition">
                <i class="ti ti-x text-xl"></i>
            </button>
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
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center flex-shrink-0">
                        <i class="ti ti-user text-white text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-xs font-medium truncate">{{ Auth::user()->full_name }}</p>
                        <p class="text-purple-300 text-[10px] truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    
    <!-- MAIN CHAT AREA -->
    <main class="flex-1 flex flex-col bg-gradient-to-b from-[#0a0e27] to-[#0f1235] min-w-0">
        
        <!-- Top Bar -->
        <div class="h-14 px-4 flex items-center justify-between border-b border-white/10 flex-shrink-0">
            <!-- Mobile Menu Button -->
            <button onclick="openSidebar()" class="md:hidden text-purple-300 hover:text-white transition">
                <i class="ti ti-menu-2 text-xl"></i>
            </button>
            
            <h1 class="text-xl font-bold gradient-title truncate">JLIBRARY AI Assistant</h1>
            
            <!-- Mobile New Chat Button -->
            <button onclick="newChat()" class="md:hidden text-purple-300 hover:text-white transition">
                <i class="ti ti-plus text-xl"></i>
            </button>
            
            <!-- Desktop Placeholder -->
            <div class="w-8 md:block hidden"></div>
        </div>
        
        <!-- WELCOME SCREEN -->
        <div id="welcomeScreen" class="flex flex-col items-center justify-start text-center pt-6 pb-10 px-4 {{ ($currentSession && $currentSession->messages && count($currentSession->messages) > 0) ? 'hidden' : '' }}">
            
            <!-- Robot Image -->
            <div class="relative mb-3">
                <div class="absolute inset-0 bg-purple-500/20 blur-3xl rounded-full"></div>
                <img src="{{ asset('images/ChatGPT Image May 29, 2026, 08_07_04 PM.png') }}" 
                     alt="AI Robot" 
                     class="welcome-robot relative w-56 h-56 object-contain animate-float drop-shadow-[0_10px_30px_rgba(168,85,247,0.3)]">
            </div>
            
            <!-- Welcome Message -->
            <h1 class="welcome-title text-2xl font-bold text-white mb-1">
                Hello, {{ Auth::user()->full_name }} 
            </h1>
            <p class="welcome-subtitle text-purple-200 text-sm mb-6">
                What would you like to do today?
            </p>
            
            <!-- Prompt Buttons -->
            <div class="prompt-grid grid grid-cols-2 md:grid-cols-4 gap-3 max-w-3xl w-full">
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
        
        <!-- Messages Area -->
        <div id="messagesArea" class="flex-1 overflow-y-auto px-4 py-4 {{ ($currentSession && $currentSession->messages && count($currentSession->messages) > 0) ? 'block' : 'hidden' }}">
            <div class="max-w-3xl mx-auto">
                <div id="chatMessages">
                    @if($currentSession && $currentSession->messages && count($currentSession->messages) > 0)
                        @foreach($currentSession->messages as $msg)
                        <div class="mb-3 {{ $msg['role'] == 'user' ? 'text-right' : '' }}">
                            <div class="chat-message inline-block {{ $msg['role'] == 'user' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white' : 'bg-white/10 text-white' }} rounded-xl px-4 py-2 max-w-[85%] md:max-w-[80%]">
                                @if($msg['role'] == 'user')
                                    <p class="text-sm">{{ $msg['content'] }}</p>
                                @else
                                    <div class="ai-message text-sm">
                                        <span class="ai-markdown" data-content="{{ e($msg['content']) }}"></span>
                                    </div>
                                @endif
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

        <!-- DOCUMENT SELECTOR - Add before INPUT AREA -->
<div class="px-4 pb-1">
    <div class="max-w-3xl mx-auto">
        <div class="flex flex-wrap items-center gap-2 mb-1">
            @if(isset($documents) && $documents->count() > 0)
                <span class="text-purple-300/60 text-xs flex items-center gap-1">
                    <i class="ti ti-file-text text-xs"></i> Reference:
                </span>
                <select id="documentSelector" 
                        class="bg-white/10 border border-white/15 text-white text-xs rounded-lg px-3 py-1.5 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 max-w-[220px]">
                    <option value="">None</option>
                    @foreach($documents as $doc)
                        <option value="{{ $doc->id }}" style="color: #1a1a2e; background: white; padding: 4px 8px;">
                            {{ Str::limit($doc->title, 30) }}
                        </option>
                    @endforeach
                </select>
                <span class="text-purple-300/40 text-xs">Ask about your document</span>
            @else
                <span class="text-purple-300/40 text-xs flex items-center gap-1">
                    <i class="ti ti-file-text text-xs"></i> No documents
                </span>
                <a href="{{ route('documents.create') }}" class="text-purple-400 hover:text-purple-300 text-xs underline">Upload a document</a>
            @endif
        </div>
    </div>
</div>

        <!-- INPUT AREA -->
        <div class="px-4 pb-4 pt-2 flex-shrink-0">
            <div class="max-w-3xl mx-auto">
                <div class="input-area flex items-center bg-white/10 border border-white/15 rounded-xl px-3 py-1.5 focus-within:border-purple-500 focus-within:ring-1 focus-within:ring-purple-500 transition">
                    <button class="text-purple-300 text-lg mr-2 hover:text-white transition flex-shrink-0">
                        <i class="ti ti-plus"></i>
                    </button>
                    <input type="text" id="messageInput" 
                        placeholder="Ask anything..."
                        class="flex-1 bg-transparent outline-none text-white placeholder:text-purple-300/60 text-sm py-2 min-w-0">
                    <button id="sendBtn" class="w-8 h-8 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 text-white flex items-center justify-center shadow-md hover:scale-105 transition flex-shrink-0">
                        <i class="ti ti-send text-sm"></i>
                    </button>
                </div>
                <p class="text-purple-300/50 text-[10px] text-center mt-2">Press Enter to send</p>
            </div>
        </div>
        
    </main>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
// ============================================
// CONFIGURATION
// ============================================
marked.setOptions({
    breaks: true,
    gfm: true,
});

// ============================================
// STATE VARIABLES
// ============================================
let currentSessionId = {{ $currentSession ? $currentSession->id : 'null' }};
let isSending = false;
let activeDropdown = null;

// Data for auto-firing an analysis (fresh upload) or a redirected
// document question on page load, without changing manual sending.
const autoAnalyze = @json($autoAnalyze ?? false);
const autoAnalyzePrompt = @json($autoAnalyzePrompt ?? null);
const autoAnalyzeDisplay = @json($autoAnalyzeDisplay ?? null);
const autoQuestionText = @json($autoQuestion ?? null);
const preselectedDocumentId = @json($selectedDocumentId ?? null);

// ============================================
// DOM ELEMENTS
// ============================================
const chatMessages = document.getElementById('chatMessages');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const typingIndicator = document.getElementById('typingIndicator');
const welcomeScreen = document.getElementById('welcomeScreen');
const messagesArea = document.getElementById('messagesArea');
const newChatBtn = document.getElementById('newChatBtn');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');

// ============================================
// SIDEBAR FUNCTIONS
// ============================================
function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

// Close sidebar on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeSidebar();
});

// ============================================
// RENDER FUNCTIONS
// ============================================
document.querySelectorAll('.ai-markdown').forEach(el => {
    const raw = el.dataset.content || '';
    el.innerHTML = marked.parse(raw);
});

function renderAI(content) {
    return marked.parse(content);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================
// ADD MESSAGE FUNCTION
// ============================================
function addMessage(role, content) {
    if (welcomeScreen && !welcomeScreen.classList.contains('hidden')) {
        welcomeScreen.classList.add('hidden');
        messagesArea.classList.remove('hidden');
    }

    const div = document.createElement('div');
    div.className = `mb-3 ${role === 'user' ? 'text-right' : ''}`;

    if (role === 'user') {
        div.innerHTML = `
            <div class="chat-message inline-block bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl px-4 py-2 max-w-[85%] md:max-w-[80%]">
                <p class="text-sm">${escapeHtml(content)}</p>
            </div>`;
    } else {
       div.innerHTML = `
    <div class="chat-message inline-block bg-white/10 text-white rounded-xl px-5 py-3 max-w-[92%] md:max-w-[85%] ai-message text-sm" style="text-align:left">
        ${renderAI(content)}
    </div>`;
    }

    chatMessages.appendChild(div);

    const container = document.getElementById('messagesArea');
    if (container) setTimeout(() => { container.scrollTop = container.scrollHeight; }, 100);
}

// ============================================
// TYPING INDICATOR
// ============================================
function showTyping() {
    typingIndicator.classList.remove('hidden');
    const c = document.getElementById('messagesArea');
    if (c) c.scrollTop = c.scrollHeight;
}

function hideTyping() {
    typingIndicator.classList.add('hidden');
}

// ============================================
// NEW CHAT FUNCTION
// ============================================
async function newChat() {
    try {
        const response = await fetch('{{ route("ai.new") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        const data = await response.json();
        if (data.success) {
            window.location.href = '{{ route("ai.chat") }}?chat_session=' + data.session_id;
        }
    } catch (error) {
        console.error('New chat error:', error);
        alert('Could not create new chat. Please try again.');
    }
}

// ============================================
// SHARED NETWORK CALL (used by manual sends AND auto-analysis)
// ============================================
async function submitToAI(messageForAI, displayText, documentId) {
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
            body: JSON.stringify({
                message: messageForAI,
                display_message: displayText,
                session_id: currentSessionId,
                document_id: documentId
            })
        });

        const data = await response.json();
        hideTyping();

        if (!response.ok) {
            const errorMsg = data.response || 'Sorry, I encountered an error. Please try again.';
            addMessage('assistant', errorMsg);
            if (response.status === 429) {
                console.warn('Rate limited:', data);
            }
            return;
        }

        if (data.success) {
            addMessage('assistant', data.response);
            if (data.session_id) currentSessionId = data.session_id;
        } else {
            addMessage('assistant', data.response || 'Sorry, I encountered an error. Please try again.');
        }
    } catch (error) {
        hideTyping();
        console.error('Fetch error:', error);
        addMessage('assistant', 'Connection error. Please check your internet and try again.');
    }

    isSending = false;
    sendBtn.disabled = false;
}

async function sendMessage() {
    const message = messageInput.value.trim();
    if (!message || isSending) return;

    // Get selected document
    const documentSelector = document.getElementById('documentSelector');
    const documentId = documentSelector ? documentSelector.value : null;

    addMessage('user', message);
    messageInput.value = '';

    await submitToAI(message, message, documentId);
    messageInput.focus();
}

// ============================================
// AUTO-ANALYZE / AUTO-QUESTION ON LOAD
// ============================================
async function runAutoAnalysis() {
    const documentSelector = document.getElementById('documentSelector');
    if (documentSelector && preselectedDocumentId) {
        documentSelector.value = preselectedDocumentId;
    }

    if (autoAnalyze && autoAnalyzePrompt) {
        addMessage('user', autoAnalyzeDisplay || 'Analyze this document');
        await submitToAI(autoAnalyzePrompt, autoAnalyzeDisplay, preselectedDocumentId);
        return;
    }

    if (autoQuestionText) {
        addMessage('user', autoQuestionText);
        await submitToAI(autoQuestionText, autoQuestionText, preselectedDocumentId);
    }
}

// ============================================
// DELETE CHAT FUNCTION
// FIX: previously used location.reload(), which reloads the CURRENT
// URL — including any ?analyze=1&document_id=... still in the address
// bar from a fresh upload. Deleting that session then reloading the
// same URL made the page re-run the analysis on a session that no
// longer existed. Now it navigates to a clean ai.chat URL instead.
// ============================================
async function deleteChat(sessionId) {
    try {
        await fetch('/ai/session/' + sessionId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        window.location.href = '{{ route("ai.chat") }}';
    } catch (error) {
        console.error('Delete failed:', error);
    }
}

// ============================================
// EVENT LISTENERS
// ============================================

// 1. Send Button
if (sendBtn) {
    sendBtn.addEventListener('click', function(e) {
        e.preventDefault();
        sendMessage();
    });
}

// 2. New Chat Button
if (newChatBtn) {
    newChatBtn.addEventListener('click', function(e) {
        e.preventDefault();
        newChat();
    });
}

// 3. Enter Key on Message Input
if (messageInput) {
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
}

// 4. Click on Chat Items (sidebar)
document.querySelectorAll('.chat-item').forEach(item => {
    item.addEventListener('click', function(e) {
        if (!e.target.closest('.menu-dots') && !e.target.closest('.chat-menu-btn')) {
            window.location.href = '{{ route("ai.chat") }}?chat_session=' + this.dataset.sessionId;
        }
    });
});

// 5. Prompt Buttons
document.querySelectorAll('.prompt-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const text = this.textContent.trim().replace(/^[^\w]+/, '');
        messageInput.value = text;
        sendMessage();
    });
});

// 6. Chat Menu Dropdown (three dots)
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

// ============================================
// FOCUS ON LOAD + AUTO-FIRE ANALYSIS/QUESTION
// ============================================
messageInput.focus();

if (autoAnalyze || autoQuestionText) {
    runAutoAnalysis();
}
</script>
</body>
</html>
