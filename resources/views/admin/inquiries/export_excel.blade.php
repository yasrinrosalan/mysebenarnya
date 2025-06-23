<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inquiry Report (Excel)</title>
</head>
<body>
    <h2>Inquiry Report</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inquiries as $inq)
            <tr>
                <td>{{ $inq->title }}</td>
                <td>{{ ucfirst($inq->status) }}</td>
                <td>{{ $inq->submitted_at }}</td>
                <td>{{ $inq->category->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
