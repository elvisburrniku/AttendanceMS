<template>
  <view class="container">
    <view class="header">
      <text class="title">Attendance History</text>
    </view>

    <view class="filter-container">
      <view class="date-filters">
        <touchable-opacity @press="setDateRange('week')" class="filter-button" :class="{ active: selectedRange === 'week' }">
          <text class="filter-text">This Week</text>
        </touchable-opacity>
        <touchable-opacity @press="setDateRange('month')" class="filter-button" :class="{ active: selectedRange === 'month' }">
          <text class="filter-text">This Month</text>
        </touchable-opacity>
        <touchable-opacity @press="setDateRange('all')" class="filter-button" :class="{ active: selectedRange === 'all' }">
          <text class="filter-text">All Time</text>
        </touchable-opacity>
      </view>
    </view>

    <scroll-view class="records-container" v-if="!loading">
      <view v-if="attendanceRecords.length === 0" class="no-records">
        <text class="no-records-text">No attendance records found</text>
      </view>
      
      <view v-else>
        <view v-for="record in attendanceRecords" :key="record.id" class="record-card">
          <view class="record-header">
            <text class="record-date">{{ formatDate(record.date) }}</text>
            <view class="status-badge" :class="record.status">
              <text class="status-text">{{ record.status.toUpperCase() }}</text>
            </view>
          </view>
          
          <view class="record-details">
            <view class="time-row">
              <text class="time-label">Check-in:</text>
              <text class="time-value">{{ record.check_in ? formatTime(record.check_in) : '--' }}</text>
            </view>
            <view class="time-row">
              <text class="time-label">Check-out:</text>
              <text class="time-value">{{ record.check_out ? formatTime(record.check_out) : '--' }}</text>
            </view>
            <view v-if="record.work_hours" class="time-row">
              <text class="time-label">Work Hours:</text>
              <text class="time-value">{{ record.work_hours }}h</text>
            </view>
          </view>
          
          <view v-if="record.location_in || record.location_out" class="location-info">
            <text class="location-title">Locations:</text>
            <text v-if="record.location_in" class="location-text">In: {{ record.location_in }}</text>
            <text v-if="record.location_out" class="location-text">Out: {{ record.location_out }}</text>
          </view>
        </view>
      </view>
    </scroll-view>

    <view v-if="loading" class="loading-container">
      <text class="loading-text">Loading attendance history...</text>
    </view>

    <view v-if="error" class="error-container">
      <text class="error-text">{{ error }}</text>
      <touchable-opacity @press="loadAttendanceHistory" class="retry-button">
        <text class="retry-text">Retry</text>
      </touchable-opacity>
    </view>
  </view>
</template>

<script>
import ApiService from '../services/ApiService'

export default {
  props: ['user'],
  
  data() {
    return {
      attendanceRecords: [],
      loading: false,
      error: null,
      selectedRange: 'month'
    }
  },

  async mounted() {
    await this.loadAttendanceHistory()
  },

  methods: {
    async loadAttendanceHistory() {
      if (!this.user?.employee?.id) {
        this.error = 'Employee information not found'
        return
      }

      this.loading = true
      this.error = null

      try {
        const { startDate, endDate } = this.getDateRange()
        const response = await ApiService.getAttendanceHistory(
          this.user.employee.id,
          startDate,
          endDate
        )

        if (response.success) {
          this.attendanceRecords = response.data.attendances.data || []
        } else {
          this.error = response.message || 'Failed to load attendance history'
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Network error. Please try again.'
      } finally {
        this.loading = false
      }
    },

    getDateRange() {
      const today = new Date()
      let startDate = null
      let endDate = today.toISOString().split('T')[0]

      switch (this.selectedRange) {
        case 'week':
          const weekStart = new Date(today)
          weekStart.setDate(today.getDate() - today.getDay())
          startDate = weekStart.toISOString().split('T')[0]
          break
        case 'month':
          const monthStart = new Date(today.getFullYear(), today.getMonth(), 1)
          startDate = monthStart.toISOString().split('T')[0]
          break
        case 'all':
        default:
          startDate = null
          endDate = null
          break
      }

      return { startDate, endDate }
    },

    async setDateRange(range) {
      this.selectedRange = range
      await this.loadAttendanceHistory()
    },

    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      })
    },

    formatTime(timeString) {
      if (!timeString) return ''
      const date = new Date(timeString)
      return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      })
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

.filter-container {
  padding: 15px 20px;
  background-color: white;
  border-bottom-width: 1px;
  border-bottom-color: #e9ecef;
}

.date-filters {
  flex-direction: row;
  justify-content: space-around;
}

.filter-button {
  padding: 8px 16px;
  border-radius: 20px;
  background-color: #e9ecef;
}

.filter-button.active {
  background-color: #007bff;
}

.filter-text {
  color: #6c757d;
  font-weight: 500;
}

.filter-button.active .filter-text {
  color: white;
}

.records-container {
  flex: 1;
  padding: 10px;
}

.no-records {
  align-items: center;
  padding: 50px 20px;
}

.no-records-text {
  font-size: 16px;
  color: #6c757d;
  font-style: italic;
}

.record-card {
  background-color: white;
  margin: 10px;
  padding: 15px;
  border-radius: 10px;
  shadow-color: #000;
  shadow-offset: { width: 0, height: 2 };
  shadow-opacity: 0.1;
  shadow-radius: 4;
  elevation: 3;
}

.record-header {
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.record-date {
  font-size: 16px;
  font-weight: bold;
  color: #2c3e50;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 12px;
}

.status-badge.present {
  background-color: #d4edda;
}

.status-badge.absent {
  background-color: #f8d7da;
}

.status-badge.late {
  background-color: #fff3cd;
}

.status-text {
  font-size: 12px;
  font-weight: bold;
}

.status-badge.present .status-text {
  color: #155724;
}

.status-badge.absent .status-text {
  color: #721c24;
}

.status-badge.late .status-text {
  color: #856404;
}

.record-details {
  gap: 5px;
}

.time-row {
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}

.time-label {
  font-size: 14px;
  color: #6c757d;
}

.time-value {
  font-size: 14px;
  color: #2c3e50;
  font-weight: 500;
}

.location-info {
  margin-top: 10px;
  padding-top: 10px;
  border-top-width: 1px;
  border-top-color: #e9ecef;
}

.location-title {
  font-size: 14px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 5px;
}

.location-text {
  font-size: 12px;
  color: #6c757d;
  margin-bottom: 2px;
}

.loading-container, .error-container {
  align-items: center;
  padding: 50px 20px;
}

.loading-text, .error-text {
  font-size: 16px;
  color: #6c757d;
  text-align: center;
}

.retry-button {
  margin-top: 15px;
  padding: 10px 20px;
  background-color: #007bff;
  border-radius: 5px;
}

.retry-text {
  color: white;
  font-weight: bold;
}
</style>