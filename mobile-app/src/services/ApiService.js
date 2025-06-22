import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

class ApiService {
  constructor() {
    this.baseURL = 'http://localhost:5000/api'; // Update with your server URL
    this.client = axios.create({
      baseURL: this.baseURL,
      timeout: 10000,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });

    // Request interceptor to add auth token
    this.client.interceptors.request.use(
      async (config) => {
        const token = await AsyncStorage.getItem('auth_token');
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );

    // Response interceptor for error handling
    this.client.interceptors.response.use(
      (response) => response,
      async (error) => {
        if (error.response?.status === 401) {
          await AsyncStorage.removeItem('auth_token');
          await AsyncStorage.removeItem('user_data');
        }
        return Promise.reject(error);
      }
    );
  }

  // Authentication
  async login(email, password) {
    const response = await this.client.post('/login', { email, password });
    if (response.data.success) {
      await AsyncStorage.setItem('auth_token', response.data.data.token);
      await AsyncStorage.setItem('user_data', JSON.stringify(response.data.data.user));
    }
    return response.data;
  }

  async logout() {
    try {
      await this.client.post('/logout');
    } finally {
      await AsyncStorage.removeItem('auth_token');
      await AsyncStorage.removeItem('user_data');
    }
  }

  async getProfile() {
    const response = await this.client.get('/profile');
    return response.data;
  }

  // Attendance
  async checkIn(employeeId, location) {
    const response = await this.client.post('/checkin', {
      employee_id: employeeId,
      latitude: location?.latitude,
      longitude: location?.longitude,
      location_address: location?.address,
    });
    return response.data;
  }

  async checkOut(employeeId, location) {
    const response = await this.client.post('/checkout', {
      employee_id: employeeId,
      latitude: location?.latitude,
      longitude: location?.longitude,
      location_address: location?.address,
    });
    return response.data;
  }

  async getTodayAttendance(employeeId) {
    const response = await this.client.get('/attendance/today', {
      params: { employee_id: employeeId }
    });
    return response.data;
  }

  async getAttendanceHistory(employeeId, startDate, endDate) {
    const response = await this.client.get('/attendance/history', {
      params: {
        employee_id: employeeId,
        start_date: startDate,
        end_date: endDate,
      }
    });
    return response.data;
  }
}

export default new ApiService();