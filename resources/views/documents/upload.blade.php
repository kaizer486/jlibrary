@extends('layouts.app')

@section('title', 'Upload Document')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-8">
        <a href="{{ route('documents.index') }}" class="text-jlibrary-600 hover:text-jlibrary-700 mb-4 inline-flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Documents
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Upload Document</h1>
        <p class="text-gray-600">Upload a PDF, Word document, or text file to analyze with AI</p>
    </div>
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('documents.upload') }}" enctype="multipart/form-data" id="upload-form">
            @csrf
            
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Document Title *</label>
                <input type="text" name="title" id="title" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-jlibrary-500 transition"
                       placeholder="e.g., My Research Paper, Course Notes, Book Chapter"
                       value="{{ old('title') }}">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Drag and Drop Zone -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Document File *</label>
                
                <div id="drop-zone" 
                     class="relative border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-jlibrary-500 transition-all duration-300 cursor-pointer bg-gray-50 hover:bg-jlibrary-50/30">
                    
                    <input type="file" name="document" id="document" required
                           accept=".pdf,.txt,.docx"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <div id="drop-zone-content">
                        <!-- Upload Icon -->
                        <div id="upload-icon-container" class="w-20 h-20 bg-jlibrary-100 rounded-full flex items-center justify-center mx-auto mb-4 transition-all duration-300">
                            <i id="upload-icon" class="ti ti-cloud-upload text-4xl text-jlibrary-600"></i>
                        </div>
                        
                        <!-- Upload Text -->
                        <div id="upload-text-container">
                            <p id="upload-text" class="text-gray-600 font-medium">Drag and drop your document here</p>
                            <p class="text-sm text-gray-400 mt-1">or click to browse</p>
                        </div>
                        
                        <!-- File Info (hidden initially) -->
                        <div id="file-info" class="hidden">
                            <div class="flex items-center justify-center gap-3">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="ti ti-file-check text-2xl text-green-600"></i>
                                </div>
                                <div class="text-left">
                                    <p id="file-name" class="font-medium text-gray-800"></p>
                                    <p id="file-size" class="text-sm text-gray-500"></p>
                                </div>
                            </div>
                            <button type="button" id="remove-file" class="mt-2 text-red-500 text-sm hover:text-red-700">
                                <i class="ti ti-x"></i> Remove file
                            </button>
                        </div>
                        
                        <!-- Accepted File Types -->
                        <div class="mt-4 flex flex-wrap justify-center gap-2">
                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">PDF</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">DOCX</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">TXT</span>
                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">Max 10MB</span>
                        </div>
                    </div>
                </div>
                
                @error('document')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Upload Progress -->
            <div id="upload-progress" class="hidden mb-4">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Uploading...</span>
                    <span id="progress-percentage" class="text-sm font-medium text-jlibrary-600">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div id="progress-bar" class="bg-jlibrary-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1">Please wait while your document is being uploaded</p>
            </div>
            
            <!-- Supported Formats Info -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-200">
                <h4 class="font-semibold text-blue-800 mb-2 flex items-center gap-2">
                    <i class="ti ti-info-circle"></i> What happens after upload?
                </h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>📄 The document will be processed and analyzed</li>
                    <li>🤖 You can ask questions about the document content</li>
                    <li>💡 The AI will answer based ONLY on document information</li>
                    <li>🔒 Your documents are private to your account</li>
                    <li>📚 You can reference this document in your learning</li>
                </ul>
            </div>
            
            <button type="submit" id="submit-btn" class="w-full bg-gradient-to-r from-blue-600 to-jlibrary-600 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="ti ti-cloud-upload"></i> Upload and Process Document
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('document');
        const uploadForm = document.getElementById('upload-form');
        const submitBtn = document.getElementById('submit-btn');
        
        // File info elements
        const uploadIconContainer = document.getElementById('upload-icon-container');
        const uploadIcon = document.getElementById('upload-icon');
        const uploadText = document.getElementById('upload-text');
        const uploadTextContainer = document.getElementById('upload-text-container');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const removeFileBtn = document.getElementById('remove-file');
        
        // Progress elements
        const uploadProgress = document.getElementById('upload-progress');
        const progressBar = document.getElementById('progress-bar');
        const progressPercentage = document.getElementById('progress-percentage');
        
        let selectedFile = null;
        
        // ==========================================
        // FILE SELECTION HANDLERS
        // ==========================================
        
        // Click to upload (already handled by input)
        fileInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                handleFileSelect(this.files[0]);
            }
        });
        
        // Drag and drop handlers
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-jlibrary-500', 'bg-jlibrary-50', 'scale-[1.02]');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-jlibrary-500', 'bg-jlibrary-50', 'scale-[1.02]');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-jlibrary-500', 'bg-jlibrary-50', 'scale-[1.02]');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                // Validate file type
                const validTypes = ['application/pdf', 'text/plain', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                const validExtensions = ['.pdf', '.txt', '.docx'];
                const ext = '.' + file.name.split('.').pop().toLowerCase();
                
                if (validTypes.includes(file.type) || validExtensions.includes(ext)) {
                    fileInput.files = files;
                    handleFileSelect(file);
                } else {
                    alert('Please upload a valid file (PDF, DOCX, or TXT)');
                }
            }
        });
        
        // Remove file
        removeFileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            clearFileSelection();
        });
        
        // ==========================================
        // FILE HANDLING FUNCTIONS
        // ==========================================
        
        function handleFileSelect(file) {
            selectedFile = file;
            
            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size exceeds 10MB limit. Please choose a smaller file.');
                clearFileSelection();
                return;
            }
            
            // Update UI
            uploadTextContainer.classList.add('hidden');
            fileInfo.classList.remove('hidden');
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            
            // Update icon
            uploadIcon.className = 'ti ti-file-check text-4xl text-green-600';
            uploadIconContainer.className = 'w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 transition-all duration-300';
            
            // Enable submit button
            submitBtn.disabled = false;
            
            // Update drop zone style
            dropZone.classList.add('border-green-400', 'bg-green-50/30');
        }
        
        function clearFileSelection() {
            selectedFile = null;
            fileInput.value = '';
            
            // Reset UI
            uploadTextContainer.classList.remove('hidden');
            fileInfo.classList.add('hidden');
            
            // Reset icon
            uploadIcon.className = 'ti ti-cloud-upload text-4xl text-jlibrary-600';
            uploadIconContainer.className = 'w-20 h-20 bg-jlibrary-100 rounded-full flex items-center justify-center mx-auto mb-4 transition-all duration-300';
            
            // Disable submit button
            submitBtn.disabled = true;
            
            // Reset drop zone style
            dropZone.classList.remove('border-green-400', 'bg-green-50/30');
        }
        
        // ==========================================
        // FORM SUBMISSION WITH PROGRESS
        // ==========================================
        
        uploadForm.addEventListener('submit', function(e) {
            if (!selectedFile) {
                e.preventDefault();
                alert('Please select a file to upload.');
                return;
            }
            
            // Show progress bar
            uploadProgress.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ti ti-loader-2 animate-spin"></i> Uploading...';
            
            // Simulate progress (actual progress would come from server)
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 95) {
                    progress = 95;
                    clearInterval(interval);
                }
                updateProgress(progress);
            }, 200);
            
            // Real progress will be updated by the actual upload
            // The form will submit normally
        });
        
        function updateProgress(percent) {
            const rounded = Math.round(percent);
            progressBar.style.width = rounded + '%';
            progressPercentage.textContent = rounded + '%';
        }
        
        // ==========================================
        // UTILITY FUNCTIONS
        // ==========================================
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // ==========================================
        // DRAG OVERLAY EFFECTS
        // ==========================================
        
        // Prevent default drag behaviors on page
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            document.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });
        
        // Highlight drop zone when dragging over page
        document.addEventListener('dragover', function(e) {
            if (!dropZone.contains(e.target)) {
                dropZone.classList.add('border-jlibrary-500', 'bg-jlibrary-50');
            }
        });
        
        document.addEventListener('dragleave', function(e) {
            if (!dropZone.contains(e.target)) {
                dropZone.classList.remove('border-jlibrary-500', 'bg-jlibrary-50');
            }
        });
    });
</script>

<style>
    #drop-zone {
        transition: all 0.3s ease;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    #drop-zone.dragover {
        border-color: #6366f1;
        background-color: rgba(99, 102, 241, 0.05);
        transform: scale(1.02);
    }
    
    #drop-zone input[type="file"] {
        cursor: pointer;
    }
    
    #upload-icon-container {
        transition: all 0.3s ease;
    }
    
    #upload-progress {
        transition: all 0.3s ease;
    }
    
    #progress-bar {
        transition: width 0.3s ease;
    }
    
    /* File input styling */
    input[type="file"] {
        cursor: pointer;
    }
    
    /* Hover effects */
    #drop-zone:hover {
        border-color: #6366f1;
        background-color: rgba(99, 102, 241, 0.03);
    }
    
    #drop-zone:hover #upload-icon-container {
        transform: scale(1.05);
        background-color: rgba(99, 102, 241, 0.15);
    }
</style>
@endsection