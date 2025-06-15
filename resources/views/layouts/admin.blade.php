<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Dashboard</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Styles -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 250px;
            background-color: #ffffff;
            height: 100vh;
            position: fixed;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .sidebar-header h1 {
            color: #1a56db;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .sidebar-header p {
            color: #6b7280;
            font-size: 0.875rem;
            margin: 0.5rem 0 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background-color: #1a56db;
            color: #ffffff;
        }

        .nav-link.active {
            background-color: #1a56db;
            color: #ffffff;
        }

        .nav-link i {
            margin-right: 0.75rem;
            width: 1.25rem;
        }

        .main-content {
            margin-left: 250px;
            padding: 1.5rem;
        }

        .top-bar {
            background-color: #ffffff;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #1a56db;
            color: #ffffff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1e40af;
        }

        .card {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
            margin: 0 0 1rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #def7ec;
            color: #03543f;
            border: 1px solid #0f766e;
        }

        .alert-error {
            background-color: #fde8e8;
            color: #9b1c1c;
            border: 1px solid #f05252;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background-color: #ffffff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }

        .stat-card-title {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .stat-card-value {
            color: #111827;
            font-size: 1.5rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>MedipetCare</h1>
            <p>Admin Dashboard</p>
        </div>
        <nav>
            <a href="{{ route('admin.dashboard') }}" 
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                Dashboard
            </a>
            <a href="{{ route('admin.doctors.index') }}" 
               class="nav-link {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
                <i class="fas fa-user-md"></i>
                Doctors
            </a>
            <a href="{{ route('admin.owners.index') }}" 
               class="nav-link {{ request()->routeIs('admin.owners.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                Pet Owners
            </a>
            <a href="{{ route('admin.medical-records.index') }}" 
               class="nav-link {{ request()->routeIs('admin.medical-records.*') ? 'active' : '' }}">
                <i class="fas fa-notes-medical"></i>
                Medical Records
            </a>
            <a href="{{ route('admin.appointments.index') }}" 
               class="nav-link {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                Appointments
            </a>
            <a href="{{ route('admin.consultations.index') }}" 
               class="nav-link {{ request()->routeIs('admin.consultations.*') ? 'active' : '' }}">
                <i class="fas fa-stethoscope"></i>
                Consultations
            </a>
            <a href="{{ route('admin.settings.index') }}" 
               class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h1 class="page-title">@yield('header', 'Dashboard')</h1>
            <div class="user-menu">
                <span>{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html> 