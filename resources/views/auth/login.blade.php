<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tamdan Luy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10b981', // Emerald 500
                    },
                    boxShadow: {
                        '2xs': '0 1px 2px 0 rgba(0, 0, 0, 0.02)',
                        '3xs': '0 1px 1px 0 rgba(0, 0, 0, 0.015)',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50/50 flex flex-col justify-center items-center min-h-screen p-4 antialiased">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-10 text-center relative overflow-hidden">
        
        <!-- Top Accenting Design Strip Matrix -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-400 to-primary"></div>

        <!-- System Branding Node Layer -->
        <div class="w-16 h-16 bg-emerald-50 text-primary rounded-2xl flex items-center justify-center mx-auto mb-6 border border-emerald-100/50 shadow-3xs">
            <i class="fas fa-wallet text-2xl"></i>
        </div>
        
        <!-- Header Text Block -->
        <div class="mb-8">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Welcome Back</h1>
            <p class="text-sm text-gray-500 mt-1.5 max-w-xs mx-auto">Sign in to Tamdan Luy to manage your localized financial ledger channels.</p>
        </div>

        <!-- Error Handling Warning Deck -->
        @if(session('error'))
            <div class="mb-6 bg-red-50/60 border border-red-100 p-4 rounded-xl shadow-3xs text-left flex items-start gap-3">
                <div class="text-red-500 mt-0.5 flex-none">
                    <i class="fa-solid fa-circle-exclamation text-sm"></i>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-red-600">Authentication Failure</span>
                    <p class="text-xs text-red-700 font-semibold leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Core Authentication Single-Sign-On Node -->
        <div class="space-y-4">
            <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest text-left mb-2">Identify Terminal Access</span>
            
            <a href="{{ route('auth.google') }}" class="flex items-center justify-center w-full bg-white border border-gray-200 rounded-xl px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50/80 hover:border-gray-300 hover:shadow-sm active:scale-[0.99] transition-all duration-150 gap-3 group">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5 group-hover:scale-105 transition-transform">
                Continue with Google
            </a>
        </div>

        <!-- Interactive Terms and Service Layout Footer -->
        <div class="mt-10 pt-6 border-t border-gray-50 text-center">
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider leading-loose">
                By continuing, you agree to our 
                <a href="#" class="text-primary hover:underline font-bold">Terms of Service</a> 
                <br class="hidden sm:inline">
                and 
                <a href="#" class="text-primary hover:underline font-bold">Privacy Policy</a>.
            </p>
        </div>
    </div>
</body>
</html>