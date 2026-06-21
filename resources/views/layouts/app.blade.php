<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
                    },
                    boxShadow: {
                        '2xs': '0 1px 2px 0 rgba(0, 0, 0, 0.02)',
                        '3xs': '0 1px 1px 0 rgba(0, 0, 0, 0.015)',
                        'nav': '0 -2px 10px rgba(0, 0, 0, 0.04)',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        /* Smooth scrolling adjustments for sleek app feel */
        body { -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="bg-gray-50/50 text-gray-800 font-sans antialiased overflow-y-scroll pb-24 sm:pb-0" x-data="{ profileMenuOpen: false }">
    
    <!-- Desktop Top Navbar Matrix -->
    <nav class="bg-white border-b border-gray-100 relative z-40 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo Component Layer -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-black text-primary flex items-center gap-2 tracking-tight">
                            <i class="fas fa-wallet text-lg"></i> <span>Tamdan Luy</span>
                        </a>
                    </div>
                    <!-- Desktop Target Navigation Links -->
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-6">
                        <a href="{{ route('dashboard') }}" class="border-b-2 {{ request()->routeIs('dashboard') ? 'border-primary text-gray-900 font-bold' : 'border-transparent text-gray-400 hover:text-gray-600' }} inline-flex items-center px-1 pt-1 text-xs font-semibold uppercase tracking-wider transition-colors">Home</a>
                        <a href="{{ route('accounts.index') }}" class="border-b-2 {{ request()->routeIs('accounts.*') ? 'border-primary text-gray-900 font-bold' : 'border-transparent text-gray-400 hover:text-gray-600' }} inline-flex items-center px-1 pt-1 text-xs font-semibold uppercase tracking-wider transition-colors">Accounts</a>
                        <a href="{{ route('categories.index') }}" class="border-b-2 {{ request()->routeIs('categories.*') ? 'border-primary text-gray-900 font-bold' : 'border-transparent text-gray-400 hover:text-gray-600' }} inline-flex items-center px-1 pt-1 text-xs font-semibold uppercase tracking-wider transition-colors">Categories</a>
                        <a href="{{ route('budgets.index') }}" class="border-b-2 {{ request()->routeIs('budgets.*') ? 'border-primary text-gray-900 font-bold' : 'border-transparent text-gray-400 hover:text-gray-600' }} inline-flex items-center px-1 pt-1 text-xs font-semibold uppercase tracking-wider transition-colors">Budgets</a>
                        <a href="{{ route('transactions.index') }}" class="border-b-2 {{ request()->routeIs('transactions.*') ? 'border-primary text-gray-900 font-bold' : 'border-transparent text-gray-400 hover:text-gray-600' }} inline-flex items-center px-1 pt-1 text-xs font-semibold uppercase tracking-wider transition-colors">Transactions</a>
                        <a href="{{ route('reports.index') }}" class="border-b-2 {{ request()->routeIs('reports.*') ? 'border-primary text-gray-900 font-bold' : 'border-transparent text-gray-400 hover:text-gray-600' }} inline-flex items-center px-1 pt-1 text-xs font-semibold uppercase tracking-wider transition-colors">Reports</a>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- Global Account Dynamic Form Switcher -->
                    @auth
                    @if(isset($globalAccounts) && $globalAccounts->count() > 0)
                    <form action="{{ route('accounts.switch') }}" method="POST" class="hidden sm:block">
                        @csrf
                        <div class="relative">
                            <select name="account_id" onchange="this.form.submit()" class="text-xs font-bold uppercase tracking-wider border border-gray-200 rounded-xl shadow-3xs focus:ring-2 focus:ring-primary/20 focus:border-primary py-2.5 pl-3 pr-8 w-40 text-gray-600 bg-gray-50/50 outline-none appearance-none cursor-pointer transition-all">
                                @foreach($globalAccounts as $account)
                                    <option value="{{ $account->id }}" {{ $activeAccountId == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </form>
                    @endif
                    @endauth

                    <a href="{{ route('transactions.create') }}" class="hidden sm:inline-flex bg-primary hover:bg-emerald-600 text-white text-xs font-bold uppercase tracking-wider py-2.5 px-4 rounded-xl shadow-sm shadow-emerald-100 transition items-center gap-1.5">
                        <i class="fas fa-plus text-[10px]"></i> New Transaction
                    </a>

                    @auth
                    <!-- Desktop Specific Profile Context Dropdown Menu Structure -->
                    <div class="relative ml-1" x-data="{ userMenuOpen: false }">
                        <div>
                            <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false" type="button" class="flex text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary items-center transition-transform active:scale-95" id="user-menu-button">
                                <span class="sr-only">Open user menu</span>
                                @if(auth()->user()->avatar)
                                    <img class="h-9 w-9 rounded-xl object-cover border border-gray-200 shadow-3xs" src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=10b981&background=d1fae5';">
                                @else
                                    <div class="h-9 w-9 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-primary font-bold text-sm shadow-3xs">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                            </button>
                        </div>
                        
                        <div x-show="userMenuOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             x-cloak 
                             class="origin-top-right absolute right-0 mt-2 w-52 rounded-xl shadow-xl py-1 bg-white border border-gray-100 focus:outline-none z-50 overflow-hidden hidden sm:block">
                            <div class="px-4 py-3 bg-gray-50/50 border-b border-gray-100">
                                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Identified User</span>
                                <div class="font-bold text-sm text-gray-900 mt-0.5 truncate">{{ auth()->user()->name }}</div>
                            </div>
                            <div class="p-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-2 text-xs font-bold uppercase tracking-wider text-red-600 hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2">
                                        <i class="fas fa-sign-out-alt text-[11px]"></i> Sign Out Account
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Native Continuous Bottom Navigation Deck -->
    <div class="sm:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-gray-100 z-40 shadow-nav pb-safe">
        <div class="grid grid-cols-5 items-center h-16 text-center relative">
            
            <!-- Tab 1: Home -->
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center h-full transition-colors {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-gray-400 hover:text-gray-600' }}">
                <i class="fa-solid fa-house text-lg"></i>
                <span class="text-[10px] font-bold uppercase tracking-tight mt-1">Home</span>
            </a>

            <!-- Tab 2: Reports -->
            <a href="{{ route('reports.index') }}" class="flex flex-col items-center justify-center h-full transition-colors {{ request()->routeIs('reports.*') ? 'text-primary' : 'text-gray-400 hover:text-gray-600' }}">
                <i class="fa-solid fa-chart-pie text-lg"></i>
                <span class="text-[10px] font-bold uppercase tracking-tight mt-1">Reports</span>
            </a>

            <!-- Tab 3: Central Elevated FAB Action Entry Key -->
            <div class="flex items-center justify-center h-full -mt-5 relative z-50">
                <a href="{{ route('transactions.create') }}" class="w-13 h-13 rounded-full bg-primary hover:bg-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-200/80 transform active:scale-95 transition-all border-4 border-white">
                    <i class="fa-solid fa-plus text-xl"></i>
                </a>
            </div>

            <!-- Tab 4: Budgets Matrix Node -->
            <a href="{{ route('budgets.index') }}" class="flex flex-col items-center justify-center h-full transition-colors {{ request()->routeIs('budgets.*') ? 'text-primary' : 'text-gray-400 hover:text-gray-600' }}">
                <i class="fa-solid fa-bullseye text-lg"></i>
                <span class="text-[10px] font-bold uppercase tracking-tight mt-1">Budgets</span>
            </a>

            <!-- Tab 5: Dynamic Profile Drawer Trigger Switch -->
            <button @click="profileMenuOpen = true" type="button" :class="profileMenuOpen ? 'text-primary' : 'text-gray-400'" class="flex flex-col items-center justify-center h-full transition-colors focus:outline-none hover:text-gray-600">
                <i class="fa-solid fa-user text-lg"></i>
                <span class="text-[10px] font-bold uppercase tracking-tight mt-1">Profile</span>
            </button>
            
        </div>
    </div>

    <!-- Mobile Dynamic Bottom Sheet Drawer Overlay Box for Profile Tab -->
    <div x-show="profileMenuOpen" class="sm:hidden fixed inset-0 z-50 overflow-hidden" x-cloak>
        <!-- Backdrop Blur Matte -->
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity" @click="profileMenuOpen = false" x-show="profileMenuOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        
        <!-- Sheet Body -->
        <div class="absolute inset-x-0 bottom-0 max-h-[85vh] bg-white rounded-t-2xl shadow-xl flex flex-col border-t border-gray-100 transform transition-transform" x-show="profileMenuOpen" x-transition:enter="transform ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transform ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
            <!-- Sheet Pull Indicator Notch Line -->
            <div class="w-12 h-1 bg-gray-200 rounded-full mx-auto my-3 flex-none"></div>
            
            <div class="px-5 pb-8 pt-2 overflow-y-auto space-y-6">
                <!-- User Profile Identification Cluster Block -->
                @auth
                <div class="flex items-center gap-3 p-3 bg-gray-50/70 rounded-xl border border-gray-100">
                    @if(auth()->user()->avatar)
                        <img class="h-10 w-10 rounded-xl object-cover border border-gray-200" src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}">
                    @else
                        <div class="h-10 w-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-primary font-black text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="truncate text-left">
                        <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Signed In As</span>
                        <div class="font-bold text-sm text-gray-800 truncate mt-0.5">{{ auth()->user()->name }}</div>
                    </div>
                </div>

                <!-- Custom App-Like Account Dynamic Switcher Module -->
                @if(isset($globalAccounts) && $globalAccounts->count() > 0)
                <div class="text-left mt-4">
                    <form action="{{ route('accounts.switch') }}" method="POST">
                        @csrf
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2">Active Account</label>
                        
                        <!-- Alpine Custom Select Component -->
                        <div class="relative" x-data="{ accountSelectOpen: false }">
                            <!-- Toggle Button -->
                            <button type="button" @click="accountSelectOpen = !accountSelectOpen" class="w-full flex justify-between items-center text-xs font-bold uppercase tracking-wider border border-gray-200 rounded-xl py-3 px-4 text-gray-600 bg-gray-50 focus:outline-none transition-all shadow-3xs cursor-pointer active:scale-[0.98]">
                                <span class="truncate">
                                    @php $activeAcc = $globalAccounts->firstWhere('id', $activeAccountId); @endphp
                                    {{ $activeAcc ? $activeAcc->name . ' (' . $activeAcc->currency . ')' : 'Select Account' }}
                                </span>
                                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="accountSelectOpen ? 'rotate-180' : ''"></i>
                            </button>
                            
                            <!-- Expandable Inline Options Menu -->
                            <div x-show="accountSelectOpen" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="mt-2 bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm" 
                                 x-cloak>
                                <div class="max-h-40 overflow-y-auto">
                                    @foreach($globalAccounts as $account)
                                        <button type="submit" name="account_id" value="{{ $account->id }}" class="w-full text-left px-4 py-3 text-xs font-bold uppercase tracking-wider hover:bg-emerald-50 transition-colors border-b border-gray-50 last:border-b-0 flex items-center justify-between {{ $activeAccountId == $account->id ? 'text-primary bg-emerald-50/50' : 'text-gray-500' }}">
                                            <span>{{ $account->name }}</span>
                                            <span class="text-[9px] {{ $activeAccountId == $account->id ? 'text-primary/70' : 'text-gray-400' }}">{{ $account->currency }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @endif
                @endauth

                <!-- Management Links -->
                <div class="space-y-2 text-left border-t border-gray-100 pt-4">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1 mb-2">Management</span>
                    <a href="{{ route('accounts.index') }}" @click="profileMenuOpen = false" class="flex items-center gap-3 px-3 py-3 text-sm font-bold uppercase tracking-wider text-gray-600 hover:bg-gray-50 rounded-xl transition-colors {{ request()->routeIs('accounts.*') ? 'bg-emerald-50/40 text-primary' : '' }}">
                        <i class="fa-solid fa-building-columns text-base w-5 text-center"></i> Accounts
                    </a>
                    <a href="{{ route('categories.index') }}" @click="profileMenuOpen = false" class="flex items-center gap-3 px-3 py-3 text-sm font-bold uppercase tracking-wider text-gray-600 hover:bg-gray-50 rounded-xl transition-colors {{ request()->routeIs('categories.*') ? 'bg-emerald-50/40 text-primary' : '' }}">
                        <i class="fa-solid fa-tags text-base w-5 text-center"></i> Categories
                    </a>
                </div>

                <!-- Dynamic Functional Log Out Action Call -->
                @auth
                <div class="border-t border-gray-100 pt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-3 text-sm font-bold uppercase tracking-wider text-red-600 hover:bg-red-50 rounded-xl transition-colors flex items-center gap-3">
                            <i class="fas fa-sign-out-alt text-base w-5 text-center"></i> Sign Out
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Dynamic Content Engine -->
    <main class="max-w-7xl mx-auto pt-6 pb-20 sm:py-8 px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Flash Alert Messages Feedback Loop -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50/60 border border-emerald-100 p-4 rounded-xl shadow-3xs flex items-start gap-3">
                <div class="text-emerald-500 mt-0.5 flex-none">
                    <i class="fas fa-check-circle text-sm"></i>
                </div>
                <div class="space-y-0.5 text-left">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-600">Action Complete</span>
                    <p class="text-xs text-emerald-700 font-semibold leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>