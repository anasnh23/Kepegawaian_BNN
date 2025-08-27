<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Pegawai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            position: relative;
            margin-bottom: 20px;
        }

        .header .logo {
            position: absolute;
            left: 20px;
            top: 0;
        }

        .header .logo img {
            height: 70px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .header h4 {
            margin: 4px 0;
            font-size: 13px;
            font-weight: normal;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 5px;
            text-align: center;
            font-size: 11px;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }

        .badge {
            padding: 3px 6px;
            color: #fff;
            border-radius: 3px;
            font-size: 10px;
        }

        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; }
        .badge-danger  { background-color: #dc3545; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">
            <img src="{{ public_path('adminlte/dist/img/bnn.jpg') }}" alt="Logo BNN">
        </div>
        <h2>SISTEM INFORMASI KEPEGAWAIAN</h2>
        <h4>BADAN NARKOTIKA NASIONAL KOTA KEDIRI</h4>
        <h4>Laporan Presensi Pegawai</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->user->nama ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                <td>{{ $row->jam_masuk ?? '-' }}</td>
                <td>{{ $row->jam_pulang ?? '-' }}</td>
                <td>
                    <span class="badge 
                        {{ $row->status == 'hadir' ? 'badge-success' : 
                           ($row->status == 'terlambat' ? 'badge-warning' : 'badge-danger') }}">
                        {{ ucfirst($row->status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">Tidak ada data presensi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </div>

</body>
</html>
