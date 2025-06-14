@extends('layouts.app')

@section('content')
<div class="container">
    <h3>All Inquiries</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Submitted At</th>
                <th>Status</th>
                <th>Category</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inquiries as $inquiry)
                <tr>
                    <td>{{ $inquiry->title }}</td>
                    <td>{{ $inquiry->submitted_at }}</td>
                    <td>{{ ucfirst($inquiry->status) }}</td>
                    <td>{{ $inquiry->category->name }}</td>
                    <td>
                        <a href="{{ route('admin.inquiries.show', $inquiry->inquiry_id) }}" class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
