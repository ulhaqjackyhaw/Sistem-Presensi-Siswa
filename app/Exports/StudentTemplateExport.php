<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StudentTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnFormatting
{
    public function array(): array
    {
        return [
            [
                'nis' => '1234567', // NIS (maksimal 20 karakter)
                'nisn' => '1234567890', // NISN (10 karakter)
                'nama' => 'Contoh: Rudi Hartono',
                'alamat' => 'Jl. Contoh No. 123',
                'telepon' => '081234567890',
                'nama_orang_tua' => 'Contoh: Budi Hartono',
                'telepon_orang_tua' => '081234567891',
                'email_orang_tua' => 'budi@example.com',
                'jenis_kelamin' => 'L', // L atau P
                'tanggal_lahir' => '2005-01-01', // Format YYYY-MM-DD
                'tahun_masuk' => '2025', // 4 digit
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'NIS',
            'NISN',
            'Nama',
            'Alamat',
            'Telepon',
            'Nama Orang Tua',
            'Telepon Orang Tua',
            'Email Orang Tua',
            'Jenis Kelamin (L/P)',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Tahun Masuk',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,          // NIS
            'B' => NumberFormat::FORMAT_TEXT,          // NISN
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Tanggal Lahir
            'K' => NumberFormat::FORMAT_TEXT,          // Tahun Masuk
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ]
        ]);

        // Style untuk contoh data
        $sheet->getStyle('2')->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '666666']
            ]
        ]);

        // Set lebar kolom
        $sheet->getColumnDimension('A')->setWidth(25); // NIS
        $sheet->getColumnDimension('B')->setWidth(10); // NISN
        $sheet->getColumnDimension('C')->setWidth(30); // Nama
        $sheet->getColumnDimension('D')->setWidth(35); // Alamat
        $sheet->getColumnDimension('E')->setWidth(20); // Telepon
        $sheet->getColumnDimension('F')->setWidth(30); // Nama Orang Tua
        $sheet->getColumnDimension('G')->setWidth(20); // Telepon Orang Tua
        $sheet->getColumnDimension('H')->setWidth(30); // Email Orang Tua
        $sheet->getColumnDimension('I')->setWidth(20); // Jenis Kelamin
        $sheet->getColumnDimension('J')->setWidth(25); // Tanggal Lahir
        $sheet->getColumnDimension('K')->setWidth(15); // Tahun Masuk

        // Validasi untuk jenis kelamin
        $validation = $sheet->getCell('I2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"L,P"');
        $validation->setPromptTitle('Pilih Jenis Kelamin');
        $validation->setPrompt('Pilih L untuk Laki-laki atau P untuk Perempuan');
        $validation->setErrorTitle('Input Salah');
        $validation->setError('Pilih L atau P saja');

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}