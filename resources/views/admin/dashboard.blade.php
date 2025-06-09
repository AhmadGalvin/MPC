@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
    <!-- Quick Action Buttons -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.doctors.index') }}" class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-user-md text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Manage Doctors</h3>
                        <p class="text-sm text-gray-600">Add or edit doctors</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.schedules.index') }}" class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-calendar-alt text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Manage Schedules</h3>
                        <p class="text-sm text-gray-600">Set doctor schedules</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.products.index') }}" class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-box text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Manage Products</h3>
                        <p class="text-sm text-gray-600">Update inventory</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.medical-records.index') }}" class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-notes-medical text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Medical Records</h3>
                        <p class="text-sm text-gray-600">View patient history</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-4">Statistics Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 mr-4">
                        <i class="fas fa-paw text-blue-500 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-sm">Total Pets</h3>
                        <p class="text-2xl font-semibold" id="totalPets">0</p>
                        <a href="{{ route('admin.medical-records.index') }}" class="text-blue-600 text-sm hover:text-blue-800">
                            View Records →
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <i class="fas fa-user-md text-green-500 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-sm">Active Doctors</h3>
                        <p class="text-2xl font-semibold" id="activeDoctors">0</p>
                        <a href="{{ route('admin.doctors.index') }}" class="text-green-600 text-sm hover:text-green-800">
                            Manage Doctors →
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 mr-4">
                        <i class="fas fa-clipboard-check text-purple-500 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-sm">Completed Consultations</h3>
                        <p class="text-2xl font-semibold" id="completedConsultations">0</p>
                        <a href="{{ route('admin.consultations.index') }}" class="text-purple-600 text-sm hover:text-purple-800">
                            View All →
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 mr-4">
                        <i class="fas fa-box text-yellow-500 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-sm">Total Products</h3>
                        <p class="text-2xl font-semibold" id="totalProducts">0</p>
                        <a href="{{ route('admin.products.index') }}" class="text-yellow-600 text-sm hover:text-yellow-800">
                            Manage Stock →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Product Stock Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Product Stock Levels</h2>
                <a href="{{ route('admin.products.index') }}" class="text-primary hover:text-primary-dark">
                    View All Products →
                </a>
            </div>
            <canvas id="productStockChart" height="300"></canvas>
        </div>

        <!-- Low Stock Alerts -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Low Stock Alerts</h2>
                <button id="refreshStockAlerts" class="text-primary hover:text-primary-dark">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            <div id="lowStockList" class="space-y-4">
                <!-- Low stock items will be populated here -->
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="mt-8">
        <h2 class="text-lg font-semibold mb-4">Recent Activities</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6" id="recentActivities">
                <div class="animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-2/3 mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function loadDashboardData() {
        $.get('/api/admin/dashboard', function(response) {
            if (response.status === 'success') {
                const data = response.data;
                
                // Update statistics with animation
                animateNumber('#totalPets', data.stats.total_pets);
                animateNumber('#activeDoctors', data.stats.active_doctors);
                animateNumber('#completedConsultations', data.stats.completed_consultations);
                animateNumber('#totalProducts', data.stats.total_products);

                // Initialize product stock chart
                const products = data.products || [];
                new Chart(document.getElementById('productStockChart'), {
                    type: 'bar',
                    data: {
                        labels: products.map(p => p.name),
                        datasets: [{
                            label: 'Current Stock',
                            data: products.map(p => p.stock),
                            backgroundColor: 'rgba(79, 70, 229, 0.2)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                // Populate low stock alerts
                const lowStockProducts = products.filter(p => p.stock < 10);
                const lowStockHtml = lowStockProducts.map(product => `
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                        <div>
                            <h3 class="font-semibold text-red-700">${product.name}</h3>
                            <p class="text-sm text-red-600">Current Stock: ${product.stock}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="/admin/products/${product.id}" class="text-red-700 hover:text-red-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="restockProduct(${product.id})" class="text-green-700 hover:text-green-800">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
                
                $('#lowStockList').html(lowStockHtml || '<p class="text-gray-500">No products with low stock.</p>');
            }
        });
    }

    function animateNumber(selector, end) {
        const start = parseInt($(selector).text()) || 0;
        $({number: start}).animate({number: end}, {
            duration: 1000,
            easing: 'swing',
            step: function() {
                $(selector).text(Math.floor(this.number));
            },
            complete: function() {
                $(selector).text(end);
            }
        });
    }

    // Initial load
    loadDashboardData();

    // Refresh button handler
    $('#refreshStockAlerts').click(function() {
        $(this).addClass('animate-spin');
        loadDashboardData();
        setTimeout(() => $(this).removeClass('animate-spin'), 1000);
    });

    // Restock function
    window.restockProduct = function(productId) {
        // Implement restock functionality
        alert('Restock functionality will be implemented here');
    };

    // Load recent activities
    $.get('/api/admin/activities', function(response) {
        if (response.status === 'success') {
            const activities = response.data.map(activity => `
                <div class="border-l-4 border-primary pl-4 py-2 mb-4">
                    <p class="font-semibold">${activity.description}</p>
                    <p class="text-sm text-gray-500">${new Date(activity.created_at).toLocaleDateString()}</p>
                </div>
            `).join('');
            
            $('#recentActivities').html(activities || '<p class="text-gray-500">No recent activities.</p>');
        }
    });
});
</script>
@endpush 