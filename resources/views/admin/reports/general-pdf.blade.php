<!DOCTYPE html>
<html>
<head>
    <title>Laporan Umum Sistem</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .stats { display: flex; justify-content: space-around; margin: 20px 0; }
        .stat-box { text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .actions { display: flex; gap: 10px; justify-content: flex-end; margin-bottom: 20px; }
        .actions a, .actions button { padding: 10px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .actions button { background-color: #2563eb; color: white; }
        .actions a { background-color: #6b7280; color: white; }
        @media print { .actions { display: none; } }
    </style>
</head>
<body>
    <div class="actions">
        <a href="{{ route('admin.reports.index') }}">Kembali</a>
        <button type="button" onclick="window.print()">Print / Simpan PDF</button>
    </div>
    <div class="header">
        <h1>Laporan Umum Sistem</h1>
        <p>Dibuat pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h3>Statistik Umum</h3>
        <div class="stats">
            <div class="stat-box">
                <h4>{{ $generalStats['total_projects'] }}</h4>
                <p>Total Proyek</p>
            </div>
            <div class="stat-box">
                <h4>{{ $generalStats['active_projects'] }}</h4>
                <p>Proyek Aktif</p>
            </div>
            <div class="stat-box">
                <h4>{{ $generalStats['total_users'] }}</h4>
                <p>Total Users</p>
            </div>
            <div class="stat-box">
                <h4>{{ $generalStats['overall_completion_rate'] }}%</h4>
                <p>Overall Completion Rate</p>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Proyek Aktif</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Proyek</th>
                    <th>Total Cards</th>
                    <th>Completed Cards</th>
                    <th>Completion Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activeProjects as $project)
                <tr>
                    <td>{{ $project['project_name'] }}</td>
                    <td>{{ $project['total_cards'] }}</td>
                    <td>{{ $project['completed_cards'] }}</td>
                    <td>{{ $project['completion_rate'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Performa Tim</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Role</th>
                    <th>Total Tasks</th>
                    <th>Completed Tasks</th>
                    <th>Completion Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teamPerformance as $member)
                <tr>
                    <td>{{ $member['name'] }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $member['role'])) }}</td>
                    <td>{{ $member['total_tasks'] }}</td>
                    <td>{{ $member['completed_tasks'] }}</td>
                    <td>{{ $member['completion_rate'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
