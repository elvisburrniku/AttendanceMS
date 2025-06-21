<template>
  <div class="schedules-container">
    <div class="page-header mb-4">
      <h2>Schedule Management</h2>
      <p class="text-muted">Manage employee schedules and assignments</p>
    </div>

    <div class="row mb-4">
      <div class="col-md-8">
        <div class="input-group">
          <input 
            v-model="searchTerm" 
            type="text" 
            class="form-control" 
            placeholder="Search schedules..."
          >
          <button class="btn btn-outline-secondary" type="button">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </div>
      <div class="col-md-4 text-end">
        <router-link to="/calendar" class="btn btn-primary">
          <i class="fa fa-calendar"></i> Calendar View
        </router-link>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-4">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <div v-else-if="filteredSchedules.length === 0" class="text-center py-4">
          <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
          <h5>No schedules found</h5>
          <p class="text-muted">Create schedules using the calendar view</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Shift</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="schedule in filteredSchedules" :key="schedule.id">
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                      <i class="fa fa-user text-white"></i>
                    </div>
                    <div>
                      <strong>{{ getEmployeeName(schedule.employee_id) }}</strong>
                      <br>
                      <small class="text-muted">{{ getEmployeeCode(schedule.employee_id) }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-info">{{ getShiftName(schedule.shift_id) }}</span>
                </td>
                <td>{{ formatDate(schedule.start_date) }}</td>
                <td>{{ formatDate(schedule.end_date) }}</td>
                <td>{{ getDuration(schedule.start_date, schedule.end_date) }} days</td>
                <td>
                  <span 
                    class="badge"
                    :class="getStatusClass(schedule.start_date, schedule.end_date)"
                  >
                    {{ getStatus(schedule.start_date, schedule.end_date) }}
                  </span>
                </td>
                <td>
                  <button 
                    @click="deleteSchedule(schedule)" 
                    class="btn btn-sm btn-outline-danger"
                    title="Delete Schedule"
                  >
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

export default {
  name: 'Schedules',
  
  data() {
    return {
      searchTerm: '',
      loading: false
    };
  },
  
  computed: {
    filteredSchedules() {
      if (!this.searchTerm) {
        return this.$store.getters.allSchedules;
      }
      
      const term = this.searchTerm.toLowerCase();
      return this.$store.getters.allSchedules.filter(schedule => {
        const employeeName = this.getEmployeeName(schedule.employee_id).toLowerCase();
        const shiftName = this.getShiftName(schedule.shift_id).toLowerCase();
        return employeeName.includes(term) || shiftName.includes(term);
      });
    }
  },
  
  async created() {
    await this.loadData();
  },
  
  methods: {
    async loadData() {
      this.loading = true;
      try {
        await Promise.all([
          this.$store.dispatch('fetchSchedules'),
          this.$store.dispatch('fetchEmployees'),
          this.$store.dispatch('fetchShifts')
        ]);
      } catch (error) {
        this.$toast.error('Error loading data');
      } finally {
        this.loading = false;
      }
    },
    
    getEmployeeName(employeeId) {
      const employee = this.$store.getters.allEmployees.find(e => e.id === employeeId);
      return employee ? `${employee.first_name} ${employee.last_name}` : 'Unknown';
    },
    
    getEmployeeCode(employeeId) {
      const employee = this.$store.getters.allEmployees.find(e => e.id === employeeId);
      return employee ? (employee.emp_code || 'N/A') : 'N/A';
    },
    
    getShiftName(shiftId) {
      const shift = this.$store.getters.allShifts.find(s => s.id === shiftId);
      return shift ? shift.alias : 'Unknown';
    },
    
    formatDate(date) {
      return moment(date).format('MMM D, YYYY');
    },
    
    getDuration(startDate, endDate) {
      return moment(endDate).diff(moment(startDate), 'days') + 1;
    },
    
    getStatus(startDate, endDate) {
      const now = moment();
      const start = moment(startDate);
      const end = moment(endDate);
      
      if (now.isBefore(start)) {
        return 'Upcoming';
      } else if (now.isAfter(end)) {
        return 'Completed';
      } else {
        return 'Active';
      }
    },
    
    getStatusClass(startDate, endDate) {
      const status = this.getStatus(startDate, endDate);
      switch (status) {
        case 'Upcoming':
          return 'bg-warning';
        case 'Active':
          return 'bg-success';
        case 'Completed':
          return 'bg-secondary';
        default:
          return 'bg-info';
      }
    },
    
    async deleteSchedule(schedule) {
      if (!confirm(`Are you sure you want to delete the schedule for ${this.getEmployeeName(schedule.employee_id)}?`)) {
        return;
      }
      
      try {
        await this.$store.dispatch('deleteSchedule', schedule.id);
        this.$toast.success('Schedule deleted successfully');
      } catch (error) {
        this.$toast.error('Error deleting schedule');
      }
    }
  }
};
</script>

<style scoped>
.schedules-container {
  padding: 20px;
}

.page-header h2 {
  color: #2c3e50;
  margin-bottom: 0;
}

.avatar-sm {
  width: 32px;
  height: 32px;
  font-size: 12px;
}

.card {
  border: none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.table th {
  background-color: #f8f9fa;
  border-top: none;
  font-weight: 600;
}

.badge {
  font-size: 0.75em;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
}
</style>