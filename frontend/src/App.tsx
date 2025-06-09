import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

// Layouts
import AdminLayout from './layouts/AdminLayout';

// Admin Pages
import Dashboard from './pages/admin/Dashboard';
import ClinicProfile from './pages/admin/ClinicProfile';
import Doctors from './pages/admin/Doctors';
import Schedules from './pages/admin/Schedules';
import Products from './pages/admin/Products';

// Auth Components
import PrivateRoute from './components/PrivateRoute';
import RoleRoute from './components/RoleRoute';

const App: React.FC = () => {
  return (
    <Router>
      <ToastContainer />
      <Routes>
        {/* Admin Routes */}
        <Route
          path="/admin"
          element={
            <PrivateRoute>
              <RoleRoute roles={['clinic_admin']}>
                <AdminLayout />
              </RoleRoute>
            </PrivateRoute>
          }
        >
          <Route path="dashboard" element={<Dashboard />} />
          <Route path="clinic" element={<ClinicProfile />} />
          <Route path="doctors" element={<Doctors />} />
          <Route path="schedules" element={<Schedules />} />
          <Route path="products" element={<Products />} />
        </Route>
      </Routes>
    </Router>
  );
};

export default App; 