{{-- resources/views/admin/activity_logs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
    <h1>📜 Activity Logs</h1>

    <table class="table table-striped table-bordered mt-4">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Action</th>
                <th>Timestamp</th>
                <th>Details</th>
                <th>Inquiry ID</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->log_id }}</td>
                    <td>{{ $log->user?->name ?? 'Unknown User' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->timestamp }}</td>
                    <td>{{ Str::limit($log->details, 50) }}</td>
                    <td>{{ $log->inquiry_id ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.audit-logs.show', $log->log_id) }}" class="btn btn-sm btn-primary">
                            View
                        </a>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No audit logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
