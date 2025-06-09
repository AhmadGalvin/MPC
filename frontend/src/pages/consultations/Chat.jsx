import { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import ChatWindow from '../../components/ChatWindow';

export default function Chat() {
  const { id } = useParams();
  const [consultation, setConsultation] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchConsultation();
  }, [id]);

  const fetchConsultation = async () => {
    try {
      const { data } = await axios.get(`/api/consultations/${id}`);
      setConsultation(data.data);
    } catch (error) {
      console.error('Error fetching consultation:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div>Loading consultation...</div>;
  }

  if (!consultation) {
    return <div>Consultation not found.</div>;
  }

  return (
    <div className="space-y-6">
      <div className="bg-white shadow rounded-lg p-6">
        <h2 className="text-2xl font-bold text-gray-900 mb-4">
          Consultation with {consultation.pet.name}
        </h2>
        <div className="text-sm text-gray-500">
          <p>Doctor: Dr. {consultation.doctor.user.name}</p>
          <p>Scheduled: {new Date(consultation.scheduled_at).toLocaleString()}</p>
          <p>Status: {consultation.status}</p>
        </div>
      </div>

      <ChatWindow consultationId={consultation.id} />
    </div>
  );
} 