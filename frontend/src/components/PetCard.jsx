import PropTypes from 'prop-types';

export default function PetCard({ pet, onClick, actions }) {
  return (
    <div className="bg-white overflow-hidden shadow rounded-lg">
      <div className="aspect-w-3 aspect-h-2">
        {pet.photo ? (
          <img
            src={`${import.meta.env.VITE_API_URL}/storage/${pet.photo}`}
            alt={pet.name}
            className="w-full h-48 object-cover"
          />
        ) : (
          <div className="w-full h-48 bg-gray-200 flex items-center justify-center">
            <span className="text-gray-400">No photo</span>
          </div>
        )}
      </div>
      <div className="p-4">
        <h3 className="text-lg font-medium text-gray-900 truncate">{pet.name}</h3>
        <div className="mt-2 text-sm text-gray-500">
          <p>Weight: {pet.weight} kg</p>
          <p>Birth Date: {new Date(pet.birth_date).toLocaleDateString()}</p>
        </div>
        <div className="mt-4 flex justify-end space-x-2">
          {actions}
          {onClick && (
            <button
              onClick={() => onClick(pet)}
              className="btn btn-primary"
            >
              View Details
            </button>
          )}
        </div>
      </div>
    </div>
  );
}

PetCard.propTypes = {
  pet: PropTypes.shape({
    id: PropTypes.number.isRequired,
    name: PropTypes.string.isRequired,
    weight: PropTypes.number.isRequired,
    birth_date: PropTypes.string.isRequired,
    photo: PropTypes.string,
  }).isRequired,
  onClick: PropTypes.func,
  actions: PropTypes.node,
}; 