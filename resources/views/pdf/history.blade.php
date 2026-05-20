<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Fasilitas</title>

    <style>

        body{
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2{
            text-align: center;
            margin-bottom: 20px;
        }

        table{
            width: 100%;
            border-collapse: collapse;
        }

        th, td{
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th{
            background: #f0f0f0;
        }

        img{
            width: 70px;
            height: auto;
        }

    </style>
</head>
<body>

    <h2>
        Riwayat Fasilitas
    </h2>

    <p>
        <b>Nama Fasilitas:</b>
        {{ $fasilitas->nama }}
    </p>

    <p>
        <b>Lokasi:</b>
        {{ $fasilitas->lokasi }}
    </p>

    <br>

    <table>

        <thead>

            <tr>

                <th>No</th>

                <th>Sebelum Update</th>

<th>Tanggal Update</th>

                <th>User</th>

                <th>Status</th>

                <th>Keterangan</th>

                <th>Foto</th>

            </tr>

        </thead>

        <tbody>

            @forelse($histories as $item)

            <tr>

                <td>
                    {{ $loop->iteration }}
                </td>

             <td>

    @if($item->previous_update)

        {{ \Carbon\Carbon::parse($item->previous_update)
            ->format('d M Y H:i') }}

    @else

        -

    @endif

</td>

<td>
    {{ $item->created_at->format('d M Y H:i') }}
</td>

                <td>
                    {{ $item->user->name ?? '-' }}
                </td>

                <td>
                   {{ strtoupper($item->status_from) }}
ke
{{ strtoupper($item->status_to) }}
                </td>

                <td>
                    {{ $item->keterangan ?? '-' }}
                </td>

                <td>

    @if($item->photos->count())

        @foreach($item->photos as $photo)

            <img
                src="{{ storage_path('app/public/'.$photo->foto) }}"
                width="70"
                style="margin-bottom:5px;">

        @endforeach

    @else

        -

    @endif

</td>

            </tr>

            @empty

            <tr>

                <td colspan="6" align="center">
                    Tidak ada histori
                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</body>
</html>