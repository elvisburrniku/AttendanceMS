import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuex from 'vuex';
import axios from 'axios';
import moment from 'moment';
import VueMoment from 'vue-moment';
import draggable from 'vue-draggable';

// Define components inline to avoid Vue loader issues
const Dashboard = {
    template: `
        <div class="dashboard-container">
            <div class="page-header mb-4">
                <h2>Dashboard</h2>
                <p class="text-muted">Schedule management overview</p>
            </div>
            <div class="row">
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
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Current Week</h5>
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
    `,
    async created() {
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
    }
};

const Calendar = {
    template: `
        <div class="calendar-container">
            <div class="page-header mb-4">
                <h2>Shift Calendar</h2>
                <p class="text-muted">Drag and drop schedules to manage employee shifts</p>
            </div>
            <div class="calendar-controls mb-4">
                <div class="week-navigation">
                    <button @click="previousWeek" class="btn btn-outline-primary">
                        <i class="fa fa-chevron-left"></i> Previous
                    </button>
                    <div class="current-week">{{ $store.getters.currentWeekString }}</div>
                    <button @click="nextWeek" class="btn btn-outline-primary">
                        Next <i class="fa fa-chevron-right"></i>
                    </button>
                    <button @click="goToToday" class="btn btn-outline-secondary ms-2">Today</button>
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
            <div class="shift-calendar">
                <div class="calendar-wrapper">
                    <div class="calendar-grid">
                        <div class="calendar-row">
                            <div class="calendar-cell employee-cell day-header">Employee</div>
                            <div v-for="day in weekDays" :key="day.date" class="calendar-cell day-header" 
                                 :class="{ 'weekend': day.isWeekend, 'today': day.isToday }">
                                <div>{{ day.day }}</div>
                                <div class="day-number">{{ day.dayNumber }}</div>
                            </div>
                        </div>
                        <div v-for="employeeData in calendarData?.employees || []" :key="employeeData.employee.id" class="calendar-row">
                            <div class="calendar-cell employee-cell">
                                <div class="employee-name">{{ employeeData.employee.name }}</div>
                                <div class="employee-info">{{ employeeData.employee.emp_code }}</div>
                            </div>
                            <div v-for="day in weekDays" :key="employeeData.employee.id + '-' + day.date" 
                                 class="calendar-cell day-cell" :data-date="day.date" :data-employee-id="employeeData.employee.id"
                                 @drop="handleDrop($event, day.date, employeeData.employee.id)" @dragover.prevent @dragenter.prevent="handleDragEnter" @dragleave="handleDragLeave">
                                <div v-if="!getSchedulesForDay(employeeData, day.date).length" class="empty-day">No shifts</div>
                                <div v-for="schedule in getSchedulesForDay(employeeData, day.date)" :key="schedule.id" class="shift-block"
                                     :draggable="true" @dragstart="handleDragStart($event, schedule)" @dragend="handleDragEnd">
                                    <div class="shift-name">{{ schedule.shift.alias }}</div>
                                    <div class="shift-time">{{ getTimeIntervalsText(schedule.shift.time_intervals) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                    <option v-for="employee in $store.getters.allEmployees" :key="employee.id" :value="employee.id">
                                        {{ employee.first_name }} {{ employee.last_name }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Shift</label>
                                <select v-model="newSchedule.shift_id" class="form-control" required>
                                    <option value="">Select Shift</option>
                                    <option v-for="shift in $store.getters.allShifts" :key="shift.id" :value="shift.id">
                                        {{ shift.alias }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <input v-model="newSchedule.date" type="date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Duration (days)</label>
                                <input v-model="newSchedule.duration" type="number" class="form-control" min="1" max="365">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="closeAddModal" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Schedule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `,
    data() {
        return {
            showAddModal: false,
            draggedSchedule: null,
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
            return this.calendarData?.weekDays || [];
        }
    },
    async created() {
        await this.loadCalendarData();
    },
    methods: {
        async loadCalendarData() {
            try {
                await this.$store.dispatch('fetchCalendarData');
            } catch (error) {
                showToast('Error loading calendar data', 'danger');
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
            return intervals.map(interval => interval.in_time + ' (' + interval.duration + 'h)').join(', ');
        },
        handleDragStart(event, schedule) {
            this.draggedSchedule = schedule;
            event.target.classList.add('dragging');
        },
        handleDragEnd(event) {
            event.target.classList.remove('dragging');
            this.draggedSchedule = null;
        },
        handleDragEnter(event) {
            const cell = event.target.closest('.day-cell');
            if (cell) cell.classList.add('drag-over');
        },
        handleDragLeave(event) {
            const cell = event.target.closest('.day-cell');
            if (cell && !cell.contains(event.relatedTarget)) {
                cell.classList.remove('drag-over');
            }
        },
        async handleDrop(event, newDate, employeeId) {
            const cell = event.target.closest('.day-cell');
            if (cell) cell.classList.remove('drag-over');
            
            if (!this.draggedSchedule) return;
            
            try {
                await this.$store.dispatch('updateSchedule', {
                    scheduleId: this.draggedSchedule.id,
                    newDate: newDate,
                    employeeId: employeeId
                });
                await this.loadCalendarData();
                showToast('Schedule updated successfully', 'success');
            } catch (error) {
                showToast('Error updating schedule', 'danger');
                await this.loadCalendarData();
            }
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
                showToast('Schedule created successfully', 'success');
            } catch (error) {
                showToast('Error creating schedule', 'danger');
            }
        }
    }
};

