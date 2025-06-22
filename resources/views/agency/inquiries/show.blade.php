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

        @if($inquiry->assignment)
<form method="POST" action="{{ route('agency.assignment.update', $inquiry->assignment->assignment_id) }}">
    @csrf
    <div class="mb-3">
        <label for="status" class="form-label">Update Inquiry Status</label>
        <select name="status" class="form-select" required>
            <option value="">-- Select Status --</option>
            <option value="assigned">Assigned</option>
            <option value="under_investigation">Under Investigation</option>
            <option value="verified_true">Verified as True</option>
            <option value="fake">Identified as Fake</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="comment" class="form-label">Comment</label>
        <textarea name="comment" class="form-control" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update Status</button>
</form>
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
