<!DOCTYPE html>
<html>
<head>
    <title>User Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .chart {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h3>User Report</h3>

    <div class="chart">
        <img src="{{ $usersByRoleChart }}" width="500" alt="Users By Role Chart">
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->user_id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ optional($user->created_at)->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
