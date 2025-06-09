import { useState, useEffect } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import ProductList from '../../components/ProductList';

export default function Products() {
  const { user } = useAuth();
  const isAdmin = user?.role === 'clinic_admin';

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h2 className="text-2xl font-bold text-gray-900">Products</h2>
      </div>

      <ProductList
        isAdmin={isAdmin}
        onEdit={isAdmin ? (product) => console.log('Edit product:', product) : undefined}
        onDelete={isAdmin ? (product) => console.log('Delete product:', product) : undefined}
      />
    </div>
  );
} 