<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - E-Shop Admin</title>
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
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2">
                <i class="bi bi-bag-check text-indigo-500 text-4xl"></i>
                <span class="text-2xl font-bold text-white">E-Shop Admin</span>
            </a>
            <p class="text-slate-400 mt-2">Sign in to manage your store</p>
        </div>

        <div class="bg-slate-800 rounded-xl border border-slate-700 p-8">
            @if(session('error'))
                <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 flex items-center gap-2">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <i class="bi bi-exclamation-circle"></i>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full pl-10 pr-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="admin@example.com"
                            required
                        >
                    </div>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full pl-10 pr-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Enter your password"
                            required
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-900 border-slate-600 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-slate-800">
                        <span class="text-sm text-slate-300">Remember me</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign In
                </button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-sm text-slate-400 hover:text-indigo-400 transition-colors">
                <i class="bi bi-arrow-left"></i> Back to Store
            </a>
        </div>
    </div>
</body>
</html>