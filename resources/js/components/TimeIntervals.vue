<template>
  <div class="time-intervals-container">
    <div class="page-header mb-4">
      <h2>Time Intervals</h2>
      <p class="text-muted">Configure work time periods and overtime rules</p>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Time Intervals</h5>
            <button @click="showCreateModal = true" class="btn btn-primary btn-sm">
              <i class="fa fa-plus"></i> Create Time Interval
            </button>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-4">
              <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>

            <div v-else-if="$store.getters.allTimeIntervals.length === 0" class="text-center py-4">
              <i class="fa fa-hourglass fa-3x text-muted mb-3"></i>
              <h5>No time intervals configured</h5>
              <p class="text-muted">Create time intervals to define work periods</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Start Time</th>
                    <th>Duration</th>
                    <th>Work Hours</th>
                    <th>Status</th>
                    <th>Overtime</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="interval in $store.getters.allTimeIntervals" :key="interval.id">
                    <td>
                      <strong>{{ interval.alias }}</strong>
                    </td>
                    <td>{{ formatTime(interval.in_time) }}</td>
                    <td>{{ interval.duration }} minutes</td>
                    <td>{{ formatHours(interval.duration) }} hours</td>
                    <td>
                      <span 
                        class="badge"
                        :class="interval.use_mode > 0 ? 'bg-success' : 'bg-secondary'"
                      >
                        {{ interval.use_mode > 0 ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td>
                      <span 
                        class="badge"
                        :class="interval.enable_overtime ? 'bg-info' : 'bg-secondary'"
                      >
                        {{ interval.enable_overtime ? 'Enabled' : 'Disabled' }}
                      </span>
                    </td>
                    <td>
                      <div class="btn-group" role="group">
                        <button 
                          @click="toggleInterval(interval)"
                          class="btn btn-sm"
                          :class="interval.use_mode > 0 ? 'btn-outline-warning' : 'btn-outline-success'"
                          :title="interval.use_mode > 0 ? 'Deactivate' : 'Activate'"
                        >
                          <i :class="interval.use_mode > 0 ? 'fa fa-pause' : 'fa fa-play'"></i>
                        </button>
                        <button 
                          @click="editInterval(interval)"
                          class="btn btn-sm btn-outline-primary"
                          title="Edit"
                        >
                          <i class="fa fa-edit"></i>
                        </button>
                        <button 
                          @click="deleteInterval(interval)"
                          class="btn btn-sm btn-outline-danger"
                          title="Delete"
                        >
                          <i class="fa fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal" class="modal-overlay" @click="closeCreateModal">
      <div class="modal-content modal-lg" @click.stop>
        <div class="modal-header">
          <h5>{{ editingInterval ? 'Edit Time Interval' : 'Create New Time Interval' }}</h5>
          <button @click="closeCreateModal" class="close-btn">&times;</button>
        </div>
        <form @submit.prevent="saveInterval">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Name *</label>
                  <input 
                    v-model="intervalForm.alias" 
                    type="text" 
                    class="form-control" 
                    required 
                    placeholder="e.g., Morning Shift"
                  >
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Start Time *</label>
                  <input 
                    v-model="intervalForm.in_time" 
                    type="time" 
                    class="form-control" 
                    required
                  >
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Duration (minutes) *</label>
                  <input 
                    v-model="intervalForm.duration" 
                    type="number" 
                    class="form-control" 
                    min="1" 
                    max="1440"
                    required
                  >
                  <small class="form-text text-muted">{{ formatHours(intervalForm.duration) }} hours</small>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Early Clock-in (minutes)</label>
                  <input 
                    v-model="intervalForm.in_ahead_margin" 
                    type="number" 
                    class="form-control" 
                    min="0"
                  >
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Late Clock-in Grace (minutes)</label>
                  <input 
                    v-model="intervalForm.in_above_margin" 
                    type="number" 
                    class="form-control" 
                    min="0"
                  >
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Early Clock-out (minutes)</label>
                  <input 
                    v-model="intervalForm.out_ahead_margin" 
                    type="number" 
                    class="form-control" 
                    min="0"
                  >
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Late Clock-out (minutes)</label>
                  <input 
                    v-model="intervalForm.out_above_margin" 
                    type="number" 
                    class="form-control" 
                    min="0"
                  >
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Late Tolerance (minutes)</label>
                  <input 
                    v-model="intervalForm.allow_late" 
                    type="number" 
                    class="form-control" 
                    min="0"
                  >
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Early Leave Tolerance (minutes)</label>
                  <input 
                    v-model="intervalForm.allow_leave_early" 
                    type="number" 
                    class="form-control" 
                    min="0"
                  >
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Work Day Credit</label>
                  <input 
                    v-model="intervalForm.work_day" 
                    type="number" 
                    class="form-control" 
                    min="0" 
                    max="1" 
                    step="0.1"
                  >
                  <small class="form-text text-muted">1.0 = Full day, 0.5 = Half day</small>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label>Advanced Options</label>
              <div class="form-check">
                <input 
                  v-model="intervalForm.enable_early_in" 
                  type="checkbox" 
                  class="form-check-input" 
                  id="enable_early_in"
                >
                <label class="form-check-label" for="enable_early_in">
                  Enable early clock-in tracking
                </label>
              </div>
              <div class="form-check">
                <input 
                  v-model="intervalForm.enable_late_out" 
                  type="checkbox" 
                  class="form-check-input" 
                  id="enable_late_out"
                >
                <label class="form-check-label" for="enable_late_out">
                  Enable late clock-out tracking
                </label>
              </div>
              <div class="form-check">
                <input 
                  v-model="intervalForm.enable_overtime" 
                  type="checkbox" 
                  class="form-check-input" 
                  id="enable_overtime"
                >
                <label class="form-check-label" for="enable_overtime">
                  Enable overtime calculation
                </label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" @click="closeCreateModal" class="btn btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              {{ editingInterval ? 'Update' : 'Create' }} Time Interval
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TimeIntervals',
  
  data() {
    return {
      loading: false,
      showCreateModal: false,
      editingInterval: null,
      intervalForm: {
        alias: '',
        in_time: '09:00',
        duration: 480,
        in_ahead_margin: 15,
        in_above_margin: 10,
        out_ahead_margin: 10,
        out_above_margin: 30,
        allow_late: 15,
        allow_leave_early: 10,
        work_day: 1.0,
        enable_early_in: false,
        enable_late_out: false,
        enable_overtime: false
      }
    };
  },
  
  async created() {
    await this.loadData();
  },
  
  methods: {
    async loadData() {
      this.loading = true;
      try {
        await this.$store.dispatch('fetchTimeIntervals');
      } catch (error) {
        this.$toast.error('Error loading time intervals');
      } finally {
        this.loading = false;
      }
    },
    
    formatTime(timeString) {
      if (!timeString) return 'N/A';
      return timeString.substring(0, 5); // HH:MM format
    },
    
    formatHours(minutes) {
      if (!minutes) return '0';
      return (minutes / 60).toFixed(2);
    },
    
    closeCreateModal() {
      this.showCreateModal = false;
      this.editingInterval = null;
      this.resetForm();
    },
    
    resetForm() {
      this.intervalForm = {
        alias: '',
        in_time: '09:00',
        duration: 480,
        in_ahead_margin: 15,
        in_above_margin: 10,
        out_ahead_margin: 10,
        out_above_margin: 30,
        allow_late: 15,
        allow_leave_early: 10,
        work_day: 1.0,
        enable_early_in: false,
        enable_late_out: false,
        enable_overtime: false
      };
    },
    
    editInterval(interval) {
      this.editingInterval = interval;
      this.intervalForm = {
        alias: interval.alias,
        in_time: this.formatTime(interval.in_time),
        duration: interval.duration,
        in_ahead_margin: interval.in_ahead_margin,
        in_above_margin: interval.in_above_margin,
        out_ahead_margin: interval.out_ahead_margin,
        out_above_margin: interval.out_above_margin,
        allow_late: interval.allow_late,
        allow_leave_early: interval.allow_leave_early,
        work_day: interval.work_day,
        enable_early_in: interval.enable_early_in,
        enable_late_out: interval.enable_late_out,
        enable_overtime: interval.enable_overtime
      };
      this.showCreateModal = true;
    },
    
    async saveInterval() {
      try {
        // This would need to be implemented in the API
        this.$toast.info('Time interval save functionality needs API implementation');
        this.closeCreateModal();
      } catch (error) {
        this.$toast.error('Error saving time interval');
      }
    },
    
    async toggleInterval(interval) {
      try {
        // This would need to be implemented in the API
        this.$toast.info('Time interval toggle functionality needs API implementation');
      } catch (error) {
        this.$toast.error('Error toggling time interval');
      }
    },
    
    async deleteInterval(interval) {
      if (!confirm(`Are you sure you want to delete "${interval.alias}"?`)) {
        return;
      }
      
      try {
        // This would need to be implemented in the API
        this.$toast.info('Time interval delete functionality needs API implementation');
      } catch (error) {
        this.$toast.error('Error deleting time interval');
      }
    }
  }
};
</script>

<style scoped>
.time-intervals-container {
  padding: 20px;
}

.page-header h2 {
  color: #2c3e50;
  margin-bottom: 0;
}

.modal-lg {
  max-width: 800px;
}

.btn-group {
  display: flex;
  gap: 2px;
}

.badge {
  font-size: 0.75em;
}

.table th {
  background-color: #f8f9fa;
  border-top: none;
  font-weight: 600;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 20px 0;
  border-bottom: 1px solid #dee2e6;
}

.modal-header h5 {
  margin: 0;
  flex: 1;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  padding: 0;
  margin-left: 15px;
}

.modal-body {
  padding: 20px;
}

.modal-footer {
  padding: 0 20px 20px;
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
}

.form-control {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 14px;
}

.form-check {
  margin-bottom: 10px;
}
</style>