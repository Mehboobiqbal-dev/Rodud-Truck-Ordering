import { Platform } from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Production API URL
const PRODUCTION_API = 'http://13.60.83.143/api';
const LOCAL_API = Platform.OS === 'android' ? 'http://10.0.2.2:8000/api' : 'http://localhost:8000/api';

// Use production API for real devices, local for web dev
const API_BASE_URL = Platform.OS === 'web' ? LOCAL_API : PRODUCTION_API;

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

// Notification APIs
export const notificationAPI = {
  getAll: () => api.get('/notifications'),
  markAsRead: () => api.post('/notifications/read'),
};

// Support APIs
export const supportAPI = {
  submit: (data: { subject: string; message: string }) => api.post('/support', data),
};

export default api;
