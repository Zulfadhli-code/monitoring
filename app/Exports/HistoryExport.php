<?php

namespace App\Exports;

use App\Models\Fasilitas;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistoryExport implements FromQuery, WithHeadings, WithDrawings, WithStyles
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

    /**
     * QUERY UTAMA (AMAN, TIDAK LOAD SEMUA KE RAM)
     */
    public function query()
    {
        return Fasilitas::query()
            ->with(['histories.user', 'histories.photos'])
            ->when($this->search, function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
    }

    /**
     * HEADER KOLOM
     */
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
            $tanggal = Carbon::create($this->tahun, $this->bulan, $i)
                ->translatedFormat('d M Y');

            $headings[] = $tanggal;
            $headings[] = 'Foto';
        }

        return $headings;
    }

    /**
     * DATA EXPORT (SUDAH DIHILANGKAN ARRAY BERAT)
     * DIPERLUKAN OLEH FromQuery (tetap wajib return collection builder)
     */
    public function map($fasilitas): array
    {
        $days = cal_days_in_month(
            CAL_GREGORIAN,
            $this->bulan,
            $this->tahun
        );

        $row = [
            $fasilitas->nama,
            $fasilitas->lokasi,
            $fasilitas->kategori,
        ];

        for ($day = 1; $day <= $days; $day++) {

            $tanggal = Carbon::create($this->tahun, $this->bulan, $day)
                ->format('Y-m-d');

            $histories = $fasilitas->histories->filter(function ($h) use ($tanggal) {
                return $h->created_at->format('Y-m-d') === $tanggal;
            });

            $text = '-';

            if ($histories->count()) {
                $text = '';

                foreach ($histories as $h) {

                    $jam = $h->created_at->format('H:i');
                    $status = strtoupper($h->status_to);
                    $keterangan = $h->keterangan ?? '-';
                    $user = $h->user->name ?? '-';

                    $text .= $jam . ' ' . $status . "\n"
                        . $keterangan . "\n"
                        . '(' . $user . ')' . "\n\n";
                }
            }

            $row[] = $text;
            $row[] = '';
        }

        return $row;
    }

    /**
     * GAMBAR (DIPERBAIKI AGAR TIDAK DOUBLE QUERY)
     */
    public function drawings()
    {
        $drawings = [];

        $fasilitasList = Fasilitas::with(['histories.photos'])
            ->when($this->search, function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            })
            ->get();

        $row = 2;

        foreach ($fasilitasList as $item) {

            $days = cal_days_in_month(
                CAL_GREGORIAN,
                $this->bulan,
                $this->tahun
            );

            for ($day = 1; $day <= $days; $day++) {

                $tanggal = Carbon::create($this->tahun, $this->bulan, $day)
                    ->format('Y-m-d');

                $histories = $item->histories->filter(function ($h) use ($tanggal) {
                    return $h->created_at->format('Y-m-d') === $tanggal;
                });

                foreach ($histories as $history) {

                    foreach ($history->photos as $photo) {

                        $path = public_path('storage/' . $photo->foto);

                        if (file_exists($path)) {

                            $drawing = new Drawing();
                            $drawing->setPath($path);
                            $drawing->setHeight(40);

                            $columnIndex = ((int)$day * 2) + 3;
                            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);

                            $drawing->setCoordinates($column . $row);
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

    /**
     * STYLE EXCEL
     */
    public function styles(Worksheet $sheet)
    {
        for ($i = 2; $i <= 100; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(120);
        }

        foreach (range('E', 'BZ') as $col) {
            $sheet->getColumnDimension($col)->setWidth(18);
        }

        $sheet->getStyle('D2:AI100')
            ->getAlignment()
            ->setWrapText(true);
    }
}