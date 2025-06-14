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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('monthlyChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($monthlyStats->toArray())) !!},
        datasets: [{
            label: 'Inquiries per Month',
            data: {!! json_encode(array_values($monthlyStats->toArray())) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.6)'
        }]
    }
});
</script>
@endpush
