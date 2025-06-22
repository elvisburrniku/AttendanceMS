<template>
  <scroll-view class="container">
    <view class="header">
      <text class="welcome-text">Welcome, {{ user?.name }}</text>
      <touchable-opacity @press="handleLogout" class="logout-button">
        <text class="logout-text">Logout</text>
      </touchable-opacity>
    </view>

    <view class="status-card">
      <text class="status-title">Today's Status</text>
      <view v-if="todayAttendance" class="attendance-info">
        <view class="time-row">
          <text class="time-label">Check-in:</text>
          <text class="time-value">
            {{ todayAttendance.check_in ? formatTime(todayAttendance.check_in) : 'Not checked in' }}
          </text>
        </view>
        <view class="time-row">
          <text class="time-label">Check-out:</text>
          <text class="time-value">
            {{ todayAttendance.check_out ? formatTime(todayAttendance.check_out) : 'Not checked out' }}
          </text>
        </view>
        <view v-if="todayAttendance.work_hours" class="time-row">
          <text class="time-label">Work Hours:</text>
          <text class="time-value">{{ todayAttendance.work_hours }} hours</text>
        </view>
      </view>
      <view v-else class="no-attendance">
        <text class="no-attendance-text">No attendance record for today</text>
      </view>
    </view>

    <view class="actions-container">
      <touchable-opacity 
        class="action-button check-in"
        :disabled="!canCheckIn"
        @press="navigateToCheckIn('checkin')"
      >
        <text class="action-button-text">Check In</text>
      </touchable-opacity>

      <touchable-opacity 
        class="action-button check-out"
        :disabled="!canCheckOut"
        @press="navigateToCheckIn('checkout')"
      >
        <text class="action-button-text">Check Out</text>
      </touchable-opacity>
    </view>

    <touchable-opacity 
      class="history-button"
      @press="navigateToHistory"
    >
      <text class="history-button-text">View Attendance History</text>
    </touchable-opacity>

    <view v-if="loading" class="loading-container">
      <text class="loading-text">Loading...</text>
    </view>
  </scroll-view>
</template>

<script>
import ApiService from '../services/ApiService'
import AsyncStorage from '@react-native-async-storage/async-storage'

export default {
  props: ['user'],
  
  data() {
    return {
      todayAttendance: null,
      loading: false
    }
  },

  computed: {
    canCheckIn() {
      return !this.todayAttendance?.check_in
    },
    
    canCheckOut() {
      return this.todayAttendance?.check_in && !this.todayAttendance?.check_out
    }
  },

  async mounted() {
    await this.loadTodayAttendance()
  },

  methods: {
    async loadTodayAttendance() {
      if (!this.user?.employee?.id) return
      
      this.loading = true
      try {
        const response = await ApiService.getTodayAttendance(this.user.employee.id)
        if (response.success) {
          this.todayAttendance = response.data.attendance
        }
      } catch (error) {
        console.error('Error loading attendance:', error)
      } finally {
        this.loading = false
      }
    },

    formatTime(timeString) {
      if (!timeString) return ''
      const date = new Date(timeString)
      return date.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
      })
    },

    navigateToCheckIn(action) {
      this.$navigator.navigate('CheckIn', { 
        action, 
        user: this.user,
        onSuccess: this.loadTodayAttendance 
      })
    },

    navigateToHistory() {
      this.$navigator.navigate('History', { user: this.user })
    },

    async handleLogout() {
      try {
        await ApiService.logout()
        this.$navigator.navigate('Login')
      } catch (error) {
        console.error('Logout error:', error)
        this.$navigator.navigate('Login')
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
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  background-color: white;
  shadow-color: #000;
  shadow-offset: { width: 0, height: 2 };
  shadow-opacity: 0.1;
  shadow-radius: 4;
  elevation: 3;
}

.welcome-text {
  font-size: 18px;
  font-weight: bold;
  color: #2c3e50;
}

.logout-button {
  padding: 8px 16px;
  background-color: #dc3545;
  border-radius: 5px;
}

.logout-text {
  color: white;
  font-weight: bold;
}

.status-card {
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

.status-title {
  font-size: 20px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 15px;
}

.attendance-info {
  gap: 10px;
}

.time-row {
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding-vertical: 8px;
}

.time-label {
  font-size: 16px;
  color: #6c757d;
  font-weight: 500;
}

.time-value {
  font-size: 16px;
  color: #2c3e50;
  font-weight: bold;
}

.no-attendance {
  align-items: center;
  padding: 20px;
}

.no-attendance-text {
  font-size: 16px;
  color: #6c757d;
  font-style: italic;
}

.actions-container {
  flex-direction: row;
  justify-content: space-around;
  padding: 20px;
  gap: 15px;
}

.action-button {
  flex: 1;
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

.check-in {
  background-color: #28a745;
}

.check-out {
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

.history-button {
  margin: 0px 20px 20px 20px;
  height: 50px;
  background-color: #17a2b8;
  border-radius: 8px;
  justify-content: center;
  align-items: center;
}

.history-button-text {
  color: white;
  font-size: 16px;
  font-weight: bold;
}

.loading-container {
  align-items: center;
  padding: 20px;
}

.loading-text {
  font-size: 16px;
  color: #6c757d;
}
</style>