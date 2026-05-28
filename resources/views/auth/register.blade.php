<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JLIBRARY - Register</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-900 via-purple-700 to-pink-800 min-h-screen">

    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="max-w-md w-full">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-2xl mb-4 backdrop-blur-sm">
                    <i class="ti ti-book text-4xl text-white"></i>
                </div>
                <h1 class="text-4xl font-bold text-white">JLIBRARY</h1>
                <p class="text-purple-200 mt-1">Join our learning community</p>
            </div>
            
            <!-- Register Card -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl border border-white/20">
                <h2 class="text-2xl font-semibold text-white text-center mb-6">Create Account</h2>
                
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-2">Full Name</label>
                        <div class="relative">
                            <i class="ti ti-user absolute left-3 top-1/2 -translate-y-1/2 text-purple-300"></i>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full pl-10 pr-3 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-400"
                                   >
                        </div>
                        @error('name')
                            <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-2">Email Address</label>
                        <div class="relative">
                            <i class="ti ti-mail absolute left-3 top-1/2 -translate-y-1/2 text-purple-300"></i>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full pl-10 pr-3 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-400"
                                   >
                        </div>
                        @error('email')
                            <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                  <div class="mb-4">
    <label class="block text-sm font-medium text-white mb-2">Password</label>
    <div class="relative">
        <i class="ti ti-lock absolute left-3 top-1/2 -translate-y-1/2 text-purple-300"></i>
        <input type="password" name="password" id="password" required
               class="w-full pl-10 pr-10 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-400">
        <button type="button" onclick="togglePassword('password', 'eye-password')"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-purple-300 hover:text-white transition">
            <i id="eye-password" class="ti ti-eye"></i>
        </button>
    </div>
    @error('password')
        <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-6">
    <label class="block text-sm font-medium text-white mb-2">Confirm Password</label>
    <div class="relative">
        <i class="ti ti-check absolute left-3 top-1/2 -translate-y-1/2 text-purple-300"></i>
        <input type="password" name="password_confirmation" id="password_confirmation" required
               class="w-full pl-10 pr-10 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-400">
        <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-purple-300 hover:text-white transition">
            <i id="eye-confirm" class="ti ti-eye"></i>
        </button>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('ti-eye');
        icon.classList.add('ti-eye-off');
    } else {
        input.type = 'password';
        icon.classList.remove('ti-eye-off');
        icon.classList.add('ti-eye');
    }
}
</script>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2">
                        <i class="ti ti-user-plus"></i>
                        Create Account
                    </button>
                </form>
            </div>
            
            <!-- Login Link -->
            <div class="text-center mt-6">
                <p class="text-purple-200">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-white font-semibold hover:underline">Sign in</a>
                </p>
            </div>
        </div>
    </div>
    
</body>
</html>