const Schedules = {
    template: `
        <div class="schedules-container">
            <div class="page-header mb-4">
                <h2>Schedule Management</h2>
                <p class="text-muted">Manage employee schedules and assignments</p>
            </div>
            <div class="card">
                <div class="card-body">
                    <div v-if="loading" class="text-center py-4">
                        <div class="spinner-border" role="status"></div>
                    </div>
                    <div v-else-if="$store.getters.allSchedules.length === 0" class="text-center py-4">
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="schedule in $store.getters.allSchedules" :key="schedule.id">
                                    <td>{{ getEmployeeName(schedule.employee_id) }}</td>
                                    <td><span class="badge bg-info">{{ getShiftName(schedule.shift_id) }}</span></td>
                                    <td>{{ formatDate(schedule.start_date) }}</td>
                                    <td>{{ formatDate(schedule.end_date) }}</td>
                                    <td>
                                        <button @click="deleteSchedule(schedule)" class="btn btn-sm btn-outline-danger">
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
    `,
    data() {
        return { loading: false };
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
                showToast('Error loading data', 'danger');
            } finally {
                this.loading = false;
            }
        },
        getEmployeeName(employeeId) {
            const employee = this.$store.getters.allEmployees.find(e => e.id === employeeId);
            return employee ? employee.first_name + ' ' + employee.last_name : 'Unknown';
        },
        getShiftName(shiftId) {
            const shift = this.$store.getters.allShifts.find(s => s.id === shiftId);
            return shift ? shift.alias : 'Unknown';
        },
        formatDate(date) {
            return moment(date).format('MMM D, YYYY');
        },
        async deleteSchedule(schedule) {
            if (!confirm('Are you sure you want to delete this schedule?')) return;
            try {
                await this.$store.dispatch('deleteSchedule', schedule.id);
                showToast('Schedule deleted successfully', 'success');
            } catch (error) {
                showToast('Error deleting schedule', 'danger');
            }
        }
    }
};

