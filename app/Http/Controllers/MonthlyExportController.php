<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;

class MonthlyExportController extends Controller
{
    public function excel(Request $request)
    {
        $request->validate([
            'bulan' => ['required', 'date_format:Y-m'],
            'kategori' => ['nullable', 'string'],
            'nama' => ['nullable', 'string'],
        ]);

        $bulan = Carbon::createFromFormat('Y-m', $request->bulan);

        $query = Fasilitas::with([
            'histories.user',
            'histories.photos',
            'photos',
        ]);

        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->nama) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        $fasilitasList = $query
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get();

        if ($fasilitasList->isEmpty()) {
            return back()->with('error', 'Tidak ada fasilitas yang sesuai dengan filter.');
        }

        $folderName = 'Laporan_Monitoring_' .
            $bulan->translatedFormat('F_Y');

        $tempPath = storage_path('app/' . $folderName);

        File::deleteDirectory($tempPath);
        File::makeDirectory($tempPath, 0755, true);

        foreach ($fasilitasList as $fasilitas) {
            $this->createFacilityExcel(
                $fasilitas,
                $bulan,
                $tempPath
            );
        }

        $zipPath = storage_path('app/' . $folderName . '.zip');

        if (file_exists($zipPath)) {
            unlink($zipPath);
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        $files = File::allFiles($tempPath);

        foreach ($files as $file) {
            $relativePath = str_replace(
                $tempPath . DIRECTORY_SEPARATOR,
                '',
                $file->getPathname()
            );

            $zip->addFile(
                $file->getPathname(),
                $relativePath
            );
        }

        $zip->close();

        File::deleteDirectory($tempPath);

        return response()->download(
            $zipPath,
            $folderName . '.zip'
        )->deleteFileAfterSend(true);
    }

    private function createFacilityExcel(
        Fasilitas $fasilitas,
        Carbon $bulan,
        string $tempPath
    ) {
        $spreadsheet = new Spreadsheet();

        $detailSheet = $spreadsheet->getActiveSheet();
        $detailSheet->setTitle('Detail Fasilitas');

        /*
        |--------------------------------------------------------------------------
        | DETAIL FASILITAS
        |--------------------------------------------------------------------------
        */

        $detailSheet->fromArray([
            ['LAPORAN MONITORING FASILITAS'],
            [],
            ['Nama Fasilitas', $fasilitas->nama],
            ['Kategori', $fasilitas->kategori],
            ['Subkategori', $fasilitas->subkategori],
            ['Lokasi', $fasilitas->lokasi],
            ['Detail', $fasilitas->detail],
            ['Status Terkini', ucfirst($fasilitas->status)],
            ['Keterangan Terakhir', $fasilitas->keterangan],
            ['Terakhir Diperbarui',
                $fasilitas->updated_at
                    ? $fasilitas->updated_at->format('d-m-Y H:i')
                    : '-'
            ],
            ['Teknisi Terakhir',
                optional($fasilitas->updater)->name ?? '-'
            ],
            ['Periode Laporan', $bulan->translatedFormat('F Y')],
        ]);

        $detailSheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(14);

        $detailSheet->getStyle('A3:A12')->getFont()->setBold(true);

        $detailSheet->getColumnDimension('A')->setWidth(25);
        $detailSheet->getColumnDimension('B')->setWidth(55);

        $detailSheet->getStyle('A1:B12')
            ->getAlignment()
            ->setVertical('top')
            ->setWrapText(true);

        /*
        |--------------------------------------------------------------------------
        | RIWAYAT MONITORING
        |--------------------------------------------------------------------------
        */

        $historySheet = $spreadsheet->createSheet();
        $historySheet->setTitle('Riwayat Monitoring');

        $historySheet->fromArray([
            [
                'Tanggal',
                'Teknisi',
                'Status Sebelum',
                'Status Sesudah',
                'Keterangan',
            ]
        ]);

        $row = 2;

        $histories = $fasilitas->histories
            ->filter(function ($history) use ($bulan) {
                return $history->created_at
                    && $history->created_at->format('Y-m') === $bulan->format('Y-m');
            })
            ->sortBy('created_at');

        foreach ($histories as $history) {
            $historySheet->fromArray([
                [
                    $history->created_at
                        ? $history->created_at->format('d-m-Y H:i')
                        : '-',

                    optional($history->user)->name ?? '-',

                    $history->status_from
                        ? ucfirst($history->status_from)
                        : '-',

                    $history->status_to
                        ? ucfirst($history->status_to)
                        : '-',

                    $history->keterangan ?? '-',
                ]
            ], null, 'A' . $row);

            $row++;
        }

        $historySheet->getStyle('A1:E1')->getFont()->setBold(true);

        foreach (range('A', 'E') as $column) {
            $historySheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $historySheet->getStyle('A1:E' . max($row, 2))
            ->getAlignment()
            ->setVertical('top')
            ->setWrapText(true);

        /*
        |--------------------------------------------------------------------------
        | DOKUMENTASI FOTO
        |--------------------------------------------------------------------------
        */

        $photoSheet = $spreadsheet->createSheet();
        $photoSheet->setTitle('Dokumentasi Foto');

        $photoSheet->fromArray([
            [
                'Tanggal',
                'Teknisi',
                'Keterangan',
                'Foto',
            ]
        ]);

        $photoSheet->getStyle('A1:D1')->getFont()->setBold(true);

        $photoRow = 2;

        foreach ($histories as $history) {
            foreach ($history->photos as $photo) {

                $path = public_path('storage/' . $photo->foto);

                if (!file_exists($path)) {
                    continue;
                }

                $photoSheet->fromArray([
                    [
                        $history->created_at
                            ? $history->created_at->format('d-m-Y H:i')
                            : '-',

                        optional($history->user)->name ?? '-',

                        $history->keterangan ?? '-',

                        'Dokumentasi',
                    ]
                ], null, 'A' . $photoRow);

                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();

                $drawing->setPath($path);
                $drawing->setHeight(100);
                $drawing->setCoordinates('D' . $photoRow);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);

                $photoSheet->getRowDimension($photoRow)->setRowHeight(110);

                $drawing->setWorksheet($photoSheet);

                $photoRow++;
            }
        }

        foreach (range('A', 'C') as $column) {
            $photoSheet
                ->getColumnDimension($column)
                ->setWidth(25);
        }

        $photoSheet
            ->getColumnDimension('D')
            ->setWidth(25);

        $photoSheet->getStyle('A1:D' . max($photoRow, 2))
            ->getAlignment()
            ->setVertical('top')
            ->setWrapText(true);

        /*
        |--------------------------------------------------------------------------
        | SIMPAN EXCEL
        |--------------------------------------------------------------------------
        */

        $categoryFolder = $this->safeFolderName(
            $fasilitas->kategori ?: 'Tanpa Kategori'
        );

        $facilityName = $this->safeFolderName(
            $fasilitas->nama
        );

        $categoryPath = $tempPath . DIRECTORY_SEPARATOR . $categoryFolder;

        if (!is_dir($categoryPath)) {
            File::makeDirectory($categoryPath, 0755, true);
        }

        $filePath = $categoryPath .
            DIRECTORY_SEPARATOR .
            $facilityName . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    private function safeFolderName(string $name): string
    {
        $name = preg_replace('/[\\\\\/:*?"<>|]+/', '_', $name);

        return trim($name) ?: 'Tanpa Nama';
    }
}