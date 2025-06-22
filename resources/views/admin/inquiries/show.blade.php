@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Inquiry Details</h3>

    <table class="table table-bordered w-75">
    <tbody>
        <tr>
            <th>Title</th>
            <td>{{ $inquiry->title }}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td>{{ $inquiry->description }}</td>
        </tr>
        @if($inquiry->publicUser && $inquiry->publicUser->user)
        <tr>
            <th>Submitted By</th>
            <td>{{ $inquiry->publicUser->user->name }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $inquiry->publicUser->user->email }}</td>
        </tr>
        <tr>
            <th>Contact Info</th>
            <td>{{ $inquiry->publicUser->user->contact_info ?? '-' }}</td>
        </tr>
        @endif
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($inquiry->status) }}</td>
        </tr>
        <tr>
            <th>Submitted At</th>
            <td>{{ $inquiry->submitted_at }}</td>
        </tr>
        <tr>
            <th>Category</th>
            <td>{{ $inquiry->category->name }}</td>
        </tr>
        @if ($inquiry->attachments->count())
        <tr>
            <th>Attachment</th>
            <td>
                <ul class="mb-0 ps-3">
                    @foreach ($inquiry->attachments as $file)
                        <li><a href="{{ asset('storage/' . $file->url_path) }}" target="_blank">View Attachment</a></li>
                    @endforeach
                </ul>
            </td>
        </tr>
        @endif
    </tbody>
</table>




    <form method="POST" action="{{ route('admin.inquiries.validate', $inquiry->inquiry_id) }}">
        @csrf
        <div class="mb-3">
            <label>Review Notes</label>
            <textarea name="review_notes" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="validated">Validate</option>
                <option value="discarded">Discard</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Submit Review</button>
    </form>

    <hr>

    
</div>
@endsection
