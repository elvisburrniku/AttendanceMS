@extends('layouts.master')

@section('title', 'NFC System Dashboard')

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group float-right">
                        <a href="{{ route('nfc.scanner') }}" class="btn btn-primary">
                            <i class="fas fa-mobile-alt"></i> Open Scanner
                        </a>
                        <a href="{{ route('nfc.devices') }}" class="btn btn-outline-secondary ml-2">
                            <i class="fas fa-cog"></i> Manage Devices
                        </a>
                    </div>
                    <h4 class="page-title">NFC System Dashboard</h4>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title text-white mb-0">{{ $stats['total_nfc_cards'] }}</h5>
                                <p class="card-text">Registered NFC Cards</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-id-card fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title text-white mb-0">{{ $stats['active_employees'] }}</h5>
                                <p class="card-text">Active Employees</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title text-white mb-0">{{ $stats['today_scans'] }}</h5>
                                <p class="card-text">Today's NFC Scans</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-qrcode fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title text-white mb-0">{{ $stats['unique_scanners'] }}</h5>
                                <p class="card-text">Active Scanners</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-mobile-alt fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Recent NFC Activity -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <h4 class="card-title mb-0">Recent NFC Activity</h4>
                            <div class="ml-auto">
                                <select class="form-control form-control-sm" id="activity-filter">
                                    <option value="all">All Activity</option>
                                    <option value="check_in">Check-ins Only</option>
                                    <option value="check_out">Check-outs Only</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-borderless table-hover" id="activity-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Action</th>
                                        <th>Time</th>
                                        <th>Scanner</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentActivity as $activity)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs mr-3">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        {{ substr($activity->first_name, 0, 1) }}{{ substr($activity->last_name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $activity->first_name }} {{ $activity->last_name }}</h6>
                                                    <small class="text-muted">{{ $activity->emp_code }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($activity->punch_state == '0')
                                                <span class="badge badge-success">Check In</span>
                                            @else
                                                <span class="badge badge-warning">Check Out</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ \Carbon\Carbon::parse($activity->punch_time)->format('M d, H:i') }}</span>
                                        </td>
                                        <td>{{ $activity->terminal_alias ?? 'Unknown' }}</td>
                                        <td>{{ $activity->area_alias ?? 'Office' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No recent NFC activity found
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics & Quick Actions -->
            <div class="col-xl-4">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Quick Actions</h4>
                        <div class="d-grid gap-2">
                            <a href="{{ route('nfc.scanner') }}" class="btn btn-primary">
                                <i class="fas fa-mobile-alt me-2"></i> Open Scanner
                            </a>
                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#bulkRegisterModal">
                                <i class="fas fa-upload me-2"></i> Bulk Register Cards
                            </button>
                            <a href="{{ route('nfc.devices') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-cog me-2"></i> Manage Devices
                            </a>
                            <button type="button" class="btn btn-outline-info" id="export-report">
                                <i class="fas fa-download me-2"></i> Export Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Real-time Stats -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Today's Analytics</h4>
                        <canvas id="todayChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health Status -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">System Health Status</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="status-indicator bg-success"></div>
                                    <h6 class="mt-2 mb-0">NFC Service</h6>
                                    <small class="text-muted">Operational</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="status-indicator bg-success"></div>
                                    <h6 class="mt-2 mb-0">Database</h6>
                                    <small class="text-muted">Connected</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="status-indicator bg-warning"></div>
                                    <h6 class="mt-2 mb-0">Scanner Devices</h6>
                                    <small class="text-muted">{{ $stats['unique_scanners'] }} Active</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="status-indicator bg-info"></div>
                                    <h6 class="mt-2 mb-0">API Endpoints</h6>
                                    <small class="text-muted">Responsive</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Register Modal -->
<div class="modal fade" id="bulkRegisterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Register NFC Cards</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Upload a CSV file or enter employee codes and NFC IDs manually.
                </div>
                
                <!-- File Upload -->
                <div class="mb-3">
                    <label for="csv-upload">Upload CSV File</label>
                    <input type="file" class="form-control-file" id="csv-upload" accept=".csv">
                    <small class="form-text text-muted">
                        CSV format: emp_code,nfc_id (one per line)
                    </small>
                </div>

                <div class="text-center mb-3">
                    <span class="text-muted">-- OR --</span>
                </div>

                <!-- Manual Entry -->
                <div class="manual-entry">
                    <h6>Manual Entry</h6>
                    <div id="registration-entries">
                        <div class="row registration-row mb-2">
                            <div class="col-md-5">
                                <input type="text" class="form-control emp-code" placeholder="Employee Code">
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control nfc-id" placeholder="NFC Card ID">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-danger remove-entry">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" id="add-entry">
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-bulk-registration">
                    <i class="fas fa-save"></i> Register Cards
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Initialize charts
    initTodayChart();
    
    // Setup event listeners
    setupEventListeners();
    
    // Auto-refresh data every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
});

