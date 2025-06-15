@extends('layouts.admin')

@section('header', 'Medical Records')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div class="card-title" style="margin: 0;">Medical Records List</div>
        <a href="{{ route('admin.medical-records.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Record
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Doctor</th>
                    <th>Diagnosis</th>
                    <th>Next Visit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td>{{ $record->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.pets.show', $record->pet) }}" class="text-primary">
                                {{ $record->pet->name }}
                            </a>
                        </td>
                        <td>{{ $record->pet->owner->name }}</td>
                        <td>{{ $record->doctor->name }}</td>
                        <td>{{ Str::limit($record->diagnosis, 50) }}</td>
                        <td>
                            @if($record->next_visit_date)
                                {{ \Carbon\Carbon::parse($record->next_visit_date)->format('M d, Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('admin.medical-records.show', $record) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.medical-records.edit', $record) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.medical-records.destroy', $record) }}" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">No medical records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1rem;">
        {{ $records->links() }}
    </div>
</div>
@endsection 