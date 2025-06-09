import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import { AuthProvider, useAuth } from './contexts/AuthContext';

// Layouts
import AuthLayout from './layouts/AuthLayout';
import DashboardLayout from './layouts/DashboardLayout';

// Auth Pages
import Login from './pages/auth/Login';
import Register from './pages/auth/Register';

// Dashboard Pages
import Dashboard from './pages/dashboard/Dashboard';
import Pets from './pages/pets/Pets';
import MedicalRecords from './pages/medical-records/MedicalRecords';
import Products from './pages/products/Products';
import Doctors from './pages/doctors/Doctors';
import Consultations from './pages/consultations/Consultations';
import Chat from './pages/consultations/Chat';

// Protected Route Component
const ProtectedRoute = ({ children, roles = [] }) => {
  const { user, loading } = useAuth();

  if (loading) {
    return <div>Loading...</div>;
  }

  if (!user) {
    return <Navigate to="/login" />;
  }

  if (roles.length > 0 && !roles.includes(user.role)) {
    return <Navigate to="/dashboard" />;
  }

  return children;
};

function App() {
  return (
    <AuthProvider>
      <Router>
        <Routes>
          {/* Auth Routes */}
          <Route element={<AuthLayout />}>
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />
          </Route>

          {/* Protected Dashboard Routes */}
          <Route
            element={
              <ProtectedRoute>
                <DashboardLayout />
              </ProtectedRoute>
            }
          >
            <Route path="/" element={<Navigate to="/dashboard" />} />
            <Route path="/dashboard" element={<Dashboard />} />
            
            {/* Owner Routes */}
            <Route
              path="/pets"
              element={
                <ProtectedRoute roles={['owner']}>
                  <Pets />
                </ProtectedRoute>
              }
            />

            {/* Doctor Routes */}
            <Route
              path="/medical-records"
              element={
                <ProtectedRoute roles={['doctor']}>
                  <MedicalRecords />
                </ProtectedRoute>
              }
            />

            {/* Clinic Admin Routes */}
            <Route
              path="/doctors"
              element={
                <ProtectedRoute roles={['clinic_admin']}>
                  <Doctors />
                </ProtectedRoute>
              }
            />

            {/* Shared Routes */}
            <Route path="/products" element={<Products />} />
            
            {/* Consultation Routes (Owner & Doctor) */}
            <Route
              path="/consultations"
              element={
                <ProtectedRoute roles={['owner', 'doctor']}>
                  <Consultations />
                </ProtectedRoute>
              }
            />
            <Route
              path="/consultations/:id"
              element={
                <ProtectedRoute roles={['owner', 'doctor']}>
                  <Chat />
                </ProtectedRoute>
              }
            />
          </Route>
        </Routes>
      </Router>
      <Toaster position="top-right" />
    </AuthProvider>
  );
}

export default App;
