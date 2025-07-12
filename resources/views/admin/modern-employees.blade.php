@extends('layouts.modern-master')

@section('title', 'Employees - AttendanceFlow')
@section('page-title', 'Employee Management')

@section('page-actions')
    <a href="{{ route('employees.create') }}" class="action-btn">
        <i class="fas fa-plus"></i>
        Add Employee
    </a>
    <button class="action-btn btn-secondary" onclick="exportEmployees()">
        <i class="fas fa-download"></i>
        Export Data
    </button>
@endsection

@section('css')
<style>
    .employee-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
    }
    
    .employee-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .employee-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
    }
    
    .employee-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-gradient);
    }
    
    .employee-header {
        display: flex;
        align-items: center;
        margin-bottom: var(--spacing-md);
    }
    
    .employee-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.25rem;
        margin-right: var(--spacing-md);
        flex-shrink: 0;
    }
    
    .employee-info h5 {
        margin: 0 0 var(--spacing-xs) 0;
        font-weight: 600;
        color: var(--text-primary);
    }
    
    .employee-info p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }
    
    .employee-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-md);
        margin: var(--spacing-md) 0;
    }
    
    .stat-item {
        text-align: center;
        padding: var(--spacing-sm);
        background: var(--bg-primary);
        border-radius: var(--radius-sm);
    }
    
    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }
    
    .employee-actions {
        display: flex;
        gap: var(--spacing-xs);
        margin-top: var(--spacing-md);
    }
    
    .action-icon {
        width: 32px;
        height: 32px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        font-size: 0.875rem;
    }
    
    .action-edit {
        background: var(--success-gradient);
        color: white;
    }
    
    .action-view {
        background: var(--primary-gradient);
        color: white;
    }
    
    .action-delete {
        background: var(--danger-gradient);
        color: white;
    }
    
    .action-icon:hover {
        transform: scale(1.1);
        color: white;
        text-decoration: none;
    }
    
    .status-badge {
        position: absolute;
        top: var(--spacing-sm);
        right: var(--spacing-sm);
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-lg);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-active {
        background: var(--success-gradient);
        color: white;
    }
    
    .status-inactive {
        background: var(--danger-gradient);
        color: white;
    }
    
    .status-pending {
        background: var(--warning-gradient);
        color: white;
    }
    
    .search-filters {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }
    
    .filter-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: var(--spacing-md);
        align-items: end;
    }
    
    .view-toggle {
        display: flex;
        background: var(--bg-primary);
        border-radius: var(--radius-md);
        padding: 4px;
        margin-bottom: var(--spacing-lg);
    }
    
    .view-btn {
        padding: var(--spacing-xs) var(--spacing-sm);
        border: none;
        background: transparent;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.3s ease;
        color: var(--text-secondary);
        font-weight: 500;
    }
    
    .view-btn.active {
        background: var(--primary-gradient);
        color: white;
        box-shadow: var(--shadow-sm);
    }
    
    @media (max-width: 768px) {
        .employee-grid {
            grid-template-columns: 1fr;
        }
        
        .filter-row {
            grid-template-columns: 1fr;
            gap: var(--spacing-sm);
        }
    }
</style>
@endsection

