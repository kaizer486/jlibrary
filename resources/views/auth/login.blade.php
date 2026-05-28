<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>JLIBRARY - Login</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* Light Orange Gradient for Email & Lock Icons */
        .gradient-icon {
            background: linear-gradient(135deg, #fb923c, #f97316, #ea580c);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: bold;
            text-shadow: 0 0 20px rgba(251, 146, 60, 0.5);
        }
        
        /* Glowing Effect */
        .glow-icon {
            filter: drop-shadow(0 0 10px rgba(251, 146, 60, 0.7));
            transition: filter 0.3s ease;
        }
        
        .glow-icon:hover {
            filter: drop-shadow(0 0 18px rgba(251, 146, 60, 1));
        }
        
        /* Light Orange Gradient for Eye Icon */
        .eye-gradient {
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #ea580c);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: bold;
        }
        
        .glow-eye {
            filter: drop-shadow(0 0 10px rgba(251, 146, 60, 0.7));
            transition: filter 0.3s ease;
        }
        
        .glow-eye:hover {
            filter: drop-shadow(0 0 18px rgba(251, 146, 60, 1));
        }
        
        /* Input focus glow - matching orange */
        .glow-input:focus {
            box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.4), 0 0 0 1px rgba(251, 146, 60, 0.6);
            border-color: transparent;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-900 via-purple-700 to-pink-800 min-h-screen">

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-2xl mb-4 backdrop-blur-sm">
                    <i class="ti ti-book text-4xl text-white"></i>
                </div>
                <h1 class="text-4xl font-bold text-white">JLIBRARY</h1>
                <p class="text-purple-200 mt-1">Learn. Share. Grow Together.</p>
            </div>
            
            <!-- Login Card -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl border border-white/20">
                <h2 class="text-2xl font-semibold text-white text-center mb-6">Welcome Back</h2>
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-2">Email Address</label>
                        <div class="relative">
                            <i class="ti ti-mail absolute left-3 top-1/2 -translate-y-1/2 text-2xl gradient-icon glow-icon"></i>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full pl-12 pr-3 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none glow-input"
                                   autocomplete="email">
                        </div>
                        @error('email')
                            <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Password Field -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-2">Password</label>
                        <div class="relative">
                            <i class="ti ti-lock absolute left-3 top-1/2 -translate-y-1/2 text-2xl gradient-icon glow-icon"></i>
                            <input type="password" id="password" name="password" required
                                   class="w-full pl-12 pr-12 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none glow-input"
                                   autocomplete="current-password">
                            <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 transition cursor-pointer">
                                <i id="passwordIcon" class="ti ti-eye text-xl eye-gradient glow-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-white/20 bg-white/10 text-orange-500">
                            <span class="ml-2 text-sm text-purple-200">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-purple-300 hover:text-white transition">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                        <i class="ti ti-login"></i>
                        Sign In
                    </button>
                </form>
                
                <!-- Social Login -->
                <div class="text-center my-6">
                    <span class="text-purple-200 text-sm">Or continue with</span>
                </div>

                <!-- Social Login Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Google Button -->
                    <a href="/auth/google" 
                       class="flex items-center justify-center gap-2 px-4 py-2 bg-white/10 border border-white/20 rounded-xl hover:bg-white/20 transition group">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span class="text-sm font-medium text-white">Google</span>
                    </a>

                    <!-- GitHub Button -->
                    <a href="/auth/github" 
                       class="flex items-center justify-center gap-2 px-4 py-2 bg-white/10 border border-white/20 rounded-xl hover:bg-white/20 transition group">
                        <svg class="w-5 h-5" fill="white" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12c0 4.42 2.87 8.17 6.84 9.49.5.09.68-.21.68-.48 0-.24-.01-.88-.01-1.72-2.78.6-3.37-1.34-3.37-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.61.07-.61 1 .07 1.53 1.03 1.53 1.03.89 1.52 2.34 1.08 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.94 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.64 0 0 .84-.27 2.75 1.02.8-.22 1.65-.33 2.5-.33.85 0 1.7.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.37.2 2.39.1 2.64.64.7 1.03 1.59 1.03 2.68 0 3.84-2.34 4.69-4.57 4.94.36.31.68.92.68 1.85 0 1.34-.01 2.42-.01 2.75 0 .27.18.58.69.48C19.13 20.17 22 16.42 22 12c0-5.52-4.48-10-10-10z"/>
                        </svg>
                        <span class="text-sm font-medium text-white">GitHub</span>
                    </a>
                </div>
                
               
            </div>
            
            <!-- Sign Up Link -->
            <div class="text-center mt-6">
                <p class="text-purple-200">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-white font-semibold hover:underline">Sign up now</a>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Password Toggle Script -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                if (type === 'password') {
                    passwordIcon.classList.remove('ti-eye-off');
                    passwordIcon.classList.add('ti-eye');
                } else {
                    passwordIcon.classList.remove('ti-eye');
                    passwordIcon.classList.add('ti-eye-off');
                }
            });
        }
    </script>
    
</body>
</html>