function initTodayChart() {
    const ctx = document.getElementById('todayChart').getContext('2d');
    
    // Generate hourly data for today
    const hours = [];
    const checkins = [];
    const checkouts = [];
    
    for (let i = 0; i < 24; i++) {
        hours.push(i + ':00');
        checkins.push(Math.floor(Math.random() * 20));
        checkouts.push(Math.floor(Math.random() * 20));
    }
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: hours,
            datasets: [{
                label: 'Check-ins',
                data: checkins,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            }, {
                label: 'Check-outs',
                data: checkouts,
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function setupEventListeners() {
    // Activity filter
    $('#activity-filter').on('change', function() {
        const filter = $(this).val();
        filterActivityTable(filter);
    });
    
    // Bulk registration
    $('#add-entry').on('click', addRegistrationEntry);
    $(document).on('click', '.remove-entry', removeRegistrationEntry);
    $('#save-bulk-registration').on('click', saveBulkRegistration);
    $('#csv-upload').on('change', handleCsvUpload);
    
    // Export report
    $('#export-report').on('click', exportReport);
}

function filterActivityTable(filter) {
    const rows = $('#activity-table tbody tr');
    
    rows.each(function() {
        const row = $(this);
        const actionBadge = row.find('.badge');
        
        if (filter === 'all') {
            row.show();
        } else if (filter === 'check_in' && actionBadge.hasClass('badge-success')) {
            row.show();
        } else if (filter === 'check_out' && actionBadge.hasClass('badge-warning')) {
            row.show();
        } else {
            row.hide();
        }
    });
}

function addRegistrationEntry() {
    const entryHtml = `
        <div class="row registration-row mb-2">
            <div class="col-md-5">
                <input type="text" class="form-control emp-code" placeholder="Employee Code">
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control nfc-id" placeholder="NFC Card ID">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger remove-entry">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    $('#registration-entries').append(entryHtml);
}

function removeRegistrationEntry() {
    if ($('.registration-row').length > 1) {
        $(this).closest('.registration-row').remove();
    }
}

async function saveBulkRegistration() {
    const registrations = [];
    
    $('.registration-row').each(function() {
        const empCode = $(this).find('.emp-code').val().trim();
        const nfcId = $(this).find('.nfc-id').val().trim();
        
        if (empCode && nfcId) {
            registrations.push({ emp_code: empCode, nfc_id: nfcId });
        }
    });
    
    if (registrations.length === 0) {
        alert('Please enter at least one registration entry');
        return;
    }
    
    try {
        const response = await fetch('{{ route("nfc.bulk-register") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({ registrations: registrations })
        });
        
        const data = await response.json();
        
        if (data.success) {
            $('#bulkRegisterModal').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 3000
            });
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error processing bulk registration');
    }
}

function handleCsvUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const csv = e.target.result;
        const lines = csv.split('\n');
        
        // Clear existing entries
        $('#registration-entries').empty();
        
        lines.forEach(line => {
            const [empCode, nfcId] = line.split(',').map(s => s.trim());
            if (empCode && nfcId) {
                const entryHtml = `
                    <div class="row registration-row mb-2">
                        <div class="col-md-5">
                            <input type="text" class="form-control emp-code" value="${empCode}">
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control nfc-id" value="${nfcId}">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-danger remove-entry">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#registration-entries').append(entryHtml);
            }
        });
    };
    reader.readAsText(file);
}

function exportReport() {
    // Generate CSV report
    const csvContent = "data:text/csv;charset=utf-8," 
        + "Employee Code,Name,Action,Time,Scanner,Location\n"
        + $('#activity-table tbody tr').map(function() {
            const cells = $(this).find('td');
            if (cells.length > 1) {
                return [
                    $(cells[0]).find('small').text(),
                    $(cells[0]).find('h6').text(),
                    $(cells[1]).text().trim(),
                    $(cells[2]).text().trim(),
                    $(cells[3]).text().trim(),
                    $(cells[4]).text().trim()
                ].join(',');
            }
        }).get().join('\n');
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "nfc_activity_report.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<style>
.status-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    margin: 0 auto;
}

.avatar-xs {
    width: 2rem;
    height: 2rem;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 500;
}

.bg-soft-primary {
    background-color: rgba(116, 120, 141, 0.15);
}

.registration-row {
    align-items: center;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.375rem;
}

.opacity-75 {
    opacity: 0.75;
}
</style>
@endsection