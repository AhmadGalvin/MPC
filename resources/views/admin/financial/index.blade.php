@extends('layouts.admin')

@section('header', 'Financial Management')

@section('content')
    <!-- Revenue Overview -->
    <div class="card">
        <div class="card-title">Revenue Overview</div>
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="flex: 1; padding: 1.5rem; background-color: #e8f5e9; border-radius: 0.5rem;">
                <h3 style="font-size: 1.25rem; color: #2e7d32; margin: 0;">Total Revenue</h3>
                <p style="font-size: 2rem; font-weight: bold; color: #1b5e20; margin: 0.5rem 0 0;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>


    <!-- Payment Lists -->
    <div class="card" style="margin-top: 1.5rem;">
        <div class="card-title">Pending Payments</div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Doctor</th>
                        <th>Pet Owner</th>
                        <th>Pet Name</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPayments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('d M Y') }}</td>
                            <td>Dr. {{ $payment->doctor->user->name }}</td>
                            <td>{{ $payment->pet->owner->name }}</td>
                            <td>{{ $payment->pet->name }}</td>
                            <td>Rp {{ number_format($payment->fee, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge" style="
                                    padding: 0.25rem 0.5rem;
                                    border-radius: 9999px;
                                    font-size: 0.75rem;
                                    background-color: #fff3e0;
                                    color: #e65100;">
                                    Pending
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #6b7280;">No pending payments</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" style="margin-top: 1.5rem;">
        <div class="card-title">Paid Payments</div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Doctor</th>
                        <th>Pet Owner</th>
                        <th>Pet Name</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paidPayments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('d M Y') }}</td>
                            <td>Dr. {{ $payment->doctor->user->name }}</td>
                            <td>{{ $payment->pet->owner->name }}</td>
                            <td>{{ $payment->pet->name }}</td>
                            <td>Rp {{ number_format($payment->fee, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge" style="
                                    padding: 0.25rem 0.5rem;
                                    border-radius: 9999px;
                                    font-size: 0.75rem;
                                    background-color: #e8f5e9;
                                    color: #1b5e20;">
                                    Paid
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #6b7280;">No paid payments</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        const monthlyData = @json($monthlyRevenue);
        const labels = monthlyData.map(data => {
            const date = new Date(data.year, data.month - 1);
            return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
        }).reverse();
        const values = monthlyData.map(data => data.total).reverse();

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Revenue',
                    data: values,
                    borderColor: '#2e7d32',
                    backgroundColor: '#e8f5e9',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection 