import { useState, useEffect } from 'react';
import axios from 'axios';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
}

interface AuthState {
  user: User | null;
  isAuthenticated: boolean;
  loading: boolean;
}

export const useAuth = () => {
  const [state, setState] = useState<AuthState>({
    user: null,
    isAuthenticated: false,
    loading: true,
  });

  useEffect(() => {
    const checkAuth = async () => {
      try {
        const token = localStorage.getItem('token');
        if (!token) {
          setState({ user: null, isAuthenticated: false, loading: false });
          return;
        }

        const response = await axios.get('/api/user', {
          headers: { Authorization: `Bearer ${token}` },
        });

        setState({
          user: response.data,
          isAuthenticated: true,
          loading: false,
        });
      } catch (error) {
        localStorage.removeItem('token');
        setState({ user: null, isAuthenticated: false, loading: false });
      }
    };

    checkAuth();
  }, []);

  const login = async (email: string, password: string) => {
    try {
      const response = await axios.post('/api/login', { email, password });
      const { token, user } = response.data;
      
      localStorage.setItem('token', token);
      setState({ user, isAuthenticated: true, loading: false });
      
      // Set default authorization header for all future requests
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      
      return true;
    } catch (error) {
      return false;
    }
  };

  const logout = () => {
    localStorage.removeItem('token');
    delete axios.defaults.headers.common['Authorization'];
    setState({ user: null, isAuthenticated: false, loading: false });
  };

  return {
    ...state,
    login,
    logout,
  };
};

export default useAuth; 