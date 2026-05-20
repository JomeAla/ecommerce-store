<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Admin Dashboard') - E-Shop Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: {
                            850: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .sidebar-active {
            background-color: rgba(99, 102, 241, 0.1);
            border-left: 3px solid #6366f1;
            color: #818cf8;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay {
                display: none;
            }
            .sidebar-overlay.open {
                display: block;
            }
        }
    </style>
    @yield('styles')
</head>
<body class="bg-slate-900 text-slate-200">
    <div class="flex h-screen overflow-hidden">
        <div class="sidebar-overlay fixed inset-0 bg-black/50 z-40 lg:hidden" id="sidebarOverlay"></div>

        <aside class="sidebar fixed lg:static inset-y-0 left-0 z-50 w-64 bg-slate-800 border-r border-slate-700 flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300" id="sidebar">
            <div class="h-16 flex items-center justify-center border-b border-slate-700 bg-slate-850">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <i class="bi bi-bag-check text-indigo-500 text-2xl"></i>
                    <span class="text-xl font-bold text-white">E-Shop Admin</span>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1 px-3">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : '' }}">
                            <i class="bi bi-grid-1x2 text-lg"></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.products.*') ? 'sidebar-active' : '' }}">
                            <i class="bi bi-box-seam text-lg"></i>
                            <span class="font-medium">Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.orders.*') ? 'sidebar-active' : '' }}">
                            <i class="bi bi-receipt text-lg"></i>
                            <span class="font-medium">Orders</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.settings*') ? 'sidebar-active' : '' }}">
                            <i class="bi bi-gear text-lg"></i>
                            <span class="font-medium">Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.shipping.zones') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.shipping.*') ? 'sidebar-active' : '' }}">
                            <i class="bi bi-truck text-lg"></i>
                            <span class="font-medium">Shipping</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="border-t border-slate-700 p-4">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full rounded-lg text-slate-300 hover:bg-red-500/10 hover:text-red-400 transition-colors">
                        <i class="bi bi-box-arrow-left text-lg"></i>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-slate-800 border-b border-slate-700 flex items-center justify-between px-4 lg:px-6">
                <div class="flex items-center gap-4">
                    <button class="lg:hidden p-2 rounded-lg hover:bg-slate-700 text-slate-300" id="mobileMenuToggle">
                        <i class="bi bi-list text-xl"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-white">@yield('page-title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-2 text-slate-300">
                        <i class="bi bi-person-circle text-xl"></i>
                        <span class="font-medium">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 lg:p-6 bg-slate-900">
                @if(session('success'))
                    <div class="mb-4 p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 flex items-center gap-2" id="successAlert">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 flex items-center gap-2" id="errorAlert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-4 p-4 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center gap-2" id="infoAlert">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.getElementById('mobileMenuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        });

        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
        });

        setTimeout(function() {
            const successAlert = document.getElementById('successAlert');
            if(successAlert) successAlert.style.display = 'none';
        }, 5000);

        setTimeout(function() {
            const errorAlert = document.getElementById('errorAlert');
            if(errorAlert) errorAlert.style.display = 'none';
        }, 5000);

        setTimeout(function() {
            const infoAlert = document.getElementById('infoAlert');
            if(infoAlert) infoAlert.style.display = 'none';
        }, 5000);
    </script>

    @yield('scripts')
</body>
</html>