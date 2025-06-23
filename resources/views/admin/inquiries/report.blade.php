@extends('layouts.app')

@section('title', 'Inquiry Reports')

@section('content')
<div class="container">
    <h3 class="mb-4">📊 Inquiry Report</h3>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="">-- All --</option>
                <option value="pending">Pending</option>
                <option value="validated">Validated</option>
                <option value="rejected">Rejected</option>
                <option value="assigned">Assigned</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>Category</label>
            <select name="category_id" class="form-select">
                <option value="">-- All --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label>From Date</label>
            <input type="date" name="from" class="form-control">
        </div>
        <div class="col-md-3">
            <label>To Date</label>
            <input type="date" name="to" class="form-control">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary w-100">🔍 Filter</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inquiries as $inq)
            <tr>
                <td>{{ $inq->title }}</td>
                <td>{{ ucfirst($inq->status) }}</td>
                <td>{{ $inq->submitted_at }}</td>
                <td>{{ $inq->category->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <h5>📈 Monthly Inquiry Summary</h5>
    <canvas id="monthlyChart"></canvas>

    {{-- Export & Download Buttons --}}
<div class="mt-3 d-flex gap-2">
    <a href="{{ route('admin.inquiries.export.excel', request()->query()) }}" class="btn btn-success btn-sm">
        📥 Export Excel
    </a>
    <a href="{{ route('admin.inquiries.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
        📄 Export PDF
    </a>
    <button class="btn btn-secondary btn-sm" onclick="downloadChart()">🖼️ Download Chart</button>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const groupedStats = @json($monthlyStats);
const categories = [...new Set(Object.values(groupedStats).flatMap(month => month.map(item => item.category_name)))];
const months = Object.keys(groupedStats);

const datasets = categories.map(category => {
    return {
        label: category,
        data: months.map(month => {
            const monthData = groupedStats[month].find(item => item.category_name === category);
            return monthData ? monthData.total : 0;
        }),
        backgroundColor: '#' + Math.floor(Math.random()*16777215).toString(16)
    };
});

const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: months,
        datasets: datasets
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Monthly Inquiry Count by Category'
            },
            tooltip: {
                mode: 'index',
                intersect: false,
            },
        },
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            x: {
                stacked: true
            },
            y: {
                stacked: true
            }
        }
    }
});
</script>
<script>
function downloadChart() {
    const chartCanvas = document.getElementById('monthlyChart');
    const link = document.createElement('a');
    link.download = 'inquiry_report_chart.png';
    link.href = chartCanvas.toDataURL('image/png');
    link.click();
}
</script>


@endpush

