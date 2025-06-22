<template>
  <scroll-view class="container">
    <view class="login-form">
      <view class="logo-container">
        <text class="app-title">Attendance App</text>
        <text class="app-subtitle">Employee Check-in System</text>
      </view>

      <view class="form-container">
        <text-input
          v-model="email"
          placeholder="Email Address"
          class="input"
          :auto-capitalize="'none'"
          :keyboard-type="'email-address'"
        />
        
        <text-input
          v-model="password"
          placeholder="Password"
          class="input"
          :secure-text-entry="true"
        />

        <touchable-opacity 
          class="login-button"
          :disabled="loading"
          @press="handleLogin"
        >
          <text class="login-button-text">
            {{ loading ? 'Logging in...' : 'Login' }}
          </text>
        </touchable-opacity>

        <view v-if="error" class="error-container">
          <text class="error-text">{{ error }}</text>
        </view>
      </view>
    </view>
  </scroll-view>
</template>

<script>
import ApiService from '../services/ApiService'
import AsyncStorage from '@react-native-async-storage/async-storage'

export default {
  data() {
    return {
      email: '',
      password: '',
      loading: false,
      error: null
    }
  },
  
  methods: {
    async handleLogin() {
      if (!this.email || !this.password) {
        this.error = 'Please enter both email and password'
        return
      }

      this.loading = true
      this.error = null

      try {
        const response = await ApiService.login(this.email, this.password)
        
        if (response.success) {
          this.$navigator.navigate('Dashboard', { user: response.data.user })
        } else {
          this.error = response.message || 'Login failed'
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Network error. Please try again.'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style>
.container {
  flex: 1;
  background-color: #f8f9fa;
}

.login-form {
  flex: 1;
  justify-content: center;
  padding: 20px;
}

.logo-container {
  align-items: center;
  margin-bottom: 40px;
}

.app-title {
  font-size: 28px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 8px;
}

.app-subtitle {
  font-size: 16px;
  color: #7f8c8d;
}

.form-container {
  background-color: white;
  padding: 30px;
  border-radius: 10px;
  shadow-color: #000;
  shadow-offset: { width: 0, height: 2 };
  shadow-opacity: 0.1;
  shadow-radius: 4;
  elevation: 3;
}

.input {
  height: 50px;
  border-width: 1px;
  border-color: #e9ecef;
  border-radius: 8px;
  padding-horizontal: 15px;
  margin-bottom: 15px;
  font-size: 16px;
  background-color: #f8f9fa;
}

.login-button {
  background-color: #007bff;
  height: 50px;
  border-radius: 8px;
  justify-content: center;
  align-items: center;
  margin-top: 10px;
}

.login-button:disabled {
  background-color: #6c757d;
}

.login-button-text {
  color: white;
  font-size: 16px;
  font-weight: bold;
}

.error-container {
  margin-top: 15px;
  padding: 10px;
  background-color: #f8d7da;
  border-radius: 5px;
}

.error-text {
  color: #721c24;
  text-align: center;
  font-size: 14px;
}
</style>