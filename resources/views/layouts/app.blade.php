<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tamdan Luy - Personal Finance Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10b981', // Emerald 500
                        secondary: '#3b82f6', // Blue 500
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-y-scroll">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-primary flex items-center gap-2">
                            <i class="fas fa-wallet"></i> <span class="hidden sm:inline">Tamdan Luy</span>
                        </a>
                    </div>
                    <!-- Desktop Navigation Links -->
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="border-b-2 {{ request()->routeIs('dashboard') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 text-sm font-medium">Dashboard</a>
                        <a href="{{ route('accounts.index') }}" class="border-b-2 {{ request()->routeIs('accounts.*') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 text-sm font-medium">Accounts</a>
                        <a href="{{ route('categories.index') }}" class="border-b-2 {{ request()->routeIs('categories.*') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 text-sm font-medium">Categories</a>
                        <a href="{{ route('budgets.index') }}" class="border-b-2 {{ request()->routeIs('budgets.*') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 text-sm font-medium">Budgets</a>
                        <a href="{{ route('transactions.index') }}" class="border-b-2 {{ request()->routeIs('transactions.*') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 text-sm font-medium">Transactions</a>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    @auth
                    @if(isset($globalAccounts) && $globalAccounts->count() > 0)
                    <form action="{{ route('accounts.switch') }}" method="POST" class="hidden sm:block">
                        @csrf
                        <select name="account_id" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 pl-3 pr-8 w-40 text-gray-700 bg-gray-50 outline-none">
                            @foreach($globalAccounts as $account)
                                <option value="{{ $account->id }}" {{ $activeAccountId == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    @endif
                    @endauth

                    <a href="{{ route('transactions.create') }}" class="hidden sm:inline-flex bg-primary text-white px-4 py-2 rounded-md shadow hover:bg-emerald-600 transition font-medium text-sm">
                        <i class="fas fa-plus mr-1"></i> New Transaction
                    </a>

                    @auth
                    <!-- Profile dropdown -->
                    <div class="relative ml-2" x-data="{ userMenuOpen: false }">
                        <div>
                            <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false" type="button" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary items-center" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                @if(auth()->user()->avatar)
                                    <img class="h-8 w-8 rounded-full object-cover border border-gray-200" src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=10b981&background=d1fae5';">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center text-primary font-bold border border-emerald-200">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                            </button>
                        </div>
                        <div x-show="userMenuOpen" x-transition x-cloak class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            <div class="px-4 py-2 text-sm text-gray-700 border-b border-gray-100">
                                <div class="font-medium text-gray-900">{{ auth()->user()->name }}</div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">Sign out</button>
                            </form>
                        </div>
                    </div>
                    @endauth
                    
                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <i class="fas fa-bars text-xl" x-show="!mobileMenuOpen"></i>
                            <i class="fas fa-times text-xl" x-show="mobileMenuOpen" x-cloak></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="sm:hidden border-t border-gray-200" id="mobile-menu" x-show="mobileMenuOpen" x-collapse x-cloak>
            <div class="pt-2 pb-3 space-y-1">
                @auth
                @if(isset($globalAccounts) && $globalAccounts->count() > 0)
                <div class="px-4 py-2">
                    <form action="{{ route('accounts.switch') }}" method="POST">
                        @csrf
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Active Account</label>
                        <select name="account_id" onchange="this.form.submit()" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2 outline-none">
                            @foreach($globalAccounts as $account)
                                <option value="{{ $account->id }}" {{ $activeAccountId == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} ({{ $account->currency }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @endif
                @endauth
                
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'text-primary bg-green-50 border-l-4 border-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">Dashboard</a>
                <a href="{{ route('accounts.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('accounts.*') ? 'text-primary bg-green-50 border-l-4 border-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">Accounts</a>
                <a href="{{ route('categories.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('categories.*') ? 'text-primary bg-green-50 border-l-4 border-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">Categories</a>
                <a href="{{ route('budgets.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('budgets.*') ? 'text-primary bg-green-50 border-l-4 border-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">Budgets</a>
                <a href="{{ route('transactions.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('transactions.*') ? 'text-primary bg-green-50 border-l-4 border-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">Transactions</a>
                <a href="{{ route('transactions.create') }}" class="block px-4 py-2 text-base font-medium text-emerald-600 hover:bg-emerald-50"><i class="fas fa-plus mr-2"></i>New Transaction</a>
                
                @auth
                <div class="border-t border-gray-200 mt-2 pt-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-600 hover:text-red-600 hover:bg-gray-50">
                            <i class="fas fa-sign-out-alt mr-2"></i>Sign out
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>