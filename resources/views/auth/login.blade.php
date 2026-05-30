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
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 flex flex-col justify-center items-center min-h-screen p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden pt-8 pb-10 px-8 text-center border border-gray-100">
        <!-- Logo/Icon -->
        <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
            <i class="fas fa-wallet text-4xl text-primary"></i>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h1>
        <p class="text-gray-500 mb-10">Sign in to Tamdan Luy to continue tracking your personal finances.</p>

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm text-left">
                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <a href="{{ route('auth.google') }}" class="flex items-center justify-center w-full bg-white border border-gray-300 rounded-xl px-6 py-3 text-gray-700 font-medium hover:bg-gray-50 hover:border-gray-400 transition-colors shadow-sm gap-3">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-6 h-6">
            Continue with Google
        </a>

        <div class="mt-12 text-sm text-gray-400">
            <p>By continuing, you agree to our Terms of Service</p>
            <p>and Privacy Policy.</p>
        </div>
    </div>
</body>
</html>