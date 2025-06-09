import React from 'react';
import { Outlet, Link, useLocation } from 'react-router-dom';
import { FaClinic, FaUserMd, FaCalendarAlt, FaBox, FaChartLine } from 'react-icons/fa';

const AdminLayout: React.FC = () => {
  const location = useLocation();

  const menuItems = [
    { path: '/admin/dashboard', label: 'Dashboard', icon: <FaChartLine /> },
    { path: '/admin/clinic', label: 'Clinic Profile', icon: <FaClinic /> },
    { path: '/admin/doctors', label: 'Doctors', icon: <FaUserMd /> },
    { path: '/admin/schedules', label: 'Schedules', icon: <FaCalendarAlt /> },
    { path: '/admin/products', label: 'Products', icon: <FaBox /> },
  ];

  return (
    <div className="flex h-screen bg-gray-100">
      {/* Sidebar */}
      <div className="w-64 bg-white shadow-lg">
        <div className="p-4">
          <h1 className="text-2xl font-bold text-primary">MedipetCare</h1>
          <p className="text-sm text-gray-600">Admin Dashboard</p>
        </div>
        <nav className="mt-4">
          {menuItems.map((item) => (
            <Link
              key={item.path}
              to={item.path}
              className={`flex items-center px-6 py-3 text-gray-700 hover:bg-primary hover:text-white transition-colors ${
                location.pathname === item.path ? 'bg-primary text-white' : ''
              }`}
            >
              <span className="mr-3">{item.icon}</span>
              {item.label}
            </Link>
          ))}
        </nav>
      </div>

      {/* Main Content */}
      <div className="flex-1 overflow-auto">
        <header className="bg-white shadow">
          <div className="px-6 py-4">
            <h2 className="text-xl font-semibold text-gray-800">
              {menuItems.find((item) => item.path === location.pathname)?.label || 'Dashboard'}
            </h2>
          </div>
        </header>
        <main className="p-6">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

export default AdminLayout; 