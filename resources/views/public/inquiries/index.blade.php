@extends('layouts.app')

@section('title', 'My Inquiries')

@section('content')
<div class="container">
    <h3 class="mb-4">📂 My Inquiries</h3>

    @if($inquiries->isEmpty())
        <div class="alert alert-info">You have not submitted any inquiries yet.</div>
    @else
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Submitted At</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Public?</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inquiries as $inquiry)
                    <tr>
                        <td>{{ $inquiry->title }}</td>
                        <td>{{ $inquiry->submitted_at }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($inquiry->status) }}</span></td>
                        <td>{{ $inquiry->category->name ?? 'N/A' }}</td>
                        <td>{{ $inquiry->is_public ? '✅ Yes' : '❌ No' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
