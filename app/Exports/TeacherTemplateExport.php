<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TeacherTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnFormatting
{
    public function array(): array
    {
        return [
            [
                'nip' => '123456789012',
                'nama' => 'Contoh: Budi Santoso, S.Pd.',
                'email' => 'budi@example.com',
                'telepon' => '081234567890',
                'alamat' => 'Jl. Contoh No. 123',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1990-01-01'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama',
            'Email',
            'Telepon',
            'Alamat',
            'Jenis Kelamin (L/P)',
            'Tanggal Lahir (YYYY-MM-DD)'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,          // NIP
            'G' => NumberFormat::FORMAT_DATE_YYYYMMDD  // Tanggal Lahir
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
        $sheet->getColumnDimension('A')->setWidth(15);  // NIP
        $sheet->getColumnDimension('B')->setWidth(30);  // Nama
        $sheet->getColumnDimension('C')->setWidth(25);  // Email
        $sheet->getColumnDimension('D')->setWidth(15);  // Telepon
        $sheet->getColumnDimension('E')->setWidth(35);  // Alamat
        $sheet->getColumnDimension('F')->setWidth(20);  // Jenis Kelamin
        $sheet->getColumnDimension('G')->setWidth(25);  // Tanggal Lahir

        // Tambahkan validasi untuk jenis kelamin
        $validation = $sheet->getCell('F2')->getDataValidation();
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
