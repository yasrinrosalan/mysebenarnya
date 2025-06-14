@extends('layouts.app')

@section('title', 'Manage Inquiries')

@section('content')
<div class="container">

    

    <h4 class="mt-4">🟡 Pending Inquiries</h4>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Submitted By</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>Category</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingInquiries as $inquiry)
                <tr>
                    <td>{{ $inquiry->title }}</td>
                    <td>{{ optional($inquiry->publicUser->user)->name ?? '-' }}</td>
                    <td>{{ ucfirst($inquiry->status) }}</td>
                    <td>{{ $inquiry->submitted_at }}</td>
                    <td>{{ $inquiry->category->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.inquiries.show', $inquiry->inquiry_id) }}" class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No pending inquiries.</td></tr>
            @endforelse
        </tbody>
    </table>
    <h4 class="mt-5">📜 Inquiries History</h4>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Status --</option>
                <option value="validated">Validated</option>
                <option value="discarded">Discard</option>
                <option value="assigned">Assigned</option>
                <option value="under_investigation">Under investigation</option>
                <option value="verified_true">Verified as True</option>
                <option value="fake">Identified as Fake</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="agency_id" class="form-select">
                <option value="">-- Assigned Agency --</option>
                @foreach($agencies as $agency)
                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-floating col-md-3">
    <input type="date" name="from" class="form-control" id="fromDate">
    <label for="fromDate">From Date</label>
</div>
<div class="form-floating col-md-3">
    <input type="date" name="to" class="form-control" id="toDate">
    <label for="toDate">To Date</label>
</div>


        <div class="col-md-12 text-end">
            <button class="btn btn-primary">🔍 Filter</button>
        </div>
    </form>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Submitted By</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historyInquiries as $inquiry)
                <tr>
                    <td>{{ $inquiry->title }}</td>
                    <td>{{ optional($inquiry->publicUser->user)->name ?? '-' }}</td>
                    <td>{{ ucfirst($inquiry->status) }}</td>
                    <td>{{ $inquiry->submitted_at }}</td>
                    <td>{{ $inquiry->category->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No inquiries in history.</td></tr>
            @endforelse
        </tbody>
    </table>


</div>
@endsection
