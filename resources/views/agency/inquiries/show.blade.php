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

        @if ($inquiry->assignment)
        <tr><th>Assigned Comment</th><td>{{ $inquiry->assignment->comment }}</td></tr>
        <tr><th>Assigned Date</th><td>{{ $inquiry->assignment->assigned_at }}</td></tr>
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

        @if ($inquiry->auditLogs->count())
        <tr>
            <th>Audit Logs</th>
            <td>
                <ul>
                    @foreach ($inquiry->auditLogs as $log)
                        <li>{{ $log->action }} by user {{ $log->user_id }} at {{ $log->timestamp }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
        @endif
    </table>
</div>
@endsection
