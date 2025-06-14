{{-- resources/views/admin/activity_logs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Audit Log Detail')

@section('content')
    <h1>📝 Audit Log Detail</h1>

    <div class="card mt-4">
        <div class="card-header">
            Log ID: {{ $log->log_id }}
        </div>
        <div class="card-body">
            <p><strong>User:</strong> {{ $log->user?->name ?? 'Unknown User' }} (ID: {{ $log->user_id }})</p>
            <p><strong>Action:</strong> {{ $log->action }}</p>
            <p><strong>Timestamp:</strong> {{ $log->timestamp }}</p>
            <p><strong>Details:</strong></p>
            <pre class="bg-light p-3">{{ $log->details }}</pre>
            @if($log->inquiry_id)
                <p><strong>Inquiry ID:</strong> {{ $log->inquiry_id }}</p>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary mt-3">← Back to Audit Logs</a>
@endsection
