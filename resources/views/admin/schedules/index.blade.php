@extends('layouts.admin')

@section('title', 'Doctor Schedules')
@section('header', 'Doctor Schedules')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
@endpush

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Doctor Schedules</h2>
        <button onclick="openModal()"
                class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark flex items-center">
            <i class="fas fa-calendar-plus mr-2"></i>
            Add Schedule
        </button>
    </div>

    <!-- Calendar -->
    <div class="bg-white rounded-lg shadow p-6">
        <div id="calendar"></div>
    </div>
</div>

<!-- Modal -->
<div id="scheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modalTitle">Add Schedule</h3>
            <form id="scheduleForm" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="schedule_id" id="scheduleId">

                <!-- Doctor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Doctor</label>
                    <select name="doctor_id" 
                            id="doctorId"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Select Doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" 
                           name="date" 
                           id="scheduleDate"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <!-- Start Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input type="time" 
                           name="start_time" 
                           id="startTime"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <!-- End Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">End Time</label>
                    <input type="time" 
                           name="end_time" 
                           id="endTime"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" 
                              id="notes"
                              rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                            onclick="closeModal()"
                            class="px-4 py-2 border text-gray-700 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">
                        <span id="submitButtonText">Add Schedule</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
    let calendar;

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '/admin/schedules/events',
            editable: true,
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,
            eventClick: function(info) {
                editSchedule(info.event.id);
            },
            select: function(info) {
                openModalWithDate(info.start, info.end);
            },
            eventDrop: function(info) {
                updateScheduleDate(info.event);
            },
            eventResize: function(info) {
                updateScheduleTime(info.event);
            }
        });
        calendar.render();
    });

    function openModal() {
        document.getElementById('modalTitle').textContent = 'Add Schedule';
        document.getElementById('submitButtonText').textContent = 'Add Schedule';
        document.getElementById('scheduleForm').reset();
        document.getElementById('scheduleId').value = '';
        document.getElementById('scheduleModal').classList.remove('hidden');
    }

    function openModalWithDate(start, end) {
        openModal();
        document.getElementById('scheduleDate').value = start.toISOString().split('T')[0];
        document.getElementById('startTime').value = start.toTimeString().slice(0, 5);
        document.getElementById('endTime').value = end.toTimeString().slice(0, 5);
    }

    function closeModal() {
        document.getElementById('scheduleModal').classList.add('hidden');
    }

    function editSchedule(id) {
        fetch(`/admin/schedules/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('scheduleId').value = data.id;
                document.getElementById('doctorId').value = data.doctor_id;
                document.getElementById('scheduleDate').value = data.date;
                document.getElementById('startTime').value = data.start_time;
                document.getElementById('endTime').value = data.end_time;
                document.getElementById('notes').value = data.notes;
                document.getElementById('modalTitle').textContent = 'Edit Schedule';
                document.getElementById('submitButtonText').textContent = 'Update Schedule';
                document.getElementById('scheduleModal').classList.remove('hidden');
            });
    }

    function updateScheduleDate(event) {
        const scheduleId = event.id;
        const newStart = event.start;
        const newEnd = event.end;

        fetch(`/admin/schedules/${scheduleId}/update-date`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                start: newStart.toISOString(),
                end: newEnd.toISOString()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                calendar.refetchEvents();
            }
        });
    }

    function updateScheduleTime(event) {
        const scheduleId = event.id;
        const newStart = event.start;
        const newEnd = event.end;

        fetch(`/admin/schedules/${scheduleId}/update-time`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                start: newStart.toISOString(),
                end: newEnd.toISOString()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                calendar.refetchEvents();
            }
        });
    }

    document.getElementById('scheduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const scheduleId = formData.get('schedule_id');
        const method = scheduleId ? 'PUT' : 'POST';
        const url = scheduleId ? `/admin/schedules/${scheduleId}` : '/admin/schedules';

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
                closeModal();
                calendar.refetchEvents();
            }
        });
    });
</script>
@endpush 