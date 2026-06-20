<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JLIBRARY - Forgot Password</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-purple-900 via-purple-700 to-pink-800 min-h-screen">

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <!-- Logo Section -->
            <div class="text-center mb-2">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-2xl mb-2 mt-1 backdrop-blur-sm">
                    <img src="{{ asset('images/jlibrary.jpeg') }}" alt="Logo" class="h-20 w-auto rounded-2xl">
                </div>
                <h1 class="text-3xl font-bold text-white">JLIBRARY</h1>
                <p class="text-purple-200 mt-1">Reset Your Password</p>
            </div>
            
            <!-- Reset Card -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl border border-white/20">
                <h2 class="text-2xl font-semibold text-white text-center mb-6">Forgot Password?</h2>
                
                <p class="text-purple-200 text-sm text-center mb-6">
                    Enter your email address and we'll send you a link to reset your password.
                </p>
                
                @if(session('status'))
                    <div class="bg-green-500/20 border border-green-500/50 text-green-200 p-3 mb-4 rounded-lg text-sm text-center">
                        {{ session('status') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-2">Email Address</label>
                        <div class="relative">
                            <i class="ti ti-mail absolute left-3 top-1/2 -translate-y-1/2 text-2xl text-orange-400"></i>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full pl-12 pr-3 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   autocomplete="email">
                        </div>
                        @error('email')
                            <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-lg">
                        <i class="ti ti-send"></i>
                        Send Reset Link
                    </button>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-sm text-purple-300 hover:text-white transition">
                            Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>