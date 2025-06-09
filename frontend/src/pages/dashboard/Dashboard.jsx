import { useAuth } from '../../contexts/AuthContext';

export default function Dashboard() {
  const { user } = useAuth();

  return (
    <div className="space-y-6">
      <div className="bg-white shadow rounded-lg p-6">
        <h2 className="text-2xl font-bold text-gray-900 mb-4">
          Welcome back, {user?.name}!
        </h2>
        <p className="text-gray-600">
          You are logged in as a {user?.role.replace('_', ' ')}.
        </p>
      </div>

      {/* Role-specific content */}
      {user?.role === 'owner' && (
        <div className="bg-white shadow rounded-lg p-6">
          <h3 className="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="p-4 bg-primary-50 rounded-lg">
              <h4 className="font-medium text-primary-900">My Pets</h4>
              <p className="text-primary-600 mt-1">Manage your pets' information</p>
            </div>
            <div className="p-4 bg-primary-50 rounded-lg">
              <h4 className="font-medium text-primary-900">Consultations</h4>
              <p className="text-primary-600 mt-1">Schedule or view consultations</p>
            </div>
          </div>
        </div>
      )}

      {user?.role === 'doctor' && (
        <div className="bg-white shadow rounded-lg p-6">
          <h3 className="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="p-4 bg-primary-50 rounded-lg">
              <h4 className="font-medium text-primary-900">Medical Records</h4>
              <p className="text-primary-600 mt-1">View and manage patient records</p>
            </div>
            <div className="p-4 bg-primary-50 rounded-lg">
              <h4 className="font-medium text-primary-900">Consultations</h4>
              <p className="text-primary-600 mt-1">View upcoming consultations</p>
            </div>
          </div>
        </div>
      )}

      {user?.role === 'clinic_admin' && (
        <div className="bg-white shadow rounded-lg p-6">
          <h3 className="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="p-4 bg-primary-50 rounded-lg">
              <h4 className="font-medium text-primary-900">Doctors</h4>
              <p className="text-primary-600 mt-1">Manage clinic doctors</p>
            </div>
            <div className="p-4 bg-primary-50 rounded-lg">
              <h4 className="font-medium text-primary-900">Products</h4>
              <p className="text-primary-600 mt-1">Manage clinic products</p>
            </div>
          </div>
        </div>
      )}
    </div>
  );
} 