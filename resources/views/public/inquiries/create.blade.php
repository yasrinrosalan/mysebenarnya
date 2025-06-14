@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Submit Inquiry</h2>
    <form action="{{ route('public.inquiries.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Category</label>
            <select name="category_id" class="form-control" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Evidence (optional)</label>
            <input type="file" name="evidence" class="form-control">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_public" class="form-check-input">
            <label class="form-check-label">Make this inquiry public</label>
        </div>
        <button type="submit" class="btn btn-primary">Submit Inquiry</button>
    </form>
</div>
@endsection
