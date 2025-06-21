<template>
  <div class="employees-container">
    <div class="page-header mb-4">
      <h2>Employee Management</h2>
      <p class="text-muted">View and manage employee information</p>
    </div>

    <div class="row mb-4">
      <div class="col-md-8">
        <div class="input-group">
          <input 
            v-model="searchTerm" 
            type="text" 
            class="form-control" 
            placeholder="Search employees..."
          >
          <button class="btn btn-outline-secondary" type="button">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </div>
      <div class="col-md-4 text-end">
        <button class="btn btn-primary" disabled>
          <i class="fa fa-plus"></i> Add Employee
        </button>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-4">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <div v-else-if="filteredEmployees.length === 0" class="text-center py-4">
          <i class="fa fa-users fa-3x text-muted mb-3"></i>
          <h5>No employees found</h5>
          <p class="text-muted">No employees match your search criteria</p>
        </div>

        <div v-else class="row">
          <div 
            v-for="employee in filteredEmployees" 
            :key="employee.id"
            class="col-md-6 col-lg-4 mb-4"
          >
            <div class="card employee-card h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <div class="avatar-circle me-3">
                    <i class="fa fa-user"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="card-title mb-1">
                      {{ employee.first_name }} {{ employee.last_name }}
                    </h6>
                    <small class="text-muted">{{ employee.emp_code || 'No Code' }}</small>
                  </div>
                </div>
                
                <div class="employee-details">
                  <div class="detail-row">
                    <i class="fa fa-envelope text-muted me-2"></i>
                    <span>{{ employee.email || 'No email' }}</span>
                  </div>
                  
                  <div class="detail-row">
                    <i class="fa fa-phone text-muted me-2"></i>
                    <span>{{ employee.mobile || 'No phone' }}</span>
                  </div>
                  
                  <div class="detail-row">
                    <i class="fa fa-building text-muted me-2"></i>
                    <span>{{ getDepartmentName(employee.department_id) }}</span>
                  </div>
                  
                  <div class="detail-row">
                    <i class="fa fa-briefcase text-muted me-2"></i>
                    <span>{{ getPositionName(employee.position_id) }}</span>
                  </div>
                  
                  <div class="detail-row">
                    <i class="fa fa-calendar text-muted me-2"></i>
                    <span>{{ formatDate(employee.hire_date) }}</span>
                  </div>
                </div>
                
                <div class="mt-3">
                  <span 
                    class="badge"
                    :class="employee.is_active ? 'bg-success' : 'bg-secondary'"
                  >
                    {{ employee.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </div>
              </div>
              <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-between">
                  <button 
                    @click="viewSchedules(employee)"
                    class="btn btn-sm btn-outline-primary"
                  >
                    <i class="fa fa-calendar"></i> Schedules
                  </button>
                  <button 
                    class="btn btn-sm btn-outline-secondary"
                    disabled
                  >
                    <i class="fa fa-edit"></i> Edit
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

export default {
  name: 'Employees',
  
  data() {
    return {
      loading: false,
      searchTerm: ''
    };
  },
  
  computed: {
    filteredEmployees() {
      if (!this.searchTerm) {
        return this.$store.getters.allEmployees;
      }
      
      const term = this.searchTerm.toLowerCase();
      return this.$store.getters.allEmployees.filter(employee => {
        const fullName = `${employee.first_name} ${employee.last_name}`.toLowerCase();
        const empCode = (employee.emp_code || '').toLowerCase();
        const email = (employee.email || '').toLowerCase();
        
        return fullName.includes(term) || 
               empCode.includes(term) || 
               email.includes(term);
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
        await this.$store.dispatch('fetchEmployees');
      } catch (error) {
        this.$toast.error('Error loading employees');
      } finally {
        this.loading = false;
      }
    },
    
    getDepartmentName(departmentId) {
      // This would need department data from the store
      return departmentId ? `Department ${departmentId}` : 'No Department';
    },
    
    getPositionName(positionId) {
      // This would need position data from the store
      return positionId ? `Position ${positionId}` : 'No Position';
    },
    
    formatDate(date) {
      if (!date) return 'Not specified';
      return moment(date).format('MMM D, YYYY');
    },
    
    viewSchedules(employee) {
      // Navigate to schedules filtered by this employee
      this.$router.push({
        name: 'schedules',
        query: { employee: employee.id }
      });
    }
  }
};
</script>

<style scoped>
.employees-container {
  padding: 20px;
}

.page-header h2 {
  color: #2c3e50;
  margin-bottom: 0;
}

.employee-card {
  border: none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  transition: transform 0.2s ease;
}

.employee-card:hover {
  transform: translateY(-2px);
}

.avatar-circle {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 18px;
}

.employee-details {
  font-size: 14px;
}

.detail-row {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
  word-break: break-word;
}

.detail-row i {
  width: 20px;
  flex-shrink: 0;
}

.card-footer {
  border-top: 1px solid #dee2e6;
}

.badge {
  font-size: 0.75em;
}

@media (max-width: 768px) {
  .col-md-6 {
    margin-bottom: 1rem;
  }
}
</style>