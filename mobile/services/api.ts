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

// Messages APIs
export const messageAPI = {
  getMessages: () => api.get('/messages'),
  getUnreadCount: () => api.get('/messages/unread-count'),
  markAsRead: (messageId: number) => api.post(`/messages/${messageId}/mark-read`),
  markAllAsRead: () => api.post('/messages/mark-all-read'),
  sendSupportMessage: (data: { subject: string; message: string }) =>
    api.post('/support', data),
  replyToAdminMessage: (data: { message_id: number; subject: string; message: string }) =>
    api.post('/messages/reply', data),
};

// Admin Messages APIs (for admin users)
export const adminMessageAPI = {
  sendToUser: (data: { user_id: number; order_id?: number; subject: string; message: string; send_email?: boolean }) =>
    api.post('/admin/messages/send', data),
  getUserMessages: (userId: number) => api.get(`/admin/messages/user/${userId}`),
  getSupportMessages: () => api.get('/admin/messages/support'),
};

export const authAPI = {
  login: (data: { email: string; password: string }) => api.post('/auth/login', data),
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

export const notificationsAPI = notificationAPI;

// Support APIs
export const supportAPI = {
  submit: (data: { subject: string; message: string }) => api.post('/support', data),
};

export default api;
