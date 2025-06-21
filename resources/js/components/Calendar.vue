<template>
  <div class="calendar-container">
    <div class="page-header mb-4">
      <h2>Shift Calendar</h2>
      <p class="text-muted">Drag and drop schedules to manage employee shifts</p>
    </div>

    <!-- Calendar Controls -->
    <div class="calendar-controls mb-4">
      <div class="week-navigation">
        <button @click="previousWeek" class="btn btn-outline-primary">
          <i class="fa fa-chevron-left"></i> Previous
        </button>
        <div class="current-week">
          {{ $store.getters.currentWeekString }}
        </div>
        <button @click="nextWeek" class="btn btn-outline-primary">
          Next <i class="fa fa-chevron-right"></i>
        </button>
        <button @click="goToToday" class="btn btn-outline-secondary ml-2">
          Today
        </button>
      </div>
      
      <div class="quick-actions">
        <button @click="showAddModal = true" class="btn btn-success">
          <i class="fa fa-plus"></i> Add Schedule
        </button>
        <button @click="refreshCalendar" class="btn btn-outline-secondary">
          <i class="fa fa-refresh"></i> Refresh
        </button>
      </div>
    </div>

    <!-- Calendar Grid -->
    <div class="shift-calendar">
      <div class="calendar-wrapper">
        <div class="calendar-grid">
          <!-- Header Row -->
          <div class="calendar-row">
            <div class="calendar-cell employee-cell day-header">Employee</div>
            <div 
              v-for="day in weekDays" 
              :key="day.date"
              class="calendar-cell day-header"
              :class="{ 
                'weekend': day.isWeekend, 
                'today': day.isToday 
              }"
            >
              <div>{{ day.day }}</div>
              <div class="day-number">{{ day.dayNumber }}</div>
            </div>
          </div>
          
          <!-- Employee Rows -->
          <div 
            v-for="employeeData in calendarData?.employees || []" 
            :key="employeeData.employee.id"
            class="calendar-row"
          >
            <div class="calendar-cell employee-cell">
              <div class="employee-name">{{ employeeData.employee.name }}</div>
              <div class="employee-info">{{ employeeData.employee.emp_code }}</div>
            </div>
            
            <div 
              v-for="day in weekDays" 
              :key="`${employeeData.employee.id}-${day.date}`"
              class="calendar-cell day-cell"
              :data-date="day.date"
              :data-employee-id="employeeData.employee.id"
              @drop="handleDrop($event, day.date, employeeData.employee.id)"
              @dragover.prevent
              @dragenter.prevent="handleDragEnter"
              @dragleave="handleDragLeave"
            >
              <div v-if="!getSchedulesForDay(employeeData, day.date).length" class="empty-day">
                No shifts
              </div>
              
              <div 
                v-for="schedule in getSchedulesForDay(employeeData, day.date)" 
                :key="schedule.id"
                class="shift-block"
                :style="{ background: schedule.shift.color }"
                :draggable="true"
                @dragstart="handleDragStart($event, schedule)"
                @dragend="handleDragEnd"
                @contextmenu.prevent="showContextMenu($event, schedule)"
              >
                <div class="shift-name">{{ schedule.shift.alias }}</div>
                <div class="shift-time">
                  {{ getTimeIntervalsText(schedule.shift.time_intervals) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div class="legend mt-3">
      <strong>Legend:</strong>
      <div 
        v-for="(shift, index) in $store.getters.allShifts" 
        :key="shift.id"
        class="legend-item"
      >
        <div 
          class="legend-color" 
          :style="{ background: getShiftColor(index) }"
        ></div>
        <span>{{ shift.alias }}</span>
      </div>
    </div>

    <!-- Add Schedule Modal -->
    <div v-if="showAddModal" class="modal-overlay" @click="closeAddModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h5>Add New Schedule</h5>
          <button @click="closeAddModal" class="close-btn">&times;</button>
        </div>
        <form @submit.prevent="createSchedule">
          <div class="modal-body">
            <div class="form-group">
              <label>Employee</label>
              <select v-model="newSchedule.employee_id" class="form-control" required>
                <option value="">Select Employee</option>
                <option 
                  v-for="employee in $store.getters.allEmployees" 
                  :key="employee.id"
                  :value="employee.id"
                >
                  {{ employee.first_name }} {{ employee.last_name }} ({{ employee.emp_code || 'N/A' }})
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Shift</label>
              <select v-model="newSchedule.shift_id" class="form-control" required>
                <option value="">Select Shift</option>
                <option 
                  v-for="shift in $store.getters.allShifts" 
                  :key="shift.id"
                  :value="shift.id"
                >
                  {{ shift.alias }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Date</label>
              <input 
                v-model="newSchedule.date" 
                type="date" 
                class="form-control" 
                required
              >
            </div>
            <div class="form-group">
              <label>Duration (days)</label>
              <input 
                v-model="newSchedule.duration" 
                type="number" 
                class="form-control" 
                min="1" 
                max="365"
              >
              <small class="form-text text-muted">Number of consecutive days</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" @click="closeAddModal" class="btn btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              Add Schedule
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Context Menu -->
    <div 
      v-if="contextMenu.show" 
      class="context-menu"
      :style="{ left: contextMenu.x + 'px', top: contextMenu.y + 'px' }"
    >
      <div class="context-menu-item" @click="editSchedule">
        <i class="fa fa-edit"></i> Edit Schedule
      </div>
      <div class="context-menu-item" @click="deleteSchedule">
        <i class="fa fa-trash"></i> Delete Schedule
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

export default {
  name: 'Calendar',
  
  data() {
    return {
      showAddModal: false,
      draggedSchedule: null,
      contextMenu: {
        show: false,
        x: 0,
        y: 0,
        schedule: null
      },
      newSchedule: {
        employee_id: '',
        shift_id: '',
        date: moment().format('YYYY-MM-DD'),
        duration: 1
      }
    };
  },
  
  computed: {
    calendarData() {
      return this.$store.getters.currentWeekData;
    },
    
    weekDays() {
      if (!this.calendarData?.weekDays) return [];
      return this.calendarData.weekDays;
    }
  },
  
  async created() {
    await this.loadCalendarData();
    document.addEventListener('click', this.hideContextMenu);
  },
  
  beforeDestroy() {
    document.removeEventListener('click', this.hideContextMenu);
  },
  
  methods: {
    async loadCalendarData() {
      try {
        await this.$store.dispatch('fetchCalendarData');
      } catch (error) {
        this.$toast.error('Error loading calendar data');
      }
    },
    
    async previousWeek() {
      const newWeek = this.$store.state.currentWeek.clone().subtract(1, 'week');
      this.$store.commit('SET_CURRENT_WEEK', newWeek);
      await this.loadCalendarData();
    },
    
    async nextWeek() {
      const newWeek = this.$store.state.currentWeek.clone().add(1, 'week');
      this.$store.commit('SET_CURRENT_WEEK', newWeek);
      await this.loadCalendarData();
    },
    
    async goToToday() {
      this.$store.commit('SET_CURRENT_WEEK', moment().startOf('week'));
      await this.loadCalendarData();
    },
    
    async refreshCalendar() {
      await this.loadCalendarData();
    },
    
    getSchedulesForDay(employeeData, date) {
      return employeeData.schedules[date] || [];
    },
    
    getTimeIntervalsText(intervals) {
      if (!Array.isArray(intervals) || intervals.length === 0) {
        return 'No time info';
      }
      return intervals.map(interval => 
        `${interval.in_time} (${interval.duration}h)`
      ).join(', ');
    },
    
    getShiftColor(index) {
      const colors = [
        '#3498db', '#e74c3c', '#2ecc71', '#f39c12',
        '#9b59b6', '#1abc9c', '#34495e', '#e67e22'
      ];
      return colors[index % colors.length];
    },
    
    handleDragStart(event, schedule) {
      this.draggedSchedule = schedule;
      event.target.classList.add('dragging');
      event.dataTransfer.effectAllowed = 'move';
    },
    
    handleDragEnd(event) {
      event.target.classList.remove('dragging');
      this.draggedSchedule = null;
    },
    
    handleDragEnter(event) {
      const cell = event.target.closest('.day-cell');
      if (cell) {
        cell.classList.add('drag-over');
      }
    },
    
    handleDragLeave(event) {
      const cell = event.target.closest('.day-cell');
      if (cell && !cell.contains(event.relatedTarget)) {
        cell.classList.remove('drag-over');
      }
    },
    
    async handleDrop(event, newDate, employeeId) {
      const cell = event.target.closest('.day-cell');
      if (cell) {
        cell.classList.remove('drag-over');
      }
      
      if (!this.draggedSchedule) return;
      
      try {
        await this.$store.dispatch('updateSchedule', {
          scheduleId: this.draggedSchedule.id,
          newDate: newDate,
          employeeId: employeeId
        });
        
        await this.loadCalendarData();
        this.$toast.success('Schedule updated successfully');
      } catch (error) {
        this.$toast.error('Error updating schedule');
        await this.loadCalendarData(); // Refresh to reset visual state
      }
    },
    
    showContextMenu(event, schedule) {
      this.contextMenu = {
        show: true,
        x: event.pageX,
        y: event.pageY,
        schedule: schedule
      };
    },
    
    hideContextMenu() {
      this.contextMenu.show = false;
    },
    
    editSchedule() {
      // TODO: Implement edit functionality
      this.$toast.info('Edit functionality coming soon');
      this.hideContextMenu();
    },
    
    async deleteSchedule() {
      if (!confirm('Are you sure you want to delete this schedule?')) {
        this.hideContextMenu();
        return;
      }
      
      try {
        await this.$store.dispatch('deleteSchedule', this.contextMenu.schedule.id);
        await this.loadCalendarData();
        this.$toast.success('Schedule deleted successfully');
      } catch (error) {
        this.$toast.error('Error deleting schedule');
      }
      
      this.hideContextMenu();
    },
    
    closeAddModal() {
      this.showAddModal = false;
      this.newSchedule = {
        employee_id: '',
        shift_id: '',
        date: moment().format('YYYY-MM-DD'),
        duration: 1
      };
    },
    
    async createSchedule() {
      try {
        await this.$store.dispatch('createSchedule', this.newSchedule);
        await this.loadCalendarData();
        this.closeAddModal();
        this.$toast.success('Schedule created successfully');
      } catch (error) {
        this.$toast.error('Error creating schedule');
      }
    }
  }
};
</script>

<style scoped>
.calendar-container {
  padding: 20px;
}

.page-header h2 {
  color: #2c3e50;
  margin-bottom: 0;
}

.calendar-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
}

.week-navigation {
  display: flex;
  align-items: center;
  gap: 15px;
}

.current-week {
  font-weight: 600;
  font-size: 16px;
  min-width: 200px;
  text-align: center;
}

.quick-actions {
  display: flex;
  gap: 10px;
}

.shift-calendar {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
}

.calendar-wrapper {
  overflow-x: auto;
}

.calendar-grid {
  display: table;
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

.calendar-row {
  display: table-row;
}

.calendar-cell {
  display: table-cell;
  border: 1px solid #dee2e6;
  min-height: 80px;
  vertical-align: top;
  position: relative;
  padding: 4px;
}

.employee-cell {
  width: 200px;
  background: #f8f9fa;
  padding: 15px 10px;
  font-weight: 600;
  border-right: 2px solid #dee2e6;
}

.day-cell {
  width: calc((100% - 200px) / 7);
  padding: 5px;
  min-height: 80px;
  transition: background-color 0.2s ease;
}

.day-header {
  text-align: center;
  padding: 15px 5px;
  background: #6c757d;
  color: white;
  font-weight: 600;
}

.day-header.weekend {
  background: #dc3545;
}

.day-header.today {
  background: #28a745;
}

.day-number {
  font-size: 12px;
  font-weight: normal;
}

.employee-name {
  font-size: 14px;
}

.employee-info {
  font-size: 12px;
  color: #6c757d;
  margin-top: 4px;
  font-weight: normal;
}

.shift-block {
  background: #3498db;
  color: white;
  padding: 4px 8px;
  margin: 2px 0;
  border-radius: 4px;
  font-size: 11px;
  cursor: move;
  position: relative;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
  transition: all 0.2s ease;
  user-select: none;
}

.shift-block:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

.shift-block.dragging {
  opacity: 0.7;
  transform: rotate(5deg) scale(1.05);
  z-index: 1000;
}

.shift-name {
  font-weight: 600;
}

.shift-time {
  font-size: 10px;
  opacity: 0.9;
  margin-top: 2px;
}

.day-cell.drag-over {
  background: #e3f2fd !important;
  border: 2px dashed #2196f3 !important;
}

.empty-day {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6c757d;
  font-style: italic;
  font-size: 12px;
  text-align: center;
  min-height: 60px;
}

.legend {
  display: flex;
  gap: 15px;
  align-items: center;
  padding: 10px;
  background: #f8f9fa;
  border-radius: 4px;
  flex-wrap: wrap;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
}

.legend-color {
  width: 16px;
  height: 16px;
  border-radius: 3px;
  border: 1px solid rgba(0,0,0,0.1);
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
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: between;
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

.context-menu {
  position: absolute;
  background: white;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  z-index: 1000;
  min-width: 150px;
}

.context-menu-item {
  padding: 8px 15px;
  cursor: pointer;
  border-bottom: 1px solid #eee;
  display: flex;
  align-items: center;
  gap: 8px;
}

.context-menu-item:hover {
  background: #f8f9fa;
}

.context-menu-item:last-child {
  border-bottom: none;
}

@media (max-width: 768px) {
  .calendar-controls {
    flex-direction: column;
    align-items: stretch;
  }
  
  .week-navigation {
    justify-content: center;
  }
  
  .quick-actions {
    justify-content: center;
  }
  
  .employee-cell {
    width: 150px;
    font-size: 12px;
  }
  
  .shift-block {
    font-size: 10px;
    padding: 3px 6px;
  }
}
</style>