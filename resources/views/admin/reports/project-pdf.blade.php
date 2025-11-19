<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Proyek - {{ $project->project_name }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            padding: 2rem;
            color: #0f172a;
            background: #f8fafc;
        }

        h1, h2, h3 {
            margin: 0 0 0.5rem 0;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .project-meta {
            font-size: 0.95rem;
            color: #475569;
        }

        .section {
            margin-bottom: 1.75rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.75rem;
        }

        th, td {
            border: 1px solid #e2e8f0;
            padding: 0.6rem 0.75rem;
            text-align: left;
            font-size: 0.9rem;
        }

        th {
            background: #eef2ff;
            color: #312e81;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .summary-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            background: #fff;
        }

        .summary-card span {
            display: block;
            font-size: 0.85rem;
            color: #64748b;
        }

        .summary-card strong {
            font-size: 1.4rem;
            color: #0f172a;
        }

        .actions {
            text-align: right;
            margin-bottom: 1.5rem;
        }

        .actions button {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 0.6rem 1.1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        @media print {
            .actions {
                display: none;
            }

            body {
                padding: 0.5rem 1rem;
                background: #fff;
            }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <div class="report-header">
        <div>
            <h1>{{ $project->project_name }}</h1>
            <p>{{ $project->description ?? 'Tidak ada deskripsi.' }}</p>
        </div>
        <div class="project-meta">
            <div><strong>Deadline:</strong> {{ $project->deadline ? $project->deadline->format('d M Y') : '-' }}</div>
            <div><strong>Total Board:</strong> {{ $project->boards->count() }}</div>
            <div><strong>Total Anggota:</strong> {{ $project->members->count() }}</div>
        </div>
    </div>

    <div class="section">
        <h2>Ringkasan Proyek</h2>
        <div class="summary-grid">
            <div class="summary-card">
                <span>Total Card</span>
                <strong>{{ $projectStats['total_cards'] }}</strong>
                <span>Card selesai: {{ $projectStats['completed_cards'] }}</span>
            </div>
            <div class="summary-card">
                <span>Progress Card</span>
                <strong>{{ $projectStats['completion_rate'] }}%</strong>
            </div>
            <div class="summary-card">
                <span>Total Subtask</span>
                <strong>{{ $projectStats['total_subtasks'] }}</strong>
                <span>Subtask selesai: {{ $projectStats['completed_subtasks'] }}</span>
            </div>
            <div class="summary-card">
                <span>Progress Subtask</span>
                <strong>{{ $projectStats['subtask_completion_rate'] }}%</strong>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Progress per Board</h2>
        <table>
            <thead>
                <tr>
                    <th>Board</th>
                    <th>Total Card</th>
                    <th>Card Selesai</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @forelse($progressData as $board)
                    <tr>
                        <td>{{ $board['board_name'] }}</td>
                        <td>{{ $board['total_cards'] }}</td>
                        <td>{{ $board['completed_cards'] }}</td>
                        <td>{{ $board['completion_rate'] }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada board pada proyek ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Tim Project</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Peran</th>
                    <th>Card Ditugaskan</th>
                    <th>Card Selesai</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teamData as $member)
                    <tr>
                        <td>{{ $member['name'] }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $member['role'])) }}</td>
                        <td>{{ $member['assigned_cards'] }}</td>
                        <td>{{ $member['completed_cards'] }}</td>
                        <td>{{ $member['completion_rate'] }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Belum ada anggota tim yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Catatan Tambahan</h2>
        <p>Dokumen ini dibuat otomatis melalui sistem monitoring proyek pada {{ now()->format('d M Y H:i') }}.</p>
    </div>
</body>
</html>
