import { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import axios from 'axios';

export default function ProductList({ onEdit, onDelete, isAdmin = false }) {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [category, setCategory] = useState('');
  const [priceRange, setPriceRange] = useState({ min: '', max: '' });
  const [sortPrice, setSortPrice] = useState('');

  useEffect(() => {
    fetchProducts();
  }, [category, priceRange, sortPrice]);

  const fetchProducts = async () => {
    try {
      const params = {
        ...(category && { category }),
        ...(priceRange.min && { min_price: priceRange.min }),
        ...(priceRange.max && { max_price: priceRange.max }),
        ...(sortPrice && { sort_price: sortPrice }),
      };

      const { data } = await axios.get('/api/products', { params });
      setProducts(data.data);
    } catch (error) {
      console.error('Error fetching products:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div>Loading products...</div>;
  }

  return (
    <div>
      {/* Filters */}
      <div className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div>
          <label htmlFor="category" className="label">Category</label>
          <select
            id="category"
            value={category}
            onChange={(e) => setCategory(e.target.value)}
            className="input"
          >
            <option value="">All Categories</option>
            <option value="food">Food</option>
            <option value="medicine">Medicine</option>
            <option value="accessories">Accessories</option>
          </select>
        </div>

        <div>
          <label htmlFor="min-price" className="label">Min Price</label>
          <input
            type="number"
            id="min-price"
            value={priceRange.min}
            onChange={(e) => setPriceRange(prev => ({ ...prev, min: e.target.value }))}
            className="input"
            min="0"
          />
        </div>

        <div>
          <label htmlFor="max-price" className="label">Max Price</label>
          <input
            type="number"
            id="max-price"
            value={priceRange.max}
            onChange={(e) => setPriceRange(prev => ({ ...prev, max: e.target.value }))}
            className="input"
            min="0"
          />
        </div>

        <div>
          <label htmlFor="sort-price" className="label">Sort by Price</label>
          <select
            id="sort-price"
            value={sortPrice}
            onChange={(e) => setSortPrice(e.target.value)}
            className="input"
          >
            <option value="">No Sorting</option>
            <option value="asc">Low to High</option>
            <option value="desc">High to Low</option>
          </select>
        </div>
      </div>

      {/* Product Grid */}
      <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {products.map((product) => (
          <div key={product.id} className="card">
            <div className="aspect-w-3 aspect-h-2 mb-4">
              {product.image ? (
                <img
                  src={`${import.meta.env.VITE_API_URL}/storage/${product.image}`}
                  alt={product.name}
                  className="w-full h-48 object-cover rounded-lg"
                />
              ) : (
                <div className="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                  <span className="text-gray-400">No image</span>
                </div>
              )}
            </div>

            <h3 className="text-lg font-medium text-gray-900">{product.name}</h3>
            
            <div className="mt-2 text-sm text-gray-500">
              <p>Category: {product.category}</p>
              <p className="mt-1">
                Price: ${product.price.toFixed(2)}
                {product.discount > 0 && (
                  <span className="ml-2 text-red-600">
                    -{product.discount}%
                  </span>
                )}
              </p>
              {product.discount > 0 && (
                <p className="mt-1 text-primary-600 font-medium">
                  Final Price: ${product.final_price.toFixed(2)}
                </p>
              )}
            </div>

            {isAdmin && (
              <div className="mt-4 flex justify-end space-x-2">
                <button
                  onClick={() => onEdit(product)}
                  className="btn btn-secondary"
                >
                  Edit
                </button>
                <button
                  onClick={() => onDelete(product)}
                  className="btn bg-red-600 text-white hover:bg-red-700"
                >
                  Delete
                </button>
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}

ProductList.propTypes = {
  onEdit: PropTypes.func,
  onDelete: PropTypes.func,
  isAdmin: PropTypes.bool,
}; 