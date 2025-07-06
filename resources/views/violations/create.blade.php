@extends('layouts.app')

@section('title')
    Form Lapor Pelanggaran
@endsection

@push('css')
{{-- Tambahan CSS jika diperlukan --}}
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    .form-group label {
        font-weight: 500;
    }
    .card-header h4, .card-header .card-title { /* Penyesuaian untuk AdminLTE versi berbeda */
        margin-bottom: 0;
    }
    .form-text.text-muted {
        font-size: 0.875em;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-uppercase">Lapor Pelanggaran Baru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li> {{-- Ganti 'home' jika nama route dashboard berbeda --}}
                    <li class="breadcrumb-item"><a href="{{ route('violations.index') }}">Laporan Pelanggaran</a></li>
                    <li class="breadcrumb-item active">Lapor Baru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                {{-- Mengubah card-danger menjadi card-primary --}}
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Formulir Laporan Pelanggaran</h3>
                    </div>
                    <form id="form-lapor-pelanggaran" action="{{ route('violations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            @if ($errors->any())
                                {{-- Pesan error tetap menggunakan alert-danger untuk penekanan --}}
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan:</strong>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="form-group mb-3">
                                <label for="class_autocomplete">Pilih Kelas <span class="text-danger">*</span></label>
                                <input type="text" id="class_autocomplete" class="form-control" placeholder="Ketik nama kelas..." autocomplete="off" required>
                                <input type="hidden" name="class_id" id="class_id" value="{{ old('class_id') }}">
                                @error('class_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                {{-- Tanda * tetap text-danger untuk menandakan wajib --}}
                                <label for="student_id">Siswa <span class="text-danger">*</span></label>
                                <input type="text" id="student_autocomplete" class="form-control" placeholder="Ketik nama siswa..." autocomplete="off" required disabled>
                                <input type="hidden" name="student_id" id="student_id" value="{{ old('student_id') }}">
                                @error('student_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="violation_point_autocomplete">Jenis Pelanggaran <span class="text-danger">*</span></label>
                                <input type="text" id="violation_point_autocomplete" class="form-control @error('violation_points_id') is-invalid @enderror" placeholder="Cari jenis/level pelanggaran..." autocomplete="off" required>
                                <input type="hidden" name="violation_points_id" id="violation_points_id" value="{{ old('violation_points_id') }}">
                                @error('violation_points_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- TAMBAHAN: Input untuk Tahun Akademik --}}
                            <div class="form-group mb-3">
                                <label for="academic_year_id">Tahun Akademik <span class="text-danger">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Tahun Akademik --</option>
                                    @if(isset($academicYears) && $academicYears->count() > 0)
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->start_year }}/{{ $year->end_year }} {{ $year->semester == 0 ? 'Genap' : 'Ganjil' }}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Tidak ada data tahun akademik</option>
                                    @endif
                                </select>
                                @error('academic_year_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="violation_date">Tanggal Pelanggaran <span class="text-danger">*</span></label>
                                <input type="date" name="violation_date" id="violation_date" class="form-control @error('violation_date') is-invalid @enderror" value="{{ old('violation_date', date('Y-m-d')) }}" required>
                                @error('violation_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Deskripsi / Kronologi / Keterangan Tambahan <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- TAMBAHAN: Input untuk Sanksi/Penalti (Opsional)
                            <div class="form-group mb-3">
                                <label for="penalty">Sanksi/Penalti yang Diberikan (Opsional)</label>
                                <input type="text" name="penalty" id="penalty" class="form-control @error('penalty') is-invalid @enderror" value="{{ old('penalty') }}">
                                @error('penalty')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div> --}}

                            <div class="form-group mb-3">
                                <label for="evidence">Bukti (Opsional, Gambar: jpg, jpeg, png. Maks: 2MB)</label>
                                <div class="custom-file">
                                    <input type="file" name="evidence" id="evidence" class="custom-file-input @error('evidence') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                                    <label class="custom-file-label" for="evidence">Pilih file...</label>
                                </div>
                                <small class="form-text text-muted">Jika ada, unggah bukti berupa gambar. Biarkan kosong jika tidak ada.</small>
                                @error('evidence')
                                    <span class="invalid-feedback d-block" role="alert"> {{-- d-block untuk custom-file --}}
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <input type="hidden" name="status" value="pending"> {{-- Pastikan input hidden status ada --}}
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('violations.index') }}" class="btn btn-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
                                {{-- Mengubah btn-danger menjadi btn-primary --}}
                                <button type="button" id="btn-lapor-pelanggaran" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> Laporkan Pelanggaran</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
{{-- Pastikan jQuery sudah termuat sebelum skrip ini --}}
{{-- Jika Anda menggunakan AdminLTE, jQuery biasanya sudah ada --}}
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}} {{-- Contoh jika jQuery belum ada --}}

