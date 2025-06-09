<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedipetCare Admin - @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <div class="p-4">
                <h1 class="text-2xl font-bold text-primary">MedipetCare</h1>
                <p class="text-sm text-gray-600">Admin Dashboard</p>
            </div>
            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-chart-line mr-3"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.clinic.profile') }}"
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors {{ request()->routeIs('admin.clinic.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-clinic-medical mr-3"></i>
                    Clinic Profile
                </a>
                <a href="{{ route('admin.doctors.index') }}"
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors {{ request()->routeIs('admin.doctors.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-user-md mr-3"></i>
                    Doctors
                </a>
                <a href="{{ route('admin.schedules.index') }}"
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors {{ request()->routeIs('admin.schedules.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    Schedules
                </a>
                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-primary text-white' : '' }}">
                    <i class="fas fa-box mr-3"></i>
                    Products
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