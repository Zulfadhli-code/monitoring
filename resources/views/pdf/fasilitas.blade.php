<!DOCTYPE html>
<html>
<head>
    <title>Monitoring Fasilitas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            background-color: #f3f4f6;
            padding: 10px;
            border: 1px solid #ccc;
        }

        td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        /* WARNA STATUS (mirip dashboard) */
        .ready {
            background-color: #22c55e;
            color: white;
            text-align: center;
            font-weight: bold;
        }

        .maintenance {
            background-color: #f59e0b;
            color: white;
            text-align: center;
            font-weight: bold;
        }

        .down {
            background-color: #ef4444;
            color: white;
            text-align: center;
            font-weight: bold;
        }

        /* ROW BACKGROUND */
        .row-ready {
            background-color: #f0fdf4;
        }

        .row-maintenance {
            background-color: #fef9c3;
        }

        .row-down {
            background-color: #fee2e2;
        }
    </style>
</head>
<body>

    <h2 style="">Monitoring Kesiapan Teknik</h2>

    <table>
        <thead>
            <tr>
                <th style="background:#1f2937; color:white;">No</th>
<th style="background:#1f2937; color:white;">Nama</th>
<th style="background:#1f2937; color:white;">Lokasi</th>
<th style="background:#1f2937; color:white;">Kategori</th>
<th style="background:#1f2937; color:white;">Detail</th>
<th style="background:#1f2937; color:white;">Status</th>
<th style="background:#1f2937; color:white;">Update</th>
<th style="background:#1f2937; color:white;">Foto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $item)
            <tr class="
                @if($item->status == 'ready') row-ready
                @elseif($item->status == 'maintenance') row-maintenance
                @else row-down
                @endif
            ">
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ $item->kategori }}</td>
                <td>{{ $item->detail }}</td>

                <!-- STATUS -->
                <td class="
                    @if($item->status == 'ready') ready
                    @elseif($item->status == 'maintenance') maintenance
                    @else down
                    @endif
                ">
                    {{ strtoupper($item->status) }}
                </td>

                <!-- UPDATE + JAM -->
                <td>
                    {{ \Carbon\Carbon::parse($item->updated_at)->format('d M Y H:i') }}
                </td>
                <!-- Foto -->
                <td>
    @if($item->foto)
        <img src="{{ public_path('storage/'.$item->foto) }}" width="80">
    @else
        -
    @endif
</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>