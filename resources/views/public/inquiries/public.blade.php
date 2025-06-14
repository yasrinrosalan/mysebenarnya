@extends('layouts.app')

@section('title', 'Browse Public Inquiries')

@section('content')
<div class="container">
    <h3 class="mb-4">🌍 Browse Public Inquiries</h3>

    <form method="GET" action="{{ route('public.inquiries.public') }}" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search inquiries..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <select name="category_id" class="form-select">
                <option value="">-- Filter by Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">🔍 Search</button>
        </div>
    </form>

    @if($inquiries->isEmpty())
        <div class="alert alert-warning">No public inquiries found.</div>
    @else
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inquiries as $inquiry)
                    <tr>
                        <td>{{ $inquiry->title }}</td>
                        <td>{{ Str::limit($inquiry->description, 100) }}</td>
                        <td>{{ $inquiry->category->name ?? 'N/A' }}</td>
                        <td>{{ $inquiry->submitted_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
