@extends('layouts.app')

@section('title', 'Assigned Inquiries')

@section('content')
<div class="container">
    <h3>📂 Assigned Inquiries</h3>

    {{-- Filter --}}
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Status --</option>
                <option value="assigned">Assigned</option>
                <option value="under_investigation">Under Investigation</option>
                <option value="verified_true">Verified True</option>
                <option value="fake">Fake</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-select">
                <option value="">-- Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}">{{ $category->name }}</option>
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

    {{-- Table --}}
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Category</th>
                <th>Submitted At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inquiries as $inquiry)
                <tr>
                    <td>{{ $inquiry->title }}</td>
                    <td>{{ ucfirst($inquiry->status) }}</td>
                    <td>{{ $inquiry->category->name ?? '-' }}</td>
                    <td>{{ $inquiry->submitted_at }}</td>
                    <td>
                        <a href="{{ route('agency.inquiries.show', $inquiry->inquiry_id) }}" class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No inquiries found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
