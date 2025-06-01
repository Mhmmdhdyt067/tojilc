<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Hasil Try Out</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            position: relative;
        }

        .watermark {
            position: fixed;
            top: 30%;
            left: 20%;
            width: 60%;
            opacity: 0.1;
            z-index: -1;
        }

        .page-break {
            page-break-after: always;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <img src="{{ public_path('images/login/logo.png') }}" class="watermark">

    <div class="header">
        <h2>{{ $data->first()->tryout->title ?? 'Try Out' }}</h2>
        <p>Waktu: {{ $data->first()->tryout->waktu ?? '-' }} menit</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Nama</th>
                <th>Subject</th>
                <th>Nilai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->user->name ?? '-' }}</td>
                <td>{{ $d->subject->title ?? '-' }}</td>
                <td>{{ $d->total_poin }}</td>
                <td>{{ $d->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>