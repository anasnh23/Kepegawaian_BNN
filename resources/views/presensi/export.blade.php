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
        @foreach($data as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $row->user->nama ?? '-' }}</td>
            <td>{{ $row->tanggal }}</td>
            <td>{{ $row->jam_masuk ?? '-' }}</td>
            <td>{{ $row->jam_pulang ?? '-' }}</td>
            <td>{{ ucfirst($row->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
