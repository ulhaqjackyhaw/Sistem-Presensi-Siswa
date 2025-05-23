<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

class TeachersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithBatchInserts
{
    use SkipsErrors;

    protected $headerMap = [];

    public function __construct()
    {
        // Mapping berbagai kemungkinan nama kolom
        $this->headerMap = [
            'nip' => ['nip', 'nomor_induk', 'nomor_induk_pegawai', 'no_induk', 'nip_guru'],
            'nama' => ['nama', 'name', 'nama_lengkap', 'nama_guru'],
            'email' => ['email', 'surel', 'alamat_email', 'email_guru'],
            'telepon' => ['telepon', 'telp', 'hp', 'no_hp', 'nomor_telepon', 'no_telepon', 'handphone', 'phone'],
            'alamat' => ['alamat', 'address', 'alamat_lengkap', 'alamat_rumah'],
            'jenis_kelamin' => ['jenis_kelamin', 'gender', 'kelamin', 'sex', 'jenis_kelamin_l_p'],
            'tanggal_lahir' => ['tanggal_lahir', 'tgl_lahir', 'birth_date', 'birthdate', 'dob', 'tanggal_lahir_yyyy_mm_dd']
        ];
    }

    protected function findHeaderKey($row, $field)
    {
        $possibleHeaders = $this->headerMap[$field];
        $headers = array_keys($row);

        // Cari header yang cocok (case insensitive)
        foreach ($headers as $header) {
            $normalizedHeader = strtolower(str_replace([' ', '_', '-'], '', $header));
            foreach ($possibleHeaders as $possibleHeader) {
                if (strtolower(str_replace([' ', '_', '-'], '', $possibleHeader)) === $normalizedHeader) {
                    return $header;
                }
            }
        }

        return null;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        try {
            // Debug log
            Log::info('Processing row:', $row);

            // Temukan key yang sesuai untuk setiap field
            $nipKey = $this->findHeaderKey($row, 'nip');
            $namaKey = $this->findHeaderKey($row, 'nama');
            $emailKey = $this->findHeaderKey($row, 'email');
            $teleponKey = $this->findHeaderKey($row, 'telepon');
            $alamatKey = $this->findHeaderKey($row, 'alamat');
            $jenisKelaminKey = $this->findHeaderKey($row, 'jenis_kelamin');
            $tanggalLahirKey = $this->findHeaderKey($row, 'tanggal_lahir');

            // Validasi semua field required ada
            if (!$nipKey || !$namaKey || !$emailKey || !$teleponKey ||
                !$alamatKey || !$jenisKelaminKey || !$tanggalLahirKey) {
                throw new \Exception('Ada kolom wajib yang tidak ditemukan di file Excel');
            }

            // Validasi jenis kelamin
            $gender = strtoupper(trim($row[$jenisKelaminKey]));
            if (!in_array($gender, ['L', 'P', 'LAKI-LAKI', 'PEREMPUAN'])) {
                throw new \Exception('Jenis kelamin harus L/P atau LAKI-LAKI/PEREMPUAN');
            }
            // Konversi jenis kelamin ke format L/P
            $gender = in_array($gender, ['L', 'LAKI-LAKI']) ? 'L' : 'P';

            // Format tanggal lahir
            $birthDate = $this->parseDate($row[$tanggalLahirKey]);
            if (!$birthDate) {
                throw new \Exception('Format tanggal lahir tidak valid');
            }

            // Format NIP
            $nip = $this->formatNIP($row[$nipKey]);

            // Mulai transaksi database
            return DB::transaction(function() use ($row, $nip, $namaKey, $emailKey, $teleponKey, $alamatKey, $gender, $birthDate) {
                // Buat user account
                $user = User::create([
                    'name' => $row[$namaKey],
                    'email' => $row[$emailKey],
                    'password' => Hash::make('password123')
                ]);

                try {
                    $user->assignRole('Guru');
                } catch (Throwable $e) {
                    Log::error('Error assigning role:', ['error' => $e->getMessage()]);
                    // Lanjutkan meskipun gagal assign role
                }

                // Buat data guru
                $teacher = new Teacher([
                    'nip' => $nip,
                    'name' => $row[$namaKey],
                    'phone' => $row[$teleponKey],
                    'address' => $row[$alamatKey],
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                    'user_id' => $user->id,
                ]);

                $teacher->save();
                return $teacher;
            });

        } catch (\Exception $e) {
            Log::error('Error in row:', [
                'row' => $row,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function formatNIP($nip)
    {
        // Hapus semua karakter non-angka
        $cleanNip = preg_replace('/[^0-9]/', '', (string)$nip);

        // Jika NIP kosong setelah dibersihkan
        if (empty($cleanNip)) {
            throw new \Exception('NIP tidak valid: harus berupa angka');
        }

        return $cleanNip;
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            // Jika input adalah numeric (Excel date)
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // Jika format sudah YYYY-MM-DD
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value;
            }

            // Coba parse dengan Carbon (mendukung berbagai format)
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error('Date parsing error:', [
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function rules(): array
    {
        return [];  // Validasi dilakukan secara manual di model()
    }

    public function customValidationMessages()
    {
        return [];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
