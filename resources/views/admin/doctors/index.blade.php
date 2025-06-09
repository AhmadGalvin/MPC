@extends('layouts.admin')

@section('title', 'Manage Doctors')
@section('header', 'Manage Doctors')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Doctors List</h2>
        <button onclick="openModal()"
                class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark flex items-center">
            <i class="fas fa-user-md mr-2"></i>
            Add New Doctor
        </button>
    </div>

    <!-- Doctors List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($doctors as $doctor)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <i class="fas fa-user-md text-gray-400 mr-2"></i>
                            <div class="text-sm font-medium text-gray-900">{{ $doctor->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $doctor->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $doctor->specialization ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $doctor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $doctor->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="editDoctor({{ $doctor->id }})"
                                class="text-primary hover:text-primary-dark mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteDoctor({{ $doctor->id }})"
                                class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="doctorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modalTitle">Add New Doctor</h3>
            <form id="doctorForm" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="doctor_id" id="doctorId">

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" 
                           name="name" 
                           id="doctorName"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" 
                           name="email" 
                           id="doctorEmail"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <!-- Password (only for new doctors) -->
                <div id="passwordField">
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" 
                           name="password" 
                           id="doctorPassword"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <!-- Specialization -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Specialization</label>
                    <input type="text" 
                           name="specialization" 
                           id="doctorSpecialization"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="doctorStatus"
                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Active</label>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                            onclick="closeModal()"
                            class="px-4 py-2 border text-gray-700 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">
                        <span id="submitButtonText">Add Doctor</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal() {
        document.getElementById('modalTitle').textContent = 'Add New Doctor';
        document.getElementById('submitButtonText').textContent = 'Add Doctor';
        document.getElementById('doctorForm').reset();
        document.getElementById('doctorId').value = '';
        document.getElementById('passwordField').style.display = 'block';
        document.getElementById('doctorModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('doctorModal').classList.add('hidden');
    }

    function editDoctor(id) {
        fetch(`/admin/doctors/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('doctorId').value = data.id;
                document.getElementById('doctorName').value = data.name;
                document.getElementById('doctorEmail').value = data.email;
                document.getElementById('doctorSpecialization').value = data.specialization;
                document.getElementById('doctorStatus').checked = data.is_active;
                document.getElementById('passwordField').style.display = 'none';
                document.getElementById('modalTitle').textContent = 'Edit Doctor';
                document.getElementById('submitButtonText').textContent = 'Update Doctor';
                document.getElementById('doctorModal').classList.remove('hidden');
            });
    }

    function deleteDoctor(id) {
        if (confirm('Are you sure you want to delete this doctor?')) {
            fetch(`/admin/doctors/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                }
            });
        }
    }

    document.getElementById('doctorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const doctorId = formData.get('doctor_id');
        const method = doctorId ? 'PUT' : 'POST';
        const url = doctorId ? `/admin/doctors/${doctorId}` : '/admin/doctors';

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.reload();
            }
        });
    });
</script>
@endpush 