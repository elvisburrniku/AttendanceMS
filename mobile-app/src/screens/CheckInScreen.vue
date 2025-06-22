<template>
  <scroll-view class="container">
    <view class="header">
      <text class="title">{{ action === 'checkin' ? 'Check In' : 'Check Out' }}</text>
    </view>

    <view class="location-card">
      <text class="section-title">Location Information</text>
      <view v-if="location" class="location-info">
        <text class="location-text">📍 {{ location.address || 'Location detected' }}</text>
        <text class="coordinates">
          Lat: {{ location.latitude?.toFixed(6) }}, 
          Lng: {{ location.longitude?.toFixed(6) }}
        </text>
      </view>
      <view v-else class="location-loading">
        <text class="location-text">📍 Getting your location...</text>
      </view>
      
      <touchable-opacity @press="getCurrentLocation" class="refresh-location">
        <text class="refresh-text">Refresh Location</text>
      </touchable-opacity>
    </view>

    <view class="time-card">
      <text class="section-title">Current Time</text>
      <text class="current-time">{{ currentTime }}</text>
      <text class="current-date">{{ currentDate }}</text>
    </view>

    <view class="action-container">
      <touchable-opacity 
        class="action-button"
        :class="action"
        :disabled="loading || !location"
        @press="handleAttendanceAction"
      >
        <text class="action-button-text">
          {{ loading ? 'Processing...' : (action === 'checkin' ? 'Check In Now' : 'Check Out Now') }}
        </text>
      </touchable-opacity>
    </view>

    <view v-if="error" class="error-container">
      <text class="error-text">{{ error }}</text>
    </view>

    <view v-if="successMessage" class="success-container">
      <text class="success-text">{{ successMessage }}</text>
    </view>
  </scroll-view>
</template>

<script>
import ApiService from '../services/ApiService'
import { Platform, PermissionsAndroid } from 'react-native'
import Geolocation from '@react-native-geolocation/geolocation'

export default {
  props: ['action', 'user', 'onSuccess'],
  
  data() {
    return {
      location: null,
      currentTime: '',
      currentDate: '',
      loading: false,
      error: null,
      successMessage: null,
      timeInterval: null
    }
  },

  async mounted() {
    this.updateTime()
    this.timeInterval = setInterval(this.updateTime, 1000)
    await this.getCurrentLocation()
  },

  beforeDestroy() {
    if (this.timeInterval) {
      clearInterval(this.timeInterval)
    }
  },

  methods: {
    updateTime() {
      const now = new Date()
      this.currentTime = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
      })
      this.currentDate = now.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },

    async requestLocationPermission() {
      if (Platform.OS === 'android') {
        try {
          const granted = await PermissionsAndroid.request(
            PermissionsAndroid.PERMISSIONS.ACCESS_FINE_LOCATION,
            {
              title: 'Location Permission',
              message: 'This app needs access to location for attendance tracking.',
              buttonNeutral: 'Ask Me Later',
              buttonNegative: 'Cancel',
              buttonPositive: 'OK',
            },
          )
          return granted === PermissionsAndroid.RESULTS.GRANTED
        } catch (err) {
          console.warn(err)
          return false
        }
      }
      return true
    },

    async getCurrentLocation() {
      const hasPermission = await this.requestLocationPermission()
      
      if (!hasPermission) {
        this.error = 'Location permission is required for attendance tracking'
        return
      }

      return new Promise((resolve) => {
        Geolocation.getCurrentPosition(
          async (position) => {
            this.location = {
              latitude: position.coords.latitude,
              longitude: position.coords.longitude,
              accuracy: position.coords.accuracy
            }
            
            // Try to get address from coordinates
            try {
              await this.reverseGeocode(this.location.latitude, this.location.longitude)
            } catch (error) {
              console.log('Reverse geocoding failed:', error)
            }
            
            this.error = null
            resolve(this.location)
          },
          (error) => {
            console.log('Location error:', error)
            this.error = 'Could not get your location. Please enable GPS and try again.'
            resolve(null)
          },
          {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 10000
          }
        )
      })
    },

    async reverseGeocode(latitude, longitude) {
      // This would typically use a geocoding service
      // For demo purposes, we'll create a simple address
      this.location.address = `Location: ${latitude.toFixed(4)}, ${longitude.toFixed(4)}`
    },

    async handleAttendanceAction() {
      if (!this.location) {
        this.error = 'Please wait for location to be detected'
        return
      }

      if (!this.user?.employee?.id) {
        this.error = 'Employee information not found'
        return
      }

      this.loading = true
      this.error = null
      this.successMessage = null

      try {
        let response
        if (this.action === 'checkin') {
          response = await ApiService.checkIn(this.user.employee.id, this.location)
        } else {
          response = await ApiService.checkOut(this.user.employee.id, this.location)
        }

        if (response.success) {
          this.successMessage = response.message
          setTimeout(() => {
            if (this.onSuccess) {
              this.onSuccess()
            }
            this.$navigator.goBack()
          }, 2000)
        } else {
          this.error = response.message || `${this.action} failed`
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

.header {
  padding: 20px;
  background-color: white;
  align-items: center;
  shadow-color: #000;
  shadow-offset: { width: 0, height: 2 };
  shadow-opacity: 0.1;
  shadow-radius: 4;
  elevation: 3;
}

.title {
  font-size: 24px;
  font-weight: bold;
  color: #2c3e50;
}

.location-card, .time-card {
  margin: 20px;
  padding: 20px;
  background-color: white;
  border-radius: 10px;
  shadow-color: #000;
  shadow-offset: { width: 0, height: 2 };
  shadow-opacity: 0.1;
  shadow-radius: 4;
  elevation: 3;
}

.section-title {
  font-size: 18px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 10px;
}

.location-info {
  gap: 5px;
}

.location-text {
  font-size: 16px;
  color: #495057;
}

.coordinates {
  font-size: 12px;
  color: #6c757d;
}

.location-loading {
  align-items: center;
  padding: 10px;
}

.refresh-location {
  margin-top: 10px;
  padding: 8px 16px;
  background-color: #17a2b8;
  border-radius: 5px;
  align-self: flex-start;
}

.refresh-text {
  color: white;
  font-weight: bold;
}

.current-time {
  font-size: 32px;
  font-weight: bold;
  color: #2c3e50;
  text-align: center;
}

.current-date {
  font-size: 16px;
  color: #6c757d;
  text-align: center;
  margin-top: 5px;
}

.action-container {
  padding: 20px;
}

.action-button {
  height: 60px;
  border-radius: 10px;
  justify-content: center;
  align-items: center;
  shadow-color: #000;
  shadow-offset: { width: 0, height: 2 };
  shadow-opacity: 0.1;
  shadow-radius: 4;
  elevation: 3;
}

.checkin {
  background-color: #28a745;
}

.checkout {
  background-color: #007bff;
}

.action-button:disabled {
  background-color: #6c757d;
  opacity: 0.6;
}

.action-button-text {
  color: white;
  font-size: 18px;
  font-weight: bold;
}

.error-container {
  margin: 0px 20px;
  padding: 15px;
  background-color: #f8d7da;
  border-radius: 8px;
  border: 1px solid #f5c6cb;
}

.error-text {
  color: #721c24;
  text-align: center;
  font-size: 14px;
}

.success-container {
  margin: 0px 20px;
  padding: 15px;
  background-color: #d4edda;
  border-radius: 8px;
  border: 1px solid #c3e6cb;
}

.success-text {
  color: #155724;
  text-align: center;
  font-size: 14px;
  font-weight: bold;
}
</style>