const Shifts = {
    template: `
        <div class="shifts-container">
            <div class="page-header mb-4">
                <h2>Shift Management</h2>
                <p class="text-muted">Configure work shifts and time intervals</p>
            </div>
            <div class="card">
                <div class="card-body">
                    <div v-if="$store.getters.allShifts.length === 0" class="text-center py-4">
                        <i class="fa fa-clock fa-3x text-muted mb-3"></i>
                        <h5>No shifts configured</h5>
                    </div>
                    <div v-else class="row">
                        <div v-for="shift in $store.getters.allShifts" :key="shift.id" class="col-md-6 col-lg-4 mb-4">
                            <div class="card shift-card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">{{ shift.alias }}</h6>
                                    <div class="mb-2">
                                        <small class="text-muted">Cycle:</small>
                                        <span class="ms-1">{{ shift.cycle_unit }} days</span>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Weekend Work:</small>
                                        <span class="badge ms-1" :class="shift.work_weekend ? 'bg-success' : 'bg-secondary'">
                                            {{ shift.work_weekend ? 'Yes' : 'No' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
    async created() {
        await this.$store.dispatch('fetchShifts');
    }
};

const TimeIntervals = {
    template: `
        <div class="time-intervals-container">
            <div class="page-header mb-4">
                <h2>Time Intervals</h2>
                <p class="text-muted">Configure work time periods and overtime rules</p>
            </div>
            <div class="card">
                <div class="card-body">
                    <div v-if="$store.getters.allTimeIntervals.length === 0" class="text-center py-4">
                        <i class="fa fa-hourglass fa-3x text-muted mb-3"></i>
                        <h5>No time intervals configured</h5>
                    </div>
                    <div v-else class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Start Time</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="interval in $store.getters.allTimeIntervals" :key="interval.id">
                                    <td><strong>{{ interval.alias }}</strong></td>
                                    <td>{{ formatTime(interval.in_time) }}</td>
                                    <td>{{ formatHours(interval.duration) }} hours</td>
                                    <td>
                                        <span class="badge" :class="interval.use_mode > 0 ? 'bg-success' : 'bg-secondary'">
                                            {{ interval.use_mode > 0 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `,
    async created() {
        await this.$store.dispatch('fetchTimeIntervals');
    },
    methods: {
        formatTime(timeString) {
            return timeString ? timeString.substring(0, 5) : 'N/A';
        },
        formatHours(minutes) {
            return minutes ? (minutes / 60).toFixed(2) : '0';
        }
    }
};

const Employees = {
    template: `
        <div class="employees-container">
            <div class="page-header mb-4">
                <h2>Employee Management</h2>
                <p class="text-muted">View and manage employee information</p>
            </div>
            <div class="card">
                <div class="card-body">
                    <div v-if="$store.getters.allEmployees.length === 0" class="text-center py-4">
                        <i class="fa fa-users fa-3x text-muted mb-3"></i>
                        <h5>No employees found</h5>
                    </div>
                    <div v-else class="row">
                        <div v-for="employee in $store.getters.allEmployees" :key="employee.id" class="col-md-6 col-lg-4 mb-4">
                            <div class="card employee-card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-circle me-3">
                                            <i class="fa fa-user"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-1">{{ employee.first_name }} {{ employee.last_name }}</h6>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
    async created() {
        await this.$store.dispatch('fetchEmployees');
    }
};

// Use plugins
Vue.use(VueRouter);
Vue.use(Vuex);
Vue.use(VueMoment, { moment });
Vue.directive('draggable', draggable);

// Configure axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content;

Vue.prototype.$http = axios;

// Vuex store
const store = new Vuex.Store({
    state: {
        user: null,
        loading: false,
        employees: [],
        shifts: [],
        timeIntervals: [],
        schedules: [],
        currentWeek: moment().startOf('week'),
        calendarData: null
    },
    
    mutations: {
        SET_LOADING(state, loading) {
            state.loading = loading;
        },
        SET_USER(state, user) {
            state.user = user;
        },
        SET_EMPLOYEES(state, employees) {
            state.employees = employees;
        },
        SET_SHIFTS(state, shifts) {
            state.shifts = shifts;
        },
        SET_TIME_INTERVALS(state, intervals) {
            state.timeIntervals = intervals;
        },
        SET_SCHEDULES(state, schedules) {
            state.schedules = schedules;
        },
        SET_CURRENT_WEEK(state, week) {
            state.currentWeek = moment(week);
        },
        SET_CALENDAR_DATA(state, data) {
            state.calendarData = data;
        },
        ADD_SCHEDULE(state, schedule) {
            state.schedules.push(schedule);
        },
        UPDATE_SCHEDULE(state, updatedSchedule) {
            const index = state.schedules.findIndex(s => s.id === updatedSchedule.id);
            if (index !== -1) {
                Vue.set(state.schedules, index, updatedSchedule);
            }
        },
        REMOVE_SCHEDULE(state, scheduleId) {
            state.schedules = state.schedules.filter(s => s.id !== scheduleId);
        }
    },
    
    actions: {
        async fetchEmployees({ commit }) {
            try {
                const response = await axios.get('/api/employees');
                commit('SET_EMPLOYEES', response.data);
                return response.data;
            } catch (error) {
                console.error('Error fetching employees:', error);
                throw error;
            }
        },
        
        async fetchShifts({ commit }) {
            try {
                const response = await axios.get('/api/shifts');
                commit('SET_SHIFTS', response.data);
                return response.data;
            } catch (error) {
                console.error('Error fetching shifts:', error);
                throw error;
            }
        },
        
        async fetchTimeIntervals({ commit }) {
            try {
                const response = await axios.get('/api/time-intervals');
                commit('SET_TIME_INTERVALS', response.data);
                return response.data;
            } catch (error) {
                console.error('Error fetching time intervals:', error);
                throw error;
            }
        },
        
        async fetchSchedules({ commit }) {
            try {
                const response = await axios.get('/api/schedules');
                commit('SET_SCHEDULES', response.data);
                return response.data;
            } catch (error) {
                console.error('Error fetching schedules:', error);
                throw error;
            }
        },
        
        async fetchCalendarData({ commit, state }, date = null) {
            try {
                const targetDate = date || state.currentWeek.format('YYYY-MM-DD');
                const response = await axios.get('/api/calendar/week-data', {
                    params: { date: targetDate }
                });
                commit('SET_CALENDAR_DATA', response.data.calendarData);
                return response.data;
            } catch (error) {
                console.error('Error fetching calendar data:', error);
                throw error;
            }
        },
        
        async createSchedule({ commit }, scheduleData) {
            try {
                const response = await axios.post('/api/calendar/create', scheduleData);
                commit('ADD_SCHEDULE', response.data.schedule);
                return response.data;
            } catch (error) {
                console.error('Error creating schedule:', error);
                throw error;
            }
        },
        
        async updateSchedule({ commit }, { scheduleId, newDate, employeeId }) {
            try {
                const response = await axios.post('/api/calendar/update', {
                    schedule_id: scheduleId,
                    new_date: newDate,
                    employee_id: employeeId
                });
                commit('UPDATE_SCHEDULE', response.data.schedule);
                return response.data;
            } catch (error) {
                console.error('Error updating schedule:', error);
                throw error;
            }
        },
        
        async deleteSchedule({ commit }, scheduleId) {
            try {
                const response = await axios.post('/api/calendar/delete', {
                    schedule_id: scheduleId
                });
                commit('REMOVE_SCHEDULE', scheduleId);
                return response.data;
            } catch (error) {
                console.error('Error deleting schedule:', error);
                throw error;
            }
        }
    },
    
    getters: {
        isLoading: state => state.loading,
        currentUser: state => state.user,
        allEmployees: state => state.employees,
        allShifts: state => state.shifts,
        allTimeIntervals: state => state.timeIntervals,
        allSchedules: state => state.schedules,
        currentWeekData: state => state.calendarData,
        currentWeekString: state => {
            const start = state.currentWeek.format('MMM D');
            const end = state.currentWeek.clone().endOf('week').format('MMM D, YYYY');
            return `${start} - ${end}`;
        }
    }
});

// Router configuration
const routes = [
    { 
        path: '/', 
        redirect: '/dashboard' 
    },
    { 
        path: '/dashboard', 
        component: Dashboard,
        name: 'dashboard'
    },
    { 
        path: '/calendar', 
        component: Calendar,
        name: 'calendar'
    },
    { 
        path: '/schedules', 
        component: Schedules,
        name: 'schedules'
    },
    { 
        path: '/shifts', 
        component: Shifts,
        name: 'shifts'
    },
    { 
        path: '/time-intervals', 
        component: TimeIntervals,
        name: 'time-intervals'
    },
    { 
        path: '/employees', 
        component: Employees,
        name: 'employees'
    }
];

const router = new VueRouter({
    mode: 'history',
    base: '/app',
    routes
});

// Global loading interceptor
axios.interceptors.request.use(config => {
    store.commit('SET_LOADING', true);
    return config;
});

axios.interceptors.response.use(
    response => {
        store.commit('SET_LOADING', false);
        return response;
    },
    error => {
        store.commit('SET_LOADING', false);
        if (error.response?.status === 401) {
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

// Create Vue app
const app = new Vue({
    router,
    store,
    
    created() {
        // Initialize app data
        this.initializeApp();
    },
    
    methods: {
        async initializeApp() {
            try {
                // Fetch initial data
                await Promise.all([
                    this.$store.dispatch('fetchEmployees'),
                    this.$store.dispatch('fetchShifts'),
                    this.$store.dispatch('fetchTimeIntervals')
                ]);
            } catch (error) {
                console.error('Error initializing app:', error);
            }
        }
    },
    
    template: `
        <div id="app" class="vue-spa">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container-fluid">
                    <router-link to="/dashboard" class="navbar-brand">
                        <i class="fa fa-calendar-alt"></i> Schedule Manager
                    </router-link>
                    
                    <div class="navbar-nav ms-auto">
                        <router-link to="/dashboard" class="nav-link" exact-active-class="active">
                            <i class="fa fa-home"></i> Dashboard
                        </router-link>
                        <router-link to="/calendar" class="nav-link" active-class="active">
                            <i class="fa fa-calendar"></i> Calendar
                        </router-link>
                        <router-link to="/schedules" class="nav-link" active-class="active">
                            <i class="fa fa-list"></i> Schedules
                        </router-link>
                        <router-link to="/shifts" class="nav-link" active-class="active">
                            <i class="fa fa-clock"></i> Shifts
                        </router-link>
                        <router-link to="/time-intervals" class="nav-link" active-class="active">
                            <i class="fa fa-hourglass"></i> Time Intervals
                        </router-link>
                        <router-link to="/employees" class="nav-link" active-class="active">
                            <i class="fa fa-users"></i> Employees
                        </router-link>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid mt-4">
                <div class="loading-overlay" v-if="$store.getters.isLoading">
                    <div class="loading-spinner"></div>
                </div>
                
                <router-view></router-view>
            </div>
        </div>
    `
});

// Mount the app
app.$mount('#app');