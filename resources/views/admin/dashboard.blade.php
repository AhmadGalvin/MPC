@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard Overview')

@section('content')
    <!-- Statistics Overview -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-title">Total Doctors</div>
            <div class="stat-card-value">{{ $totalDoctors }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-title">Total Pet Owners</div>
            <div class="stat-card-value">{{ $totalOwners }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-title">Total Pets</div>
            <div class="stat-card-value">{{ $totalPets }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-title">Today's Consultations</div>
            <div class="stat-card-value">{{ $todayConsultations }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Recent Activities</div>
        @if(count($recentActivities) > 0)
            <div style="max-height: 400px; overflow-y: auto;">
                @foreach($recentActivities as $activity)
                    <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                        <p style="margin: 0;">{{ $activity['description'] }}</p>
                        <p style="margin: 0.25rem 0 0; font-size: 0.875rem; color: #6b7280;">
                            {{ $activity['created_at'] }}
                            <span class="badge" style="
                                padding: 0.25rem 0.5rem;
                                border-radius: 9999px;
                                font-size: 0.75rem;
                                margin-left: 0.5rem;
                                background-color: {{ $activity['status'] === 'completed' ? '#def7ec' : '#fef3c7' }};
                                color: {{ $activity['status'] === 'completed' ? '#03543f' : '#92400e' }};">
                                {{ ucfirst($activity['status']) }}
                            </span>
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <p style="text-align: center; color: #6b7280; padding: 1rem;">No recent activities</p>
        @endif
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div class="card-title" style="margin: 0;">Latest Consultations</div>
            <a href="{{ route('admin.consultations.index') }}" class="btn btn-primary">View All</a>
        </div>
        
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Pet Owner</th>
                        <th>Pet</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestConsultations as $consultation)
                        <tr>
                            <td>Dr. {{ $consultation->doctor->user->name }}</td>
                            <td>{{ $consultation->pet->owner->name }}</td>
                            <td>{{ $consultation->pet->name }}</td>
                            <td>
                                <span class="badge" style="
                                    padding: 0.25rem 0.5rem;
                                    border-radius: 9999px;
                                    font-size: 0.75rem;
                                    background-color: {{ $consultation->status === 'completed' ? '#def7ec' : '#fef3c7' }};
                                    color: {{ $consultation->status === 'completed' ? '#03543f' : '#92400e' }};">
                                    {{ ucfirst($consultation->status) }}
                                </span>
                            </td>
                            <td>{{ $consultation->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.consultations.show', $consultation) }}" 
                                   class="btn btn-primary" 
                                   style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #6b7280;">No consultations found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection 