@extends('layouts.app')

@section('title', 'Audit Log for Inquiry')

@section('content')
<div class="container">
    <h3>📜 Audit Log - Inquiry #{{ $inquiry->inquiry_id }}</h3>
    <p><strong>Title:</strong> {{ $inquiry->title }}</p>
    <p><strong>Status:</strong> {{ ucfirst($inquiry->status) }}</p>

    <hr>

    @if($inquiry->auditLogs->count())
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Timestamp</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>User ID</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inquiry->auditLogs as $log)
                    <tr>
                        <td>{{ $log->timestamp }}</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->details ?? '-' }}</td>
                        <td>{{ $log->user_id }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">No audit records found for this inquiry.</p>
    @endif

    <a href="{{ route('admin.inquiries.manage') }}" class="btn btn-secondary mt-3">⬅️ Back to Inquiries</a>
</div>
@endsection