@section('content')
<div class="employee-management">
    <!-- Search and Filters -->
    <div class="search-filters">
        <div class="filter-row">
            <div class="form-group mb-0">
                <label class="form-label">Search Employees</label>
                <input type="text" 
                       class="form-control" 
                       placeholder="Search by name, email, or department..."
                       id="employeeSearch">
            </div>
            
            <div class="form-group mb-0">
                <label class="form-label">Department</label>
                <select class="form-control" id="departmentFilter">
                    <option value="">All Departments</option>
                    <option value="it">IT Department</option>
                    <option value="marketing">Marketing</option>
                    <option value="sales">Sales</option>
                    <option value="hr">Human Resources</option>
                    <option value="finance">Finance</option>
                </select>
            </div>
            
            <div class="form-group mb-0">
                <label class="form-label">Status</label>
                <select class="form-control" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            
            <button class="btn-modern" onclick="resetFilters()">
                <i class="fas fa-sync-alt"></i>
                Reset
            </button>
        </div>
    </div>
    
    <!-- View Toggle -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="view-toggle">
            <button class="view-btn active" onclick="switchView('grid')" id="gridView">
                <i class="fas fa-th me-2"></i>
                Grid View
            </button>
            <button class="view-btn" onclick="switchView('table')" id="tableView">
                <i class="fas fa-list me-2"></i>
                Table View
            </button>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted">Showing {{ $employees->count() ?? 24 }} of {{ $total ?? 156 }} employees</span>
        </div>
    </div>
    
    <!-- Grid View -->
    <div class="employee-grid" id="gridContainer">
        <!-- Sample Employee Cards -->
        <div class="employee-card">
            <div class="status-badge status-active">Active</div>
            <div class="employee-header">
                <div class="employee-avatar">SJ</div>
                <div class="employee-info">
                    <h5>Sarah Johnson</h5>
                    <p>Senior Developer • IT Department</p>
                </div>
            </div>
            
            <div class="employee-stats">
                <div class="stat-item">
                    <h6 class="stat-value">98%</h6>
                    <p class="stat-label">Attendance</p>
                </div>
                <div class="stat-item">
                    <h6 class="stat-value">2.5y</h6>
                    <p class="stat-label">Experience</p>
                </div>
            </div>
            
            <div class="employee-actions">
                <a href="#" class="action-icon action-view" title="View Profile">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="action-icon action-edit" title="Edit Employee">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="action-icon action-delete" title="Delete Employee" onclick="deleteEmployee(1)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        
        <div class="employee-card">
            <div class="status-badge status-active">Active</div>
            <div class="employee-header">
                <div class="employee-avatar">MC</div>
                <div class="employee-info">
                    <h5>Mike Chen</h5>
                    <p>UX Designer • Marketing</p>
                </div>
            </div>
            
            <div class="employee-stats">
                <div class="stat-item">
                    <h6 class="stat-value">94%</h6>
                    <p class="stat-label">Attendance</p>
                </div>
                <div class="stat-item">
                    <h6 class="stat-value">1.8y</h6>
                    <p class="stat-label">Experience</p>
                </div>
            </div>
            
            <div class="employee-actions">
                <a href="#" class="action-icon action-view" title="View Profile">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="action-icon action-edit" title="Edit Employee">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="action-icon action-delete" title="Delete Employee" onclick="deleteEmployee(2)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        
        <div class="employee-card">
            <div class="status-badge status-pending">Pending</div>
            <div class="employee-header">
                <div class="employee-avatar">ED</div>
                <div class="employee-info">
                    <h5>Emma Davis</h5>
                    <p>Sales Representative • Sales</p>
                </div>
            </div>
            
            <div class="employee-stats">
                <div class="stat-item">
                    <h6 class="stat-value">--</h6>
                    <p class="stat-label">Attendance</p>
                </div>
                <div class="stat-item">
                    <h6 class="stat-value">New</h6>
                    <p class="stat-label">Experience</p>
                </div>
            </div>
            
            <div class="employee-actions">
                <a href="#" class="action-icon action-view" title="View Profile">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="action-icon action-edit" title="Edit Employee">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="action-icon action-delete" title="Delete Employee" onclick="deleteEmployee(3)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        
        <div class="employee-card">
            <div class="status-badge status-active">Active</div>
            <div class="employee-header">
                <div class="employee-avatar">AR</div>
                <div class="employee-info">
                    <h5>Alex Rodriguez</h5>
                    <p>HR Manager • Human Resources</p>
                </div>
            </div>
            
            <div class="employee-stats">
                <div class="stat-item">
                    <h6 class="stat-value">100%</h6>
                    <p class="stat-label">Attendance</p>
                </div>
                <div class="stat-item">
                    <h6 class="stat-value">5.2y</h6>
                    <p class="stat-label">Experience</p>
                </div>
            </div>
            
            <div class="employee-actions">
                <a href="#" class="action-icon action-view" title="View Profile">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="action-icon action-edit" title="Edit Employee">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="action-icon action-delete" title="Delete Employee" onclick="deleteEmployee(4)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        
        <div class="employee-card">
            <div class="status-badge status-active">Active</div>
            <div class="employee-header">
                <div class="employee-avatar">LP</div>
                <div class="employee-info">
                    <h5>Lisa Park</h5>
                    <p>Financial Analyst • Finance</p>
                </div>
            </div>
            
            <div class="employee-stats">
                <div class="stat-item">
                    <h6 class="stat-value">96%</h6>
                    <p class="stat-label">Attendance</p>
                </div>
                <div class="stat-item">
                    <h6 class="stat-value">3.1y</h6>
                    <p class="stat-label">Experience</p>
                </div>
            </div>
            
            <div class="employee-actions">
                <a href="#" class="action-icon action-view" title="View Profile">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="action-icon action-edit" title="Edit Employee">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="action-icon action-delete" title="Delete Employee" onclick="deleteEmployee(5)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        
        <div class="employee-card">
            <div class="status-badge status-inactive">Inactive</div>
            <div class="employee-header">
                <div class="employee-avatar">JS</div>
                <div class="employee-info">
                    <h5>John Smith</h5>
                    <p>Project Manager • IT Department</p>
                </div>
            </div>
            
            <div class="employee-stats">
                <div class="stat-item">
                    <h6 class="stat-value">85%</h6>
                    <p class="stat-label">Attendance</p>
                </div>
                <div class="stat-item">
                    <h6 class="stat-value">4.5y</h6>
                    <p class="stat-label">Experience</p>
                </div>
            </div>
            
            <div class="employee-actions">
                <a href="#" class="action-icon action-view" title="View Profile">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="action-icon action-edit" title="Edit Employee">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="action-icon action-delete" title="Delete Employee" onclick="deleteEmployee(6)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Table View (Hidden by default) -->
    <div class="modern-card" id="tableContainer" style="display: none;">
        <div class="card-body">
            <table class="modern-table table" id="employeesTable">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Attendance Rate</th>
                        <th>Status</th>
                        <th class="no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="employee-avatar me-3" style="width: 40px; height: 40px; font-size: 1rem;">SJ</div>
                                <div>
                                    <h6 class="mb-0">Sarah Johnson</h6>
                                    <small class="text-muted">sarah.johnson@company.com</small>
                                </div>
                            </div>
                        </td>
                        <td>IT Department</td>
                        <td>Senior Developer</td>
                        <td><span class="badge badge-modern badge-success">98%</span></td>
                        <td><span class="badge badge-modern badge-success">Active</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="#" class="action-icon action-view">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="#" class="action-icon action-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="action-icon action-delete" onclick="deleteEmployee(1)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Showing 1 to 6 of 156 employees
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <li class="page-item active">
                    <a class="page-link" href="#">1</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">2</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">3</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchView(view) {
        const gridContainer = document.getElementById('gridContainer');
        const tableContainer = document.getElementById('tableContainer');
        const gridBtn = document.getElementById('gridView');
        const tableBtn = document.getElementById('tableView');
        
        if (view === 'grid') {
            gridContainer.style.display = 'grid';
            tableContainer.style.display = 'none';
            gridBtn.classList.add('active');
            tableBtn.classList.remove('active');
        } else {
            gridContainer.style.display = 'none';
            tableContainer.style.display = 'block';
            tableBtn.classList.add('active');
            gridBtn.classList.remove('active');
        }
    }
    
    function deleteEmployee(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                popup: 'rounded-modern',
                confirmButton: 'btn-modern',
                cancelButton: 'btn-modern btn-outline'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Add actual delete logic here
                showToast('Employee deleted successfully!', 'success');
            }
        });
    }
    
    function exportEmployees() {
        showToast('Preparing export...', 'info');
        
        // Simulate export process
        setTimeout(() => {
            showToast('Export completed successfully!', 'success');
        }, 2000);
    }
    
    function resetFilters() {
        document.getElementById('employeeSearch').value = '';
        document.getElementById('departmentFilter').value = '';
        document.getElementById('statusFilter').value = '';
        
        showToast('Filters reset', 'info');
    }
    
    // Real-time search functionality
    document.getElementById('employeeSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const employeeCards = document.querySelectorAll('.employee-card');
        
        employeeCards.forEach(card => {
            const name = card.querySelector('.employee-info h5').textContent.toLowerCase();
            const department = card.querySelector('.employee-info p').textContent.toLowerCase();
            
            if (name.includes(searchTerm) || department.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
    
    // Initialize animations
    $(document).ready(function() {
        $('.employee-card').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
            $(this).addClass('animate-fade-in-up');
        });
        
        // Initialize DataTable for table view
        if ($.fn.DataTable) {
            $('#employeesTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "",
                    searchPlaceholder: "Search employees...",
                },
                columnDefs: [
                    { targets: 'no-sort', orderable: false }
                ]
            });
        }
    });
</script>
@endsection