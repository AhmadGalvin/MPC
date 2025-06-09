<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedipetCare Owner - @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .text-primary { color: #4F46E5; }
        .bg-primary { background-color: #4F46E5; }
        .hover\:bg-primary:hover { background-color: #4F46E5; }
        .border-primary { border-color: #4F46E5; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <div class="p-4">
                <h1 class="text-2xl font-bold text-primary">MedipetCare</h1>
                <p class="text-sm text-gray-600">Pet Owner Portal</p>
            </div>
            <nav class="mt-4">
                <a href="{{ route('owner.dashboard') }}" 
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors {{ request()->routeIs('owner.dashboard') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-chart-line mr-3"></i>
                    Dashboard
                </a>
                <a href="{{ route('owner.pets.index') }}"
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors {{ request()->routeIs('owner.pets.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-paw mr-3"></i>
                    My Pets
                </a>
                <a href="{{ route('owner.appointments.index') }}"
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors {{ request()->routeIs('owner.appointments.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    Appointments
                </a>
                <a href="{{ route('owner.medical-records') }}"
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors {{ request()->routeIs('owner.medical-records') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-notes-medical mr-3"></i>
                    Medical Records
                </a>
                <a href="{{ route('products.index') }}"
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors {{ request()->routeIs('products.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-box mr-3"></i>
                    Shop Products
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('header')</h2>
                    <div class="flex items-center">
                        <span class="text-gray-600 mr-4">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @stack('scripts')
</body>
</html> 