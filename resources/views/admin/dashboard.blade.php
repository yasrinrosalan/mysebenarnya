@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col text-center">
            <h2 class="fw-bold">📊 Admin Dashboard</h2>
            <p class="text-muted">Welcome back, {{ Auth::user()->name }}. Here's a quick overview of the system.</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('admin.users.index') }}" class="btn btn-primary w-100">
                👥 Manage Users
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.register.agency.form') }}" class="btn btn-success w-100">
                🏢 Register Agency
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-dark w-100">
                📜 View Audit Logs
            </a>
        </div>
    </div>

    {{-- 🔍 Filter Reports --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-secondary text-white fw-semibold">
            Filter Reports
        </div>
        <div class="card-body">
            <form action="{{ route('admin.dashboard') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label">User Type</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">All</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="agency" {{ request('role') == 'agency' ? 'selected' : '' }}>Agency</option>
                        <option value="public" {{ request('role') == 'public' ? 'selected' : '' }}>Public</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="agency_id" class="form-label">Agency</label>
                    <select id="agency_id" name="agency_id" class="form-select">
                        <option value="">All</option>
                        @foreach($agencies as $agency)
                            <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>
                                {{ $agency->agency_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-outline-primary mt-2">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="row g-4">
        {{-- Bar Chart: Users by Role --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-semibold">
                    Total Registered Users by Role
                </div>
                <div class="card-body">
                    <canvas id="usersByRoleChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Line Chart: Monthly Registrations --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white fw-semibold">
                    Monthly New User Registrations (Last 6 Months)
                </div>
                <div class="card-body">
                    <canvas id="monthlyRegistrationsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-3">
        <a href="{{ route('admin.reports.excel', request()->query()) }}" class="btn btn-outline-success btn-sm">📥 Export Excel</a>
        <a href="{{ route('admin.reports.pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm">📄 Export PDF</a>
    </div>
    {{-- Charts: Inquiries by Category and Status --}}
    <div class="row g-4 mt-4">
        {{-- Pie Chart: Inquiries by Category --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white fw-semibold">
                    Inquiries by Category
                </div>
                <div class="card-body">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Pie Chart: Inquiries by Status --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark fw-semibold">
                    Inquiries by Status
                </div>
                <div class="card-body">
                    <canvas id="inquiryStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    


    {{-- Footer --}}
    <div class="text-center mt-4">
        <a href="{{ route('profile.show') }}" class="btn btn-link">👤 View My Profile</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const usersByRole = @json($usersByRole);
    const roleLabels = Object.keys(usersByRole);
    const roleData = Object.values(usersByRole);

    const ctx1 = document.getElementById('usersByRoleChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: roleLabels,
            datasets: [{
                label: 'Users',
                data: roleData,
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Users by Role' }
            }
        }
    });

    const monthlyData = @json($monthlyRegistrations);
    const months = Object.keys(monthlyData);
    const registrationCounts = Object.values(monthlyData);

    const ctx2 = document.getElementById('monthlyRegistrationsChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Registrations',
                data: registrationCounts,
                borderColor: '#36b9cc',
                backgroundColor: 'rgba(54, 185, 204, 0.2)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'User Registrations Trend' }
            }
        }
    });


    // Pie Chart: Inquiries by Category
    const categoryData = @json($inquiryByCategoryChart);
    const categoryLabels = Object.keys(categoryData);
    const categoryCounts = Object.values(categoryData);

    const ctx3 = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx3, {
        type: 'pie',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryCounts,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Inquiries by Category'
                }
            }
        }
    });

    // Pie Chart: Inquiries by Status
    const statusData = @json($inquiryStatusChart);
    const statusLabels = Object.keys(statusData);
    const statusCounts = Object.values(statusData);

    const ctx4 = document.getElementById('inquiryStatusChart').getContext('2d');
    new Chart(ctx4, {
        type: 'pie',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Inquiries by Status'
                }
            }
        }
    });

</script>

@endpush