{{-- Skrip untuk bs-custom-file-input jika belum di-include global oleh AdminLTE --}}
{{-- Contoh: <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script> --}}
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Inisialisasi bsCustomFileInput jika tersedia
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.init();
        } else {
            // Fallback manual jika bsCustomFileInput tidak ada
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName || "Pilih file...");
            });
        }

        // Validasi ukuran file di sisi client (opsional)
        $('#evidence').on('change', function() {
            if (this.files[0]) {
                const fileSize = this.files[0].size / 1024 / 1024; // in MB
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                const fileType = this.files[0].type;

                if (!allowedTypes.includes(fileType)) {
                    alert('Tipe file bukti harus berupa JPG, JPEG, atau PNG.');
                    $(this).val('');
                    $(this).next('.custom-file-label').html('Pilih file...');
                    return;
                }

                if (fileSize > 2) { // Batas 2MB
                    alert('Ukuran file bukti tidak boleh melebihi 2MB.');
                    $(this).val('');
                    $(this).next('.custom-file-label').html('Pilih file...');
                } else {
                    // Nama file sudah dihandle oleh bsCustomFileInput atau fallback di atas
                }
            } else {
                 $(this).next('.custom-file-label').html('Pilih file...');
            }
        });

        // Untuk dismiss alert otomatis
        setTimeout(function() {
            $(".alert-dismissible").alert('close');
        }, 7000); // Alert akan hilang setelah 7 detik

        $('#btn-lapor-pelanggaran').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Laporan',
                text: 'Anda yakin ingin melaporkan pelanggaran ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Laporkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'swal2-confirm btn btn-primary mx-2',
                    cancelButton: 'swal2-cancel btn btn-secondary mx-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#form-lapor-pelanggaran').submit();
                }
            });
        });
    });

    $(function() {
        // Autocomplete kelas
        $("#class_autocomplete").autocomplete({
            source: "{{ route('autocomplete.classes') }}",
            minLength: 0,
            select: function(event, ui) {
                $('#class_id').val(ui.item.id);
                $('#class_autocomplete').val(ui.item.value);
                // Enable student field
                $('#student_autocomplete').prop('disabled', false).val('');
                $('#student_id').val('');
            }
        }).on('focus', function () {
            $(this).autocomplete("search", "");
        });
        // Autocomplete siswa berdasarkan kelas
        $("#student_autocomplete").autocomplete({
            source: function(request, response) {
                var classId = $('#class_id').val();
                if (!classId) return response([]);
                $.getJSON("{{ route('autocomplete.siswa-by-class') }}", {
                    term: request.term,
                    class_id: classId
                }, response);
            },
            minLength: 0,
            select: function(event, ui) {
                $('#student_id').val(ui.item.id);
                $('#student_autocomplete').val(ui.item.value);
            }
        }).on('focus', function () {
            var classId = $('#class_id').val();
            if (classId) $(this).autocomplete("search", "");
        });
        // Autocomplete jenis pelanggaran
        $("#violation_point_autocomplete").autocomplete({
            source: "{{ route('autocomplete.violation-points') }}",
            minLength: 0, // ubah dari 2 ke 0
            select: function(event, ui) {
                $('#violation_points_id').val(ui.item.id);
                $('#violation_point_autocomplete').val(ui.item.value);
            }
        }).on('focus', function () {
            // Tampilkan semua jenis pelanggaran saat field difokuskan
            $(this).autocomplete("search", "");
        });
    });
</script>
@endpush
