<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'JLIBRARY' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tabler-icons@1.119.0/iconfont/tabler-icons.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 py-12">
    <div class="max-w-2xl mx-auto">
        
        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            
            <!-- Header Gradient -->
            <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-700 px-8 py-10 text-center relative">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative">
                    <i class="ti ti-book text-5xl text-white/80 mb-3 block"></i>
                    <h1 class="text-3xl font-bold text-white">JLIBRARY</h1>
                    <p class="text-purple-200 text-sm mt-2">Build knowledge, one book at a time</p>
                    <h2 class="text-xl font-semibold text-white mt-4">{{ $headerTitle ?? 'Welcome!' }}</h2>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-8 py-8">
                {{ $slot }}
            </div>
            
            <!-- Footer -->
            <div class="border-t border-gray-100 px-8 py-6 bg-gray-50">
                <div class="flex justify-center space-x-6 mb-4">
                    <a href="#" class="text-gray-400 hover:text-purple-600 transition"><i class="ti ti-brand-facebook text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-purple-600 transition"><i class="ti ti-brand-twitter text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-purple-600 transition"><i class="ti ti-brand-instagram text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-purple-600 transition"><i class="ti ti-brand-linkedin text-xl"></i></a>
                </div>
                <p class="text-center text-gray-400 text-xs">
                    © {{ date('Y') }} JLIBRARY. All rights reserved.<br>
                    <a href="#" class="text-purple-500 hover:underline">Unsubscribe</a> • 
                    <a href="#" class="text-purple-500 hover:underline">Privacy Policy</a>
                </p>
            </div>
        </div>
        
    </div>
</body>
</html>