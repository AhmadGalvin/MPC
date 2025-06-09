import React, { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import axios from 'axios';
import { toast } from 'react-toastify';
import { FaCalendarAlt, FaEdit, FaTrash } from 'react-icons/fa';

interface Doctor {
  id: number;
  name: string;
}

interface Schedule {
  id: number;
  doctor_id: number;
  doctor: Doctor;
  date: string;
  start_time: string;
  end_time: string;
  is_available: boolean;
}

interface ScheduleFormData {
  doctor_id: number;
  date: string;
  start_time: string;
  end_time: string;
}

const Schedules: React.FC = () => {
  const [schedules, setSchedules] = useState<Schedule[]>([]);
  const [doctors, setDoctors] = useState<Doctor[]>([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [editingSchedule, setEditingSchedule] = useState<Schedule | null>(null);

  const { register, handleSubmit, reset, formState: { errors } } = useForm<ScheduleFormData>();

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const [schedulesRes, doctorsRes] = await Promise.all([
        axios.get('/api/admin/schedules'),
        axios.get('/api/admin/doctors')
      ]);
      setSchedules(schedulesRes.data.data);
      setDoctors(doctorsRes.data.data);
    } catch (error) {
      toast.error('Failed to load data');
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  const onSubmit = async (data: ScheduleFormData) => {
    try {
      if (editingSchedule) {
        await axios.put(`/api/admin/schedules/${editingSchedule.id}`, data);
        toast.success('Schedule updated successfully');
      } else {
        await axios.post('/api/admin/schedules', data);
        toast.success('Schedule added successfully');
      }
      
      fetchData();
      closeModal();
    } catch (error) {
      toast.error(editingSchedule ? 'Failed to update schedule' : 'Failed to add schedule');
      console.error(error);
    }
  };

  const handleDelete = async (scheduleId: number) => {
    if (!window.confirm('Are you sure you want to delete this schedule?')) {
      return;
    }

    try {
      await axios.delete(`/api/admin/schedules/${scheduleId}`);
      toast.success('Schedule deleted successfully');
      fetchData();
    } catch (error) {
      toast.error('Failed to delete schedule');
      console.error(error);
    }
  };

  const openAddModal = () => {
    setEditingSchedule(null);
    reset({
      doctor_id: doctors[0]?.id,
      date: '',
      start_time: '',
      end_time: '',
    });
    setShowModal(true);
  };

  const openEditModal = (schedule: Schedule) => {
    setEditingSchedule(schedule);
    reset({
      doctor_id: schedule.doctor_id,
      date: schedule.date,
      start_time: schedule.start_time,
      end_time: schedule.end_time,
    });
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setEditingSchedule(null);
    reset();
  };

  if (loading) {
    return <div className="flex justify-center items-center h-full">Loading...</div>;
  }

  return (
    <div>
      {/* Header */}
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-bold">Doctor Schedules</h2>
        <button
          onClick={openAddModal}
          className="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark flex items-center"
        >
          <FaCalendarAlt className="mr-2" />
          Add New Schedule
        </button>
      </div>

      {/* Schedules List */}
      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Doctor
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Time
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {schedules.map((schedule) => (
              <tr key={schedule.id}>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm font-medium text-gray-900">
                    {schedule.doctor.name}
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {new Date(schedule.date).toLocaleDateString()}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {schedule.start_time} - {schedule.end_time}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span
                    className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                      schedule.is_available
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                    }`}
                  >
                    {schedule.is_available ? 'Available' : 'Unavailable'}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button
                    onClick={() => openEditModal(schedule)}
                    className="text-primary hover:text-primary-dark mr-3"
                  >
                    <FaEdit className="h-5 w-5" />
                  </button>
                  <button
                    onClick={() => handleDelete(schedule.id)}
                    className="text-red-600 hover:text-red-900"
                  >
                    <FaTrash className="h-5 w-5" />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Add/Edit Modal */}
      {showModal && (
        <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
          <div className="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div className="mt-3">
              <h3 className="text-lg font-medium leading-6 text-gray-900">
                {editingSchedule ? 'Edit Schedule' : 'Add New Schedule'}
              </h3>
              <form onSubmit={handleSubmit(onSubmit)} className="mt-4 space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700">Doctor</label>
                  <select
                    {...register('doctor_id', { required: 'Doctor is required' })}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                  >
                    {doctors.map((doctor) => (
                      <option key={doctor.id} value={doctor.id}>
                        {doctor.name}
                      </option>
                    ))}
                  </select>
                  {errors.doctor_id && (
                    <p className="mt-1 text-sm text-red-600">{errors.doctor_id.message}</p>
                  )}
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700">Date</label>
                  <input
                    type="date"
                    {...register('date', { required: 'Date is required' })}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                  />
                  {errors.date && (
                    <p className="mt-1 text-sm text-red-600">{errors.date.message}</p>
                  )}
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Start Time
                  </label>
                  <input
                    type="time"
                    {...register('start_time', { required: 'Start time is required' })}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                  />
                  {errors.start_time && (
                    <p className="mt-1 text-sm text-red-600">
                      {errors.start_time.message}
                    </p>
                  )}
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    End Time
                  </label>
                  <input
                    type="time"
                    {...register('end_time', { required: 'End time is required' })}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                  />
                  {errors.end_time && (
                    <p className="mt-1 text-sm text-red-600">{errors.end_time.message}</p>
                  )}
                </div>

                <div className="flex justify-end space-x-3">
                  <button
                    type="button"
                    onClick={closeModal}
                    className="px-4 py-2 border text-gray-700 rounded-md hover:bg-gray-50"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark"
                  >
                    {editingSchedule ? 'Update' : 'Add'} Schedule
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default Schedules; 