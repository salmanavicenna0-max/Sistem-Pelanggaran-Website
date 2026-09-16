<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Poin Siswa</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th {
            background-color: #f5f5f5;
            padding: 8px;
            text-align: left;
        }
        td {
            padding: 8px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>SMAN 6 Bandung</h1>
        <p>Jl. Pasirkaliki No. 51, Arjuna, Kec. Cicendo, Kota Bandung, Jawa Barat 40172</p>
        <p><strong>REKAPITULASI POIN KEDISIPLINAN SISWA</strong></p>
    </div>

    <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NIS</th>
                <th width="35%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="15%" class="text-center">Total Poin</th>
                <th width="15%">Status Zona</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $student->nis }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ $student->schoolClass->name ?? '-' }}</td>
                <td class="text-center">{{ $student->getCurrentPoints() }}</td>
                <td>{{ $student->point_zone['name'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Bandung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <br><br><br>
        <p>_______________________</p>
        <p>Bidang Kesiswaan</p>
    </div>

</body>
</html>
