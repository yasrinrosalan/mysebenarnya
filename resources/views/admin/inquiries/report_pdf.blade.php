<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inquiry Report (PDF)</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Inquiry Report</h2>

    <table>
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
