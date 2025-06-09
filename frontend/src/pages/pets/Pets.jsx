import { useState, useEffect } from 'react';
import axios from 'axios';
import PetCard from '../../components/PetCard';

export default function Pets() {
  const [pets, setPets] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchPets();
  }, []);

  const fetchPets = async () => {
    try {
      const { data } = await axios.get('/api/pets');
      setPets(data.data);
    } catch (error) {
      console.error('Error fetching pets:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div>Loading pets...</div>;
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h2 className="text-2xl font-bold text-gray-900">My Pets</h2>
        <button className="btn btn-primary">Add Pet</button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {pets.map((pet) => (
          <PetCard
            key={pet.id}
            pet={pet}
            onClick={(pet) => console.log('View pet:', pet)}
          />
        ))}
      </div>

      {pets.length === 0 && (
        <div className="text-center py-12">
          <p className="text-gray-500">No pets found. Add your first pet!</p>
        </div>
      )}
    </div>
  );
} 