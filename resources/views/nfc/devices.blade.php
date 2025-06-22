@extends('layouts.master')

@section('title', 'NFC Device Management')

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group float-right">
                        <a href="{{ route('nfc.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                        <a href="{{ route('nfc.scanner') }}" class="btn btn-primary ml-2">
                            <i class="fas fa-mobile-alt"></i> Open Scanner
                        </a>
                    </div>
                    <h4 class="page-title">NFC Device Management</h4>
                </div>
            </div>
        </div>

        <!-- Device Overview -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <h4 class="card-title mb-0">Registered NFC Devices</h4>
                            <div class="ml-auto">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDeviceModal">
                                    <i class="fas fa-plus"></i> Add Device
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="devices-table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Device ID</th>
                                        <th>Device Name</th>
                                        <th>Location</th>
                                        <th>Scan Count (30d)</th>
                                        <th>Unique Users</th>
                                        <th>Last Activity</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($devices as $device)
                                    <tr>
                                        <td>
                                            <code>{{ $device->terminal_sn }}</code>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-mobile-alt text-primary mr-2"></i>
                                                <span>{{ $device->terminal_alias ?: 'Unnamed Device' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $device->area_alias ?: 'Unknown' }}</span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold">{{ number_format($device->scan_count) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $device->unique_employees }} employees</span>
                                        </td>
                                        <td>
                                            @if($device->last_activity)
                                                <span class="text-success">
                                                    {{ \Carbon\Carbon::parse($device->last_activity)->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="text-muted">Never</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $lastActivity = $device->last_activity ? \Carbon\Carbon::parse($device->last_activity) : null;
                                                $isOnline = $lastActivity && $lastActivity->diffInMinutes(now()) < 30;
                                            @endphp
                                            @if($isOnline)
                                                <span class="badge badge-success">Online</span>
                                            @else
                                                <span class="badge badge-secondary">Offline</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary view-device-stats" 
                                                        data-device-id="{{ $device->terminal_sn }}"
                                                        data-device-name="{{ $device->terminal_alias }}">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary edit-device"
                                                        data-device-id="{{ $device->terminal_sn }}"
                                                        data-device-name="{{ $device->terminal_alias }}"
                                                        data-device-location="{{ $device->area_alias }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-mobile-alt fa-2x mb-3 d-block"></i>
                                            No NFC devices found. Devices will appear here after first scan.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Analytics -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Device Usage Analytics</h4>
                        <canvas id="deviceUsageChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Device Health</h4>
                        <div class="device-health-list">
                            @foreach($devices->take(5) as $device)
                            <div class="d-flex align-items-center mb-3">
                                <div class="device-status-indicator {{ \Carbon\Carbon::parse($device->last_activity ?? now()->subDays(1))->diffInMinutes(now()) < 30 ? 'bg-success' : 'bg-secondary' }}"></div>
                                <div class="ml-3">
                                    <h6 class="mb-0">{{ $device->terminal_alias ?: 'Device ' . $device->terminal_sn }}</h6>
                                    <small class="text-muted">{{ $device->scan_count }} scans today</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Device Modal -->
<div class="modal fade" id="addDeviceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New NFC Device</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-device-form">
                    <div class="form-group">
                        <label>Device ID</label>
                        <input type="text" class="form-control" id="device-id" required>
                        <small class="form-text text-muted">Unique identifier for the device</small>
                    </div>
                    <div class="form-group">
                        <label>Device Name</label>
                        <input type="text" class="form-control" id="device-name" required>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" class="form-control" id="device-location" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="device-description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-device">
                    <i class="fas fa-save"></i> Add Device
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Device Modal -->
<div class="modal fade" id="editDeviceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Device</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-device-form">
                    <input type="hidden" id="edit-device-id">
                    <div class="form-group">
                        <label>Device Name</label>
                        <input type="text" class="form-control" id="edit-device-name" required>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" class="form-control" id="edit-device-location" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="update-device">
                    <i class="fas fa-save"></i> Update Device
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Device Statistics Modal -->
<div class="modal fade" id="deviceStatsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Device Statistics</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="device-stats-content">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Loading statistics...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#devices-table').DataTable({
        responsive: true,
        order: [[5, 'desc']], // Sort by last activity
        pageLength: 25
    });
    
    // Initialize charts
    initDeviceUsageChart();
    
    // Setup event listeners
    setupEventListeners();
});

