import { Platform } from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Change this to your Laravel backend URL
const API_BASE_URL = Platform.OS === 'android' ? 'http://10.0.2.2:8000/api' : 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 15000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor - attach token
api.interceptors.request.use(
  async (config) => {
    const token = await AsyncStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response interceptor - handle 401
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      await AsyncStorage.removeItem('auth_token');
      await AsyncStorage.removeItem('user_data');
    }
    return Promise.reject(error);
  }
);

// Auth APIs
export const authAPI = {
  register: (data: { name: string; email: string; password: string; password_confirmation: string; phone?: string }) =>
    api.post('/auth/register', data),

  login: (data: { email: string; password: string }) =>
    api.post('/auth/login', data),

  logout: () => api.post('/auth/logout'),

  profile: () => api.get('/auth/profile'),
};

// Order APIs
export const orderAPI = {
  getAll: () => api.get('/orders'),

  getOne: (id: number) => api.get(`/orders/${id}`),

  create: (data: {
    pickup_location: string;
    delivery_location: string;
    cargo_size: string;
    cargo_weight: number;
    notes?: string;
    pickup_datetime: string;
    delivery_datetime: string;
  }) => api.post('/orders', data),

  update: (id: number, data: any) => api.put(`/orders/${id}`, data),

  delete: (id: number) => api.delete(`/orders/${id}`),
};

export default api;
