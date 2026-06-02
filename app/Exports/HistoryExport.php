<?php

namespace App\Exports;
use App\Models\Fasilitas;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

 class HistoryExport implements FromArray, WithHeadings, WithDrawings, WithStyles
{
    protected $bulan;
    protected $tahun;
    protected $search;

    public function __construct($bulan, $tahun, $search = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->search = $search;
    }

    public function headings(): array
    {
        $headings = [
            'Nama Fasilitas',
            'Lokasi',
            'Kategori'
        ];
        $days = cal_days_in_month(
            CAL_GREGORIAN,
            $this->bulan,
            $this->tahun
        );
        for ($i = 1; $i <= $days; $i++) {
        $tanggal = Carbon::create(
        $this->tahun,
        $this->bulan,
        $i
    )->translatedFormat('d M Y');
    $headings[] = $tanggal;
    $headings[] = 'Foto ';
}

        return $headings;
    }

    public function array(): array
    {
        $result = [];

        $days = cal_days_in_month(
            CAL_GREGORIAN,
            $this->bulan,
            $this->tahun
        );

        $fasilitas = Fasilitas::with([
    'histories.user',
    'histories.photos'
])

->when($this->search, function ($q) {

    $q->where('nama', 'like', '%'.$this->search.'%');
})

->get();

        foreach ($fasilitas as $item) {

            $row = [
                $item->nama,
                $item->lokasi,
                $item->kategori,
            ];

for ($day = 1; $day <= $days; $day++) {

    $tanggal = Carbon::create(
        $this->tahun,
        $this->bulan,
        $day
    )->format('Y-m-d');

    $histories = $item->histories
        ->filter(function ($h) use ($tanggal) {

            return $h->created_at
                ->format('Y-m-d') == $tanggal;
        });

    $text = '-';

    if ($histories->count()) {

        $text = '';

        foreach ($histories as $h) {

            $jam = $h->created_at
                ->format('H:i');

            $status = strtoupper($h->status_to);

            $keterangan = $h->keterangan ?? '-';

            $user = $h->user->name ?? '-';

            $text .=
                $jam.' '.$status."\n".
                $keterangan."\n".
                '(' .$user. ')'."\n\n";
        }
    }

    // WAJIB selalu 2 kolom
    $row[] = $text;
    $row[] = '';
}
            $result[] = $row;
        }

        return $result;
    }
    public function drawings()
{
    $drawings = [];

    $fasilitas = Fasilitas::with([
        'histories.photos'
    ])->get();

    $row = 2;

    foreach ($fasilitas as $item) {

        $days = cal_days_in_month(
            CAL_GREGORIAN,
            $this->bulan,
            $this->tahun
        );

        for ($day = 1; $day <= $days; $day++) {

            $tanggal = Carbon::create(
                $this->tahun,
                $this->bulan,
                $day
            )->format('Y-m-d');

            $histories = $item->histories
                ->filter(function ($h) use ($tanggal) {

                    return $h->created_at
                        ->format('Y-m-d') == $tanggal;
                });

            foreach ($histories as $history) {

                foreach ($history->photos as $photo) {

                    $path = public_path(
                        'storage/'.$photo->foto
                    );

                    if (file_exists($path)) {

                        $drawing = new Drawing();

                        $drawing->setPath($path);

                        $drawing->setHeight(40);

                        // D = tanggal 1
                       $columnIndex = ((int)$day * 2) + 3; $column =\PhpOffice\PhpSpreadsheet\Cell\Coordinate
    ::stringFromColumnIndex($columnIndex);

                        $drawing->setCoordinates(
                            $column.$row
                        );
                        $drawing->setOffsetX(5);
$drawing->setOffsetY(5);

                        $drawings[] = $drawing;

                        break;
                    }
                }
            }
        }

        $row++;
    }

    return $drawings;
}
public function styles(Worksheet $sheet)
{
    // Tinggi semua row data
    for ($i = 2; $i <= 100; $i++) {

        $sheet->getRowDimension($i)
            ->setRowHeight(120);
    }

    // Lebar kolom tanggal
    foreach (range('E', 'BZ') as $col) {

    $sheet->getColumnDimension($col)
        ->setWidth(18);
}

    // Wrap text supaya histori turun ke bawah
    $sheet->getStyle('D2:AI100')
        ->getAlignment()
        ->setWrapText(true);


    
}
}