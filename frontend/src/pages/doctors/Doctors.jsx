import { useState, useEffect } from 'react';
import axios from 'axios';

export default function Doctors() {
  const [doctors, setDoctors] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDoctors();
  }, []);

  const fetchDoctors = async () => {
    try {
      const { data } = await axios.get('/api/doctors');
      setDoctors(data.data);
    } catch (error) {
      console.error('Error fetching doctors:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div>Loading doctors...</div>;
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h2 className="text-2xl font-bold text-gray-900">Doctors</h2>
        <button className="btn btn-primary">Add Doctor</button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {doctors.map((doctor) => (
          <div key={doctor.id} className="bg-white shadow rounded-lg p-6">
            <h3 className="text-lg font-medium text-gray-900">{doctor.user.name}</h3>
            <div className="mt-2 text-sm text-gray-500">
              <p>Specialization: {doctor.specialization}</p>
              <p>SIP Number: {doctor.sip_number}</p>
            </div>
            <div className="mt-4 flex justify-end space-x-2">
              <button className="btn btn-secondary">Edit</button>
              <button className="btn bg-red-600 text-white hover:bg-red-700">
                Delete
              </button>
            </div>
          </div>
        ))}
      </div>

      {doctors.length === 0 && (
        <div className="text-center py-12">
          <p className="text-gray-500">No doctors found.</p>
        </div>
      )}
    </div>
  );
} 