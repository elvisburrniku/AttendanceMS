<template>
  <div class="shifts-container">
    <div class="page-header mb-4">
      <h2>Shift Management</h2>
      <p class="text-muted">Configure work shifts and time intervals</p>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Available Shifts</h5>
            <button @click="showCreateModal = true" class="btn btn-primary btn-sm">
              <i class="fa fa-plus"></i> Create Shift
            </button>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-4">
              <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>

            <div v-else-if="$store.getters.allShifts.length === 0" class="text-center py-4">
              <i class="fa fa-clock fa-3x text-muted mb-3"></i>
              <h5>No shifts configured</h5>
              <p class="text-muted">Create your first shift to get started</p>
            </div>

            <div v-else class="row">
              <div 
                v-for="shift in $store.getters.allShifts" 
                :key="shift.id"
                class="col-md-6 col-lg-4 mb-4"
              >
                <div class="card shift-card h-100">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <h6 class="card-title mb-0">{{ shift.alias }}</h6>
                      <div class="dropdown">
                        <button 
                          class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                          type="button" 
                          :id="`dropdownShift${shift.id}`"
                          data-bs-toggle="dropdown"
                        >
                          <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li>
                            <a class="dropdown-item" href="#" @click.prevent="editShift(shift)">
                              <i class="fa fa-edit"></i> Edit
                            </a>
                          </li>
                          <li>
                            <a class="dropdown-item text-danger" href="#" @click.prevent="deleteShift(shift)">
                              <i class="fa fa-trash"></i> Delete
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    
                    <div class="mb-3">
                      <small class="text-muted">Cycle:</small>
                      <span class="ms-1">{{ shift.cycle_unit }} days</span>
                    </div>
                    
                    <div class="mb-3">
                      <small class="text-muted">Weekend Work:</small>
                      <span 
                        class="badge ms-1"
                        :class="shift.work_weekend ? 'bg-success' : 'bg-secondary'"
                      >
                        {{ shift.work_weekend ? 'Yes' : 'No' }}
                      </span>
                    </div>
                    
                    <div class="mb-3">
                      <small class="text-muted">Overtime:</small>
                      <span 
                        class="badge ms-1"
                        :class="shift.enable_ot_rule ? 'bg-info' : 'bg-secondary'"
                      >
                        {{ shift.enable_ot_rule ? 'Enabled' : 'Disabled' }}
                      </span>
                    </div>
                    
                    <div class="time-intervals">
                      <small class="text-muted">Time Intervals:</small>
                      <div v-if="shift.time_intervals && shift.time_intervals.length > 0" class="mt-1">
                        <div 
                          v-for="interval in shift.time_intervals" 
                          :key="interval.id"
                          class="badge bg-light text-dark me-1 mb-1"
                        >
                          {{ interval.alias }}
                        </div>
                      </div>
                      <div v-else class="mt-1">
                        <span class="text-muted">No intervals assigned</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal" class="modal-overlay" @click="closeCreateModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h5>{{ editingShift ? 'Edit Shift' : 'Create New Shift' }}</h5>
          <button @click="closeCreateModal" class="close-btn">&times;</button>
        </div>
        <form @submit.prevent="saveShift">
          <div class="modal-body">
            <div class="form-group">
              <label>Shift Name *</label>
              <input 
                v-model="shiftForm.alias" 
                type="text" 
                class="form-control" 
                required 
                placeholder="e.g., Day Shift"
              >
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Cycle Unit (Days) *</label>
                  <select v-model="shiftForm.cycle_unit" class="form-control" required>
                    <option value="1">Daily</option>
                    <option value="7">Weekly</option>
                    <option value="14">Bi-weekly</option>
                    <option value="30">Monthly</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Shift Cycle *</label>
                  <input 
                    v-model="shiftForm.shift_cycle" 
                    type="number" 
                    class="form-control" 
                    min="1" 
                    required
                  >
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <div class="form-check">
                <input 
                  v-model="shiftForm.work_weekend" 
                  type="checkbox" 
                  class="form-check-input" 
                  id="work_weekend"
                >
                <label class="form-check-label" for="work_weekend">
                  Work on Weekends
                </label>
              </div>
            </div>
            
            <div class="form-group">
              <div class="form-check">
                <input 
                  v-model="shiftForm.enable_ot_rule" 
                  type="checkbox" 
                  class="form-check-input" 
                  id="enable_ot_rule"
                >
                <label class="form-check-label" for="enable_ot_rule">
                  Enable Overtime Rules
                </label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" @click="closeCreateModal" class="btn btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              {{ editingShift ? 'Update' : 'Create' }} Shift
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Shifts',
  
  data() {
    return {
      loading: false,
      showCreateModal: false,
      editingShift: null,
      shiftForm: {
        alias: '',
        cycle_unit: 7,
        shift_cycle: 1,
        work_weekend: false,
        enable_ot_rule: false
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
        await this.$store.dispatch('fetchShifts');
      } catch (error) {
        this.$toast.error('Error loading shifts');
      } finally {
        this.loading = false;
      }
    },
    
    closeCreateModal() {
      this.showCreateModal = false;
      this.editingShift = null;
      this.resetForm();
    },
    
    resetForm() {
      this.shiftForm = {
        alias: '',
        cycle_unit: 7,
        shift_cycle: 1,
        work_weekend: false,
        enable_ot_rule: false
      };
    },
    
    editShift(shift) {
      this.editingShift = shift;
      this.shiftForm = {
        alias: shift.alias,
        cycle_unit: shift.cycle_unit,
        shift_cycle: shift.shift_cycle,
        work_weekend: shift.work_weekend,
        enable_ot_rule: shift.enable_ot_rule
      };
      this.showCreateModal = true;
    },
    
    async saveShift() {
      try {
        // This would need to be implemented in the API
        this.$toast.info('Shift save functionality needs API implementation');
        this.closeCreateModal();
      } catch (error) {
        this.$toast.error('Error saving shift');
      }
    },
    
    async deleteShift(shift) {
      if (!confirm(`Are you sure you want to delete "${shift.alias}"?`)) {
        return;
      }
      
      try {
        // This would need to be implemented in the API
        this.$toast.info('Shift delete functionality needs API implementation');
      } catch (error) {
        this.$toast.error('Error deleting shift');
      }
    }
  }
};
</script>

<style scoped>
.shifts-container {
  padding: 20px;
}

.page-header h2 {
  color: #2c3e50;
  margin-bottom: 0;
}

.shift-card {
  border: none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  transition: transform 0.2s ease;
}

.shift-card:hover {
  transform: translateY(-2px);
}

.time-intervals .badge {
  font-size: 0.7em;
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
  max-width: 600px;
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