import { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-hot-toast';

const AuthContext = createContext({});

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  // Configure axios
  axios.defaults.baseURL = 'http://localhost:8000';
  axios.defaults.withCredentials = true;
  axios.defaults.headers.common['Accept'] = 'application/json';
  axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

  // Add response interceptor to handle 419 (CSRF token mismatch)
  axios.interceptors.response.use(
    response => response,
    async error => {
      if (error.response?.status === 419) {
        // Get a new CSRF token
        await axios.get('/sanctum/csrf-cookie');
        // Retry the original request
        return axios(error.config);
      }
      return Promise.reject(error);
    }
  );

  useEffect(() => {
    checkAuth();
  }, []);

  const checkAuth = async () => {
    try {
      // Get CSRF cookie first
      await axios.get('/sanctum/csrf-cookie');
      
      const { data } = await axios.get('/api/me');
      setUser(data);
    } catch (error) {
      setUser(null);
    } finally {
      setLoading(false);
    }
  };

  const login = async (credentials) => {
    try {
      // Get CSRF cookie
      await axios.get('/sanctum/csrf-cookie');
      
      // Login request
      const { data } = await axios.post('/api/login', credentials);
      setUser(data.user);
      toast.success('Logged in successfully');
      return true;
    } catch (error) {
      const message = error.response?.data?.message || 'Failed to login';
      toast.error(message);
      return false;
    }
  };

  const register = async (userData) => {
    try {
      // Get CSRF cookie
      await axios.get('/sanctum/csrf-cookie');
      
      // Register request
      const { data } = await axios.post('/api/register', userData);
      setUser(data.user);
      toast.success('Registered successfully');
      return true;
    } catch (error) {
      const message = error.response?.data?.message || 'Failed to register';
      toast.error(message);
      return false;
    }
  };

  const logout = async () => {
    try {
      await axios.post('/api/logout');
      setUser(null);
      toast.success('Logged out successfully');
    } catch (error) {
      toast.error('Failed to logout');
    }
  };

  return (
    <AuthContext.Provider value={{
      user,
      loading,
      login,
      register,
      logout,
    }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  return useContext(AuthContext);
}; 