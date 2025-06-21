<template>
  <div class="dashboard-container">
    <div class="page-header mb-4">
      <h2>Dashboard</h2>
      <p class="text-muted">Schedule management overview</p>
    </div>

    <div class="row">
      <!-- Statistics Cards -->
      <div class="col-md-3 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <i class="fa fa-users fa-2x text-primary mb-3"></i>
            <h4 class="card-title">{{ $store.getters.allEmployees.length }}</h4>
            <p class="card-text text-muted">Total Employees</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <i class="fa fa-clock fa-2x text-success mb-3"></i>
            <h4 class="card-title">{{ $store.getters.allShifts.length }}</h4>
            <p class="card-text text-muted">Active Shifts</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <i class="fa fa-calendar fa-2x text-warning mb-3"></i>
            <h4 class="card-title">{{ $store.getters.allSchedules.length }}</h4>
            <p class="card-text text-muted">Total Schedules</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <i class="fa fa-hourglass fa-2x text-info mb-3"></i>
            <h4 class="card-title">{{ $store.getters.allTimeIntervals.length }}</h4>
            <p class="card-text text-muted">Time Intervals</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Quick Actions -->
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <router-link to="/calendar" class="btn btn-primary">
                <i class="fa fa-calendar"></i> Open Shift Calendar
              </router-link>
              <router-link to="/schedules" class="btn btn-outline-primary">
                <i class="fa fa-list"></i> Manage Schedules
              </router-link>
              <router-link to="/shifts" class="btn btn-outline-secondary">
                <i class="fa fa-clock"></i> Configure Shifts
              </router-link>
              <router-link to="/employees" class="btn btn-outline-info">
                <i class="fa fa-users"></i> View Employees
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">Recent Schedules</h5>
          </div>
          <div class="card-body">
            <div v-if="recentSchedules.length === 0" class="text-muted text-center py-3">
              No recent schedules found
            </div>
            <div v-else>
              <div 
                v-for="schedule in recentSchedules" 
                :key="schedule.id"
                class="border-bottom py-2"
              >
                <div class="d-flex justify-content-between">
                  <div>
                    <strong>{{ getEmployeeName(schedule.employee_id) }}</strong>
                    <br>
                    <small class="text-muted">{{ getShiftName(schedule.shift_id) }}</small>
                  </div>
                  <div class="text-end">
                    <small class="text-muted">
                      {{ formatDate(schedule.start_date) }}
                      <br>
                      to {{ formatDate(schedule.end_date) }}
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Current Week Overview -->
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">This Week's Overview</h5>
          </div>
          <div class="card-body">
            <p class="text-muted">{{ $store.getters.currentWeekString }}</p>
            <router-link to="/calendar" class="btn btn-primary">
              <i class="fa fa-calendar"></i> View Full Calendar
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

export default {
  name: 'Dashboard',
  
  computed: {
    recentSchedules() {
      return this.$store.getters.allSchedules
        .slice(0, 5)
        .sort((a, b) => new Date(b.start_date) - new Date(a.start_date));
    }
  },
  
  async created() {
    // Fetch all data for dashboard
    try {
      await Promise.all([
        this.$store.dispatch('fetchEmployees'),
        this.$store.dispatch('fetchShifts'),
        this.$store.dispatch('fetchTimeIntervals'),
        this.$store.dispatch('fetchSchedules')
      ]);
    } catch (error) {
      console.error('Error loading dashboard data:', error);
    }
  },
  
  methods: {
    getEmployeeName(employeeId) {
      const employee = this.$store.getters.allEmployees.find(e => e.id === employeeId);
      return employee ? `${employee.first_name} ${employee.last_name}` : 'Unknown';
    },
    
    getShiftName(shiftId) {
      const shift = this.$store.getters.allShifts.find(s => s.id === shiftId);
      return shift ? shift.alias : 'Unknown';
    },
    
    formatDate(date) {
      return moment(date).format('MMM D');
    }
  }
};
</script>

<style scoped>
.dashboard-container {
  padding: 20px;
}

.page-header h2 {
  color: #2c3e50;
  margin-bottom: 0;
}

.card {
  border: none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  transition: transform 0.2s ease;
}

.card:hover {
  transform: translateY(-2px);
}

.card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
}

.btn {
  transition: all 0.2s ease;
}

.btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.router-link-exact-active {
  text-decoration: none;
}
</style>