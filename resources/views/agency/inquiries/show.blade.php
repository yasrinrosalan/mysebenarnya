@extends('layouts.app')

@section('title', 'Inquiry Details')

@section('content')
<div class="container">
    <h3>🔎 Inquiry Details</h3>

    <table class="table table-bordered w-75">
        <tr><th>Title</th><td>{{ $inquiry->title }}</td></tr>
        <tr><th>Description</th><td>{{ $inquiry->description }}</td></tr>
        <tr><th>Category</th><td>{{ $inquiry->category->name ?? '-' }}</td></tr>
        <tr><th>Status</th><td>{{ ucfirst($inquiry->status) }}</td></tr>
        <tr><th>Submitted At</th><td>{{ $inquiry->submitted_at }}</td></tr>

        @if ($inquiry->auditLogs->count())
    <h5 class="mt-4">📝 Inquiry History</h5>
    <table class="table table-bordered">
        <thead class="table-secondary">
            <tr>
                <th>Timestamp</th>
                <th>Action</th>
                <th>Details</th>
                <th>By User ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inquiry->auditLogs as $log)
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
    <p>No audit logs available.</p>
@endif



        @if ($inquiry->attachments->count())
        <tr>
            <th>Evidence</th>
            <td>
                <ul>
                    @foreach ($inquiry->attachments as $file)
                        <li><a href="{{ asset('storage/' . $file->url_path) }}" target="_blank">{{ $file->file_type }}</a></li>
                    @endforeach
                </ul>
            </td>
        </tr>
        @endif

    
    </table>
</div>
@endsection
