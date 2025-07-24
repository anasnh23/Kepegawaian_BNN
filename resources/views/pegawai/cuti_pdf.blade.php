<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Formulir Cuti</title>
  <style>
    body {
      font-family: sans-serif;
      font-size: 12px;
      position: relative;
      margin: 0;
      padding: 0;
    }

    .watermark {
      position: fixed;
      top: 30%;
      left: 20%;
      width: 500px;
      opacity: 0.08;
      z-index: -1;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      position: relative;
      z-index: 1;
    }

    td, th {
      border: 1px solid #000;
      padding: 6px;
      vertical-align: top;
      background: transparent;
    }

    .header {
      background-color: #333;
      color: #fff;
      text-align: center;
      font-weight: bold;
    }

    .no-border {
      border: none;
    }

    .checkbox {
      font-family: DejaVu Sans, sans-serif;
    }

    .ttd-section td {
      height: 80px;
      text-align: center;
      vertical-align: bottom;
    }

    .no-border td {
      border: none;
    }
  </style>
</head>
<body>

  <!-- Watermark besar di tengah -->
  <img src="{{ public_path('adminlte/dist/img/bnn.jpg') }}" class="watermark">

  <h2 style="text-align:center;">FORMULIR PENGAJUAN CUTI</h2>

  <table>
    <tr>
      <td colspan="2" class="header">Data Pegawai</td>
    </tr>
    <tr>
      <td>Nama</td>
      <td>{{ $cuti->pegawai->nama ?? '-' }}</td>
    </tr>
    <tr>
      <td>No. Induk Pegawai</td>
      <td>{{ $cuti->pegawai->nip ?? '-' }}</td>
    </tr>
    <tr>
      <td>Jabatan</td>
      <td>{{ $cuti->pegawai->jabatan ?? '-' }}</td>
    </tr>
    <tr>
      <td>Divisi</td>
      <td>{{ $cuti->pegawai->divisi ?? '-' }}</td>
    </tr>
    <tr>
      <td>No. Handphone</td>
      <td>{{ $cuti->pegawai->no_tlp ?? '-' }}</td>
    </tr>

    <tr>
      <td colspan="2" class="header">Jenis Cuti & Periode</td>
    </tr>
    <tr>
      <td width="50%">
        <div class="checkbox">☑ {{ $cuti->jenis_cuti }}</div>
        @php
          $jenis = ['Cuti Tahunan', 'Cuti Sakit', 'Cuti Melahirkan', 'Cuti Alasan Penting', 'Cuti Tanpa Bayar'];
        @endphp
        @foreach($jenis as $jenis_cuti)
          @if($jenis_cuti !== $cuti->jenis_cuti)
            <div class="checkbox">☐ {{ $jenis_cuti }}</div>
          @endif
        @endforeach
      </td>
      <td>
        <table style="border: none; width: 100%;">
          <tr>
            <td>Diajukan Tgl</td>
            <td>: {{ \Carbon\Carbon::parse($cuti->tanggal_pengajuan)->format('d-m-Y') }}</td>
          </tr>
          <tr>
            <td>Tgl. Mulai</td>
            <td>: {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d-m-Y') }}</td>
          </tr>
          <tr>
            <td>Lama Cuti</td>
            <td>: {{ $cuti->lama_cuti }} hari</td>
          </tr>
          <tr>
            <td>Tgl. Masuk</td>
            <td>: {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->addDay()->format('d-m-Y') }}</td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td colspan="2" class="header">Keterangan / Alasan</td>
    </tr>
    <tr>
      <td colspan="2">{{ $cuti->keterangan ?? '-' }}</td>
    </tr>

    <tr>
      <td colspan="2" class="header">Pelimpahan Tugas & Wewenang kepada</td>
    </tr>
    <tr>
      <td>Nama</td>
      <td>.....................................................</td>
    </tr>
    <tr>
      <td>No. Induk Pegawai</td>
      <td>.....................................................</td>
    </tr>
    <tr>
      <td>Jabatan</td>
      <td>.....................................................</td>
    </tr>

    <tr class="header">
      <td>Diajukan oleh</td>
      <td>Disetujui oleh</td>
    </tr>
    <tr class="ttd-section">
      <td>
        Kediri, {{ \Carbon\Carbon::now()->format('d-m-Y') }}<br><br>
        <u>{{ $cuti->pegawai->nama ?? '-' }}</u><br>
        {{ $cuti->pegawai->jabatan ?? '-' }}
      </td>
      <td>
        Kediri, {{ \Carbon\Carbon::now()->format('d-m-Y') }}<br><br>
        <u>.................................</u><br>
        Atasan / Admin
      </td>
    </tr>
  </table>

</body>
</html>
