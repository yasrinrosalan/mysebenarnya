@extends('layouts.app')

@section('content')
<div class="container">
    <h3>📋 Inquiry Details</h3>

    <table class="table">
        <tr><th>Title</th><td>{{ $inquiry->title }}</td></tr>
        <tr><th>Description</th><td>{{ $inquiry->description }}</td></tr>
        <tr><th>Category</th><td>{{ $inquiry->category->name ?? '-' }}</td></tr>
        <tr><th>Status</th><td>{{ ucfirst($inquiry->status) }}</td></tr>
        <tr><th>Submitted At</th><td>{{ $inquiry->submitted_at }}</td></tr>
        <tr><th>Evidence</th>
            <td>
                @forelse($inquiry->attachments as $file)
                    <a href="{{ asset('storage/' . $file->url_path) }}" target="_blank">{{ $file->file_type }}</a><br>
                @empty
                    No attachment.
                @endforelse
            </td>
        </tr>
    </table>

    <h5>🧾 Inquiry History</h5>
    <ul class="list-group">
        @forelse($inquiry->auditLogs as $log)
            <li class="list-group-item">
                <strong>{{ $log->action }}:</strong> {{ $log->details }} <br>
                <small class="text-muted">{{ $log->timestamp }}</small>
            </li>
        @empty
            <li class="list-group-item">No actions logged yet.</li>
        @endforelse
    </ul>
</div>
@endsection