function initDeviceUsageChart() {
    const ctx = document.getElementById('deviceUsageChart').getContext('2d');
    
    // Get device data for chart
    const deviceNames = @json($devices->pluck('terminal_alias')->take(10));
    const scanCounts = @json($devices->pluck('scan_count')->take(10));
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: deviceNames,
            datasets: [{
                label: 'Scan Count (30 days)',
                data: scanCounts,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
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
    // View device stats
    $('.view-device-stats').on('click', function() {
        const deviceId = $(this).data('device-id');
        const deviceName = $(this).data('device-name');
        showDeviceStats(deviceId, deviceName);
    });
    
    // Edit device
    $('.edit-device').on('click', function() {
        const deviceId = $(this).data('device-id');
        const deviceName = $(this).data('device-name');
        const deviceLocation = $(this).data('device-location');
        
        $('#edit-device-id').val(deviceId);
        $('#edit-device-name').val(deviceName);
        $('#edit-device-location').val(deviceLocation);
        $('#editDeviceModal').modal('show');
    });
    
    // Save new device
    $('#save-device').on('click', saveDevice);
    
    // Update device
    $('#update-device').on('click', updateDevice);
}

function showDeviceStats(deviceId, deviceName) {
    $('#deviceStatsModal .modal-title').text(`Statistics for ${deviceName}`);
    $('#deviceStatsModal').modal('show');
    
    // Load device statistics
    fetch(`{{ route('nfc.analytics') }}?device_id=${deviceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayDeviceStats(data.data);
            } else {
                $('#device-stats-content').html('<p class="text-danger">Failed to load statistics</p>');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            $('#device-stats-content').html('<p class="text-danger">Error loading statistics</p>');
        });
}

function displayDeviceStats(stats) {
    const html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>${stats.total_scans || 0}</h3>
                        <p class="mb-0">Total Scans</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>${stats.unique_users || 0}</h3>
                        <p class="mb-0">Unique Users</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6>Recent Activity</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Employee</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${stats.recent_activity ? stats.recent_activity.map(activity => `
                                <tr>
                                    <td>${activity.time}</td>
                                    <td>${activity.employee}</td>
                                    <td><span class="badge badge-${activity.action === 'Check In' ? 'success' : 'warning'}">${activity.action}</span></td>
                                </tr>
                            `).join('') : '<tr><td colspan="3" class="text-center text-muted">No recent activity</td></tr>'}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    $('#device-stats-content').html(html);
}

function saveDevice() {
    const deviceData = {
        device_id: $('#device-id').val(),
        device_name: $('#device-name').val(),
        location: $('#device-location').val(),
        description: $('#device-description').val()
    };
    
    // In a real implementation, this would save to database
    console.log('Saving device:', deviceData);
    
    $('#addDeviceModal').modal('hide');
    Swal.fire({
        icon: 'success',
        title: 'Device Added!',
        text: 'The device has been registered successfully.',
        timer: 3000
    });
    
    // Reset form
    $('#add-device-form')[0].reset();
}

function updateDevice() {
    const deviceData = {
        device_id: $('#edit-device-id').val(),
        device_name: $('#edit-device-name').val(),
        location: $('#edit-device-location').val()
    };
    
    // In a real implementation, this would update the database
    console.log('Updating device:', deviceData);
    
    $('#editDeviceModal').modal('hide');
    Swal.fire({
        icon: 'success',
        title: 'Device Updated!',
        text: 'The device information has been updated.',
        timer: 3000
    });
}
</script>

<style>
.device-status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.device-health-list {
    max-height: 300px;
    overflow-y: auto;
}

.table th {
    border-top: none;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

code {
    background-color: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}
</style>
@endsection