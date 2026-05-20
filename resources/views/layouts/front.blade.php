<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Your trusted Nigerian e-commerce store for quality products')">
    <meta name="keywords" content="@yield('meta_keywords', 'ecommerce, nigeria, online shopping, digital products')">
    <title>@yield('title', config('app.name', 'Online Store'))</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #1f2937 0%, #374151 50%, #1f2937 100%);
        }
        @media (max-width: 768px) {
            .mobile-menu { display: none; }
            .mobile-menu.active { display: block; }
        }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-800">
                        <i class="bi bi-shop text-amber-600"></i> Store
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('shop') }}" class="text-gray-700 hover:text-amber-600 font-medium transition">Shop</a>
                    <a href="{{ route('cart') }}" class="text-gray-700 hover:text-amber-600 font-medium transition relative">
                        <i class="bi bi-cart3 text-lg"></i>
                        Cart
                        @php
                            $cartCount = \App\Models\Cart::where('session_id', session('cart_session_id'))->sum('quantity');
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -right-2 bg-amber-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    @if(session()->has('admin_id'))
                        <div class="flex items-center space-x-3">
                            <span class="text-gray-600 text-sm font-medium">{{ session('admin_name') }}</span>
                            <a href="{{ route('admin.dashboard') }}" class="text-amber-600 hover:text-amber-700 text-sm font-semibold">Dashboard</a>
                        </div>
                    @else
                        <a href="{{ route('admin.login') }}" class="text-gray-700 hover:text-amber-600 font-medium transition">
                            <i class="bi bi-person"></i> Login
                        </a>
                    @endif
                    
                    <button id="mobile-menu-btn" class="md:hidden text-gray-700 hover:text-amber-600">
                        <i class="bi bi-list text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div id="mobile-menu" class="mobile-menu md:hidden bg-white border-t">
            <div class="px-4 py-3 space-y-3">
                <a href="{{ route('shop') }}" class="block text-gray-700 hover:text-amber-600 font-medium py-2">
                    <i class="bi bi-grid me-2"></i> Shop
                </a>
                <a href="{{ route('cart') }}" class="block text-gray-700 hover:text-amber-600 font-medium py-2 relative">
                    <i class="bi bi-cart3 me-2"></i> Cart
                    @if($cartCount > 0)
                        <span class="ml-2 bg-amber-600 text-white text-xs px-2 py-0.5 rounded-full">{{ $cartCount }}</span>
                    @endif
                </a>
                @if(session()->has('admin_id'))
                    <a href="{{ route('admin.dashboard') }}" class="block text-gray-700 hover:text-amber-600 font-medium py-2">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('admin.login') }}" class="block text-gray-700 hover:text-amber-600 font-medium py-2">
                        <i class="bi bi-person me-2"></i> Login
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop') }}" class="text-gray-400 hover:text-amber-400 transition">Shop</a></li>
                        <li><a href="{{ route('cart') }}" class="text-gray-400 hover:text-amber-400 transition">Cart</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-amber-400 transition">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-amber-400 transition">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-amber-400 transition">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="bi bi-envelope me-2"></i> support@example.com</li>
                        <li><i class="bi bi-telephone me-2"></i> +234 800 123 4567</li>
                        <li><i class="bi bi-geo-alt me-2"></i> Lagos, Nigeria</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Online Store') }}. All rights reserved.
                </p>
                @php
                    $footerCartCount = \App\Models\Cart::where('session_id', session()->getId())->sum('quantity');
                @endphp
                @if($footerCartCount > 0)
                    <div class="mt-4 md:mt-0 flex items-center text-gray-400 text-sm">
                        <i class="bi bi-cart3 me-2"></i>
                        <span>{{ $footerCartCount }} item(s) in cart</span>
                    </div>
                @endif
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('active');
        });
    </script>
    
    @yield('scripts')
</body>
</html>