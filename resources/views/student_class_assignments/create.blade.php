{{-- Hapus semua isi file lama Anda dan ganti dengan kode ini --}}
@extends('layouts.app')

@section('title', isset($selectedClass) ? 'Plotting Siswa Ke Kelas ' . $selectedClass->name . ' ' . $selectedClass->parallel_name : 'Plotting Siswa Ke Kelas')

@push('css')
    {{-- CSS Library --}}
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- CSS Kustom untuk Halaman Ini (Digabung Jadi Satu) --}}
    <style>
        /* Font Dasar & Tabel */
        #studentsTable,
        #studentsTable thead th,
        #studentsTable tbody td {
            font-size: 14px;
        }
        #studentsTable thead th,
        #studentsTable tbody td {
            padding: 8px 12px;
            vertical-align: middle;
            line-height: 1.4;
        }
        .table thead th {
            text-align: center;
            vertical-align: middle;
        }
        .dt-center-flex-header {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        .table thead th:nth-child(4), /* Kolom Nama */
        .table tbody td:nth-child(4) {
            text-align: left;
        }
/* 1. Seragamkan properti semua sel di dalam tbody */
#studentsTable tbody tr {
    height: 42px !important; /* Contoh: 42px. Sesuaikan nilainya jika perlu. */
}

/* 2. Pastikan semua sel <td> memiliki properti yang seragam */
#studentsTable tbody td {
    padding: 8px 12px !important;
    vertical-align: middle !important;
    border: none !important; /* Hapus semua border individual sel */
}

/* 3. Tambahkan kembali hanya border bawah pada baris untuk pemisah */
#studentsTable tbody tr {
    border-bottom: 1px solid #e9ecef;
}

/* 4. Atur warna latar belakang secara terpisah (ini tidak mempengaruhi tinggi) */
#studentsTable tbody tr:nth-child(odd) {
    background-color: #f8f9fa;
}
#studentsTable tbody tr:nth-child(even) {
    background-color: #ffffff;
}

/* 5. Metode positioning absolut untuk checkbox yang lebih reliable */
#studentsTable > tbody > tr > td.checkbox-cell {
    position: relative;
    padding: 0 !important; /* Kolom checkbox tidak perlu padding */
    width: 60px !important; /* Pastikan lebar kolom konsisten */
    height: 42px !important; /* Pastikan tinggi kolom konsisten */
    text-align: center;
    vertical-align: middle;
}
.checkbox-container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
}
.checkbox-container .form-check-input {
    margin: 0;
    width: 18px;
    height: 18px;
    cursor: pointer;
    position: relative;
}
        /* Panel Kontrol */
        /* Hapus style lama untuk .confirmation-card dan .control-group, ganti dengan ini */

.confirmation-card {
    background-color: #ffffff; /* Latar belakang putih bersih */
    border: 1px solid #e9ecef;
    border-radius: 12px; /* Sudut lebih melengkung */
    padding: 2rem;
    box-shadow: 0 8px 30px rgba(0, 86, 179, 0.07); /* Shadow lebih lembut */
}

/* Style untuk setiap kotak info di dalam panel */
.control-item {
    /* ... style .control-item Anda yang sudah ada ... */
    display: flex; align-items: center; background-color: #fff;
    border: 2px solid #e9ecef; /* Buat border sedikit lebih tebal untuk efek aktif */
    border-radius: 8px; padding: 1rem; height: 100%;
    transition: all 0.2s ease-in-out;
}
.control-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* Style BARU untuk state "aktif" ketika siswa dipilih */
.control-item.is-active {
    border-color: #0d6efd; /* Ganti warna border menjadi biru */
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.1); /* Tambahkan efek glow biru */
}
.control-item.is-active .control-item__value {
    color: #0d6efd; /* Ganti warna teks value menjadi biru */
}
.control-item.is-active .control-item__icon {
    transform: scale(1.1); /* Sedikit perbesar ikon saat aktif */
}

/* Style lain untuk control-item tetap sama */
.control-item__icon {
    flex-shrink: 0; width: 48px; height: 48px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin-right: 1rem; font-size: 1.2rem; transition: transform 0.2s ease-in-out;
}
.icon-blue { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
.icon-green { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
.icon-purple { background-color: rgba(111, 66, 193, 0.1); color: #6f42c1; }

.control-item__content { flex-grow: 1; }
.control-item__label { font-size: 0.8rem; color: #6c757d; margin-bottom: 0.1rem; text-transform: uppercase; letter-spacing: 0.5px; }
.control-item__value { font-size: 1.1rem; font-weight: 600; color: #212529; transition: color 0.2s ease-in-out; }
.control-item__sub-label { font-size: 0.8rem; color: #6c757d; margin-top: -2px;
/* Tombol konfirmasi agar lebih serasi */
}

/* Dropdown minimalis jika T.A. perlu dipilih */
.form-select-minimal {
    border: none;
    background-color: transparent;
    padding: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #212529;
    cursor: pointer;
}
.form-select-minimal:focus {
    box-shadow: none;
}

/* Penyesuaian untuk layar lebih kecil */
@media (max-width: 991.98px) {
    .control-item {
        margin-bottom: 1rem;
    }
}
@media (max-width: 767.98px) {
    .control-item {
        flex-direction: column;
        text-align: center;
    }
    .control-item__icon {
        margin-right: 0;
        margin-bottom: 0.75rem;
    }
    #confirmBtn {
        margin-top: 1rem;
    }
}

        /* Tombol Konfirmasi */
       #confirmBtn {
    font-size: 1rem !important;
    font-weight: 600 !important;
    padding: 1rem;
    border-radius: 8px; /* Samakan dengan radius kotak info */
    border: none;
    transition: all 0.3s ease;
    background: linear-gradient(45deg, #0d6efd, #0dcaf0); /* Gradient biru yang modern */
    color: white;
    box-shadow: 0 4px 20px rgba(13, 110, 253, 0.25);
}
#confirmBtn:hover:not(:disabled) {
    transform: translateY(-3px);
    box-shadow: 0 7px 25px rgba(13, 110, 253, 0.35);
}
#confirmBtn:disabled {
    background: #e9ecef !important; /* Latar abu-abu netral saat disabled */
    color: #adb5bd !important;
    box-shadow: none !important;
    cursor: not-allowed;
    transform: none !important;
}

        /* Style Lain-lain */
        .badge-no-class { background-color: #dc3545; color: white; padding: 5px 8px; border-radius: 4px; font-size: 13px; }
        .badge-has-class { background-color: #28a745; color: white; padding: 5px 8px; border-radius: 4px; font-size: 13px; }
        .select-all-container { display: flex; justify-content: flex-end; margin-bottom: 1rem; }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4" style="font-weight: bold; font-size: 2rem; color: #183C70;">
            Plotting Siswa
            @if (isset($selectedClass))
                Ke Kelas: {{ $selectedClass->name }} {{ $selectedClass->parallel_name }}
            @endif
        </h1>

        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0" style="font-weight: 600; color: #183C70;">Pilih Siswa</h5>
                    @can('create_student') {{-- Sesuaikan dengan permission Anda --}}
                        <a href="{{ route('manage-classes.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Data Kelas
                        </a>
                    @endcan
                </div>

                <div class="select-all-container">
                    <label class="form-check-label">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                        Pilih Semua Siswa
                    </label>
                </div>

                <div class="table-responsive">
                    <table id="studentsTable" class="table table-bordered table-hover" style="width:100%;">
                        <thead style="background-color: #009cf3; color: white;">
                            <tr>
                                <th>No.</th>
                                <th>NISN</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Tahun Masuk</th>
                                <th>Kelas Saat Ini</th>
                                <th>Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan diisi oleh DataTables dari server --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="confirmation-card mt-4">
    <div class="text-center mb-4 pt-2">
        <h5 style="font-weight: 600; color: #003366;">
            <i class="fas fa-cogs me-2"></i>Panel Kontrol Penempatan Siswa
        </h5>
    </div>

    {{-- Input hidden yang penting untuk menyimpan ID --}}
    @if (isset($selectedClassId))
        <input type="hidden" id="targetClassIdHidden" value="{{ $selectedClassId }}">
    @endif
    @if (isset($selectedClass) && $selectedClass->academic_year_id)
        <input type="hidden" id="targetAcademicYearIdHidden" value="{{ $selectedClass->academic_year_id }}">
    @endif

    {{-- Struktur Grid Baru yang Rapi dan Informatif --}}
    <div class="row g-3 justify-content-center">

        <div class="col-lg-3 col-md-6">
            <div class="control-item">
                <div class="control-item__icon icon-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="control-item__content">
                    <div class="control-item__label">Siswa Dipilih</div>
                    <div id="selectedCount" class="control-item__value">0 Siswa</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="control-item">
                <div class="control-item__icon icon-green">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="control-item__content">
                    <div class="control-item__label">Tahun Akademik</div>
                    <div class="control-item__value">
                        @if (isset($selectedClass) && $selectedClass->academicYear)
                            {{ $selectedClass->academicYear->start_year }}/{{ $selectedClass->academicYear->end_year }}
                        @else
                            {{-- Jika T.A. perlu dipilih manual, akan muncul di sini --}}
                            <select id="targetYear" name="academic_year_id" class="form-select-minimal" required>
                                <option value="">-- Pilih Tahun --</option>
                                @if(isset($academicYears))
                                    @foreach ($academicYears as $ay)
                                        <option value="{{ $ay->id }}">{{ $ay->start_year }}/{{ $ay->end_year }} - {{ $ay->semester == 1 ? 'Ganjil' : ($ay->semester == 0 ? 'Ganjil' : 'Genap') }}</option>
                                    @endforeach
                                @endif
                            </select>
                        @endif
                    </div>
                    @if(isset($selectedClass) && $selectedClass->academicYear)
                         <small class="control-item__sub-label">Semester {{ $selectedClass->academicYear->semester == 1 ? 'Ganjil' : ($selectedClass->academicYear->semester == 0 ? 'Ganjil' : 'Genap') }}</small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="control-item">
                <div class="control-item__icon icon-purple">
                    <i class="fas fa-school"></i>
                </div>
                <div class="control-item__content">
                    <div class="control-item__label">Kelas Tujuan</div>
                    <div class="control-item__value">
                        @if (isset($selectedClass))
                            {{ $selectedClass->name }} {{ $selectedClass->parallel_name }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 d-flex align-items-stretch">
            <button id="confirmBtn" class="btn btn-confirm w-100 h-100" disabled>
                <i class="fas fa-check-circle me-2"></i>Konfirmasi Penempatan
            </button>
        </div>
    </div>
</div>
@endsection

@push('js')
    {{-- JS Library --}}
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- JS Kustom untuk Halaman Ini --}}
    <script>
        $(function() {
            // Setup CSRF Token untuk semua request AJAX
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            let selectedStudents = new Set();

            // Ambil ID kelas & T.A. jika sudah ditentukan dari server
            const PRE_SELECTED_CLASS_ID = $('#targetClassIdHidden').val() || null;
            const PRE_SELECTED_CLASS_NAME = PRE_SELECTED_CLASS_ID ? "{{ isset($selectedClass) ? htmlspecialchars($selectedClass->name . ' ' . $selectedClass->parallel_name, ENT_QUOTES) : '' }}" : "";
            let PRE_SELECTED_ACADEMIC_YEAR_ID = $('#targetAcademicYearIdHidden').val() || null;

            // Tentukan URL AJAX berdasarkan apakah kelas sudah dipilih atau belum
            let ajaxUrl;
            @if (isset($selectedClassId))
                ajaxUrl = '{{ route("manage-student-class-assignments.create-for-class", $selectedClassId) }}';
            @else
                ajaxUrl = '{{ route("manage-student-class-assignments.create") }}';
            @endif

            const table = $('#studentsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: ajaxUrl,
                    error: function(xhr, error, thrown) {
                        console.error('Error DataTables:', error, thrown);
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false, width: '50px' },
                    { data: 'nisn', name: 'nisn', className: 'text-center', width: '100px' },
                    { data: 'nis', name: 'nis', className: 'text-center', width: '100px' },
                    { data: 'name', name: 'name', className: 'text-left' },
                    { data: 'gender', name: 'gender', className: 'text-center', width: '120px' },
                    { data: 'enter_year', name: 'enter_year', className: 'text-center', width: '100px' },
                    {
                        data: 'class_name', name: 'class_name', className: 'text-center', width: '120px',
                        render: function(data, type) {
                            if (type === 'display') {
                                return data && data !== '-' ? `<span class="badge-has-class">${data}</span>` : `<span class="badge-no-class">Belum Ada Kelas</span>`;
                            }
                            return data;
                        },
                        orderable: false // Kolom kelas saat ini tidak bisa diurutkan karena merupakan data yang diproses
                    },
                    {
                        data: 'nisn', title: 'Pilih', orderable: false, searchable: false, className: 'checkbox-cell text-center', headerClassName: 'dt-center-flex-header', width: '60px',
                        render: function(data) {
                           return `<div class="checkbox-container"><input type="checkbox" class="row-select form-check-input" value="${data}"></div>`;
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('height', '42px');
                        }
                    }
                ],
                order: [ [3, 'asc'] ], // Default sorting by name
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>',
                    search: "Cari Siswa:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
                    infoEmpty: "Menampilkan 0 dari 0 siswa",
                    infoFiltered: "(disaring dari _MAX_ total siswa)",
                    zeroRecords: "Tidak ada siswa yang ditemukan",
                    paginate: { first: "Awal", last: "Akhir", next: "Berikutnya", previous: "Sebelumnya" }
                },
                drawCallback: function() {
                    $('.row-select').each(function() {
                        $(this).prop('checked', selectedStudents.has($(this).val()));
                    });
                    updateCount();
                    updateSelectAllState();
                }
            });

                    // GANTI FUNGSI updateCount() LAMA ANDA DENGAN YANG INI
            function updateCount() {
                const count = selectedStudents.size;
                $('#selectedCount').text(count + ' Siswa');

                // LOGIKA BARU: Tambah/hapus kelas 'is-active' pada seluruh kotak info
                if (count > 0) {
                    $('#info-box-selected').addClass('is-active');
                } else {
                    $('#info-box-selected').removeClass('is-active');
                }

                // Fungsi untuk mengaktifkan/menonaktifkan tombol konfirmasi tetap dipanggil
                updateConfirmButton();
            }

            function updateSelectAllState() {
                const allCheckboxes = $('.row-select');
                const checkedCheckboxes = $('.row-select:checked');
                if (allCheckboxes.length > 0 && checkedCheckboxes.length === allCheckboxes.length) {
                    $('#selectAll').prop({ indeterminate: false, checked: true });
                } else {
                    $('#selectAll').prop({ indeterminate: checkedCheckboxes.length > 0, checked: false });
                }
            }

            function updateConfirmButton() {
                const hasSelectedStudents = selectedStudents.size > 0;
                // Jika T.A ditentukan oleh kelas, atau jika dipilih dari dropdown
                const targetYearValue = PRE_SELECTED_ACADEMIC_YEAR_ID || $('#targetYear').val();
                const hasTargetYear = targetYearValue !== '';
                $('#confirmBtn').prop('disabled', !(hasSelectedStudents && hasTargetYear));
            }

            $('#studentsTable tbody').on('change', '.row-select', function() {
                const nisn = $(this).val();
                this.checked ? selectedStudents.add(nisn) : selectedStudents.delete(nisn);
                updateCount();
                updateSelectAllState();
            });

            $('#selectAll').on('change', function() {
                const isChecked = this.checked;
                $('.row-select').each(function() {
                    const nisn = $(this).val();
                    $(this).prop('checked', isChecked);
                    isChecked ? selectedStudents.add(nisn) : selectedStudents.delete(nisn);
                });
                updateCount();
            });

            // Hanya aktifkan event listener ini jika T.A. perlu dipilih manual
            if (!PRE_SELECTED_ACADEMIC_YEAR_ID) {
                $('#targetYear').on('change', updateConfirmButton);
            }

            $('#confirmBtn').on('click', function() {
                const nisns = Array.from(selectedStudents);
                const kelasId = PRE_SELECTED_CLASS_ID; // Sudah pasti dari sini
                const tahunAkademikId = PRE_SELECTED_ACADEMIC_YEAR_ID || $('#targetYear').val();
                const tahunAkademikText = PRE_SELECTED_ACADEMIC_YEAR_ID ? $('.info-text:contains("/")').text().trim() : $('#targetYear option:selected').text();

                if (nisns.length === 0) {
                    Swal.fire('Peringatan', 'Pilih minimal satu siswa untuk dipindahkan!', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Penempatan',
                    html: `<div style="text-align: left; padding: 10px;">
                               <p>Anda akan memindahkan <strong>${nisns.length} siswa</strong>.</p>
                               <p><strong>Kelas Tujuan:</strong> ${PRE_SELECTED_CLASS_NAME}</p>
                               <p><strong>Tahun Akademik:</strong> ${tahunAkademikText}</p>
                               <hr>
                               <p class="text-danger fw-bold">Apakah Anda yakin ingin melanjutkan?</p>
                           </div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Pindahkan!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#dc3545',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.post('{{ route('manage-student-class-assignments.store') }}', {
                            nisns: nisns,
                            academic_year_id: tahunAkademikId,
                            class_id: kelasId
                        }).fail(function(jqXHR) {
                            Swal.showValidationMessage(`Request Gagal: ${jqXHR.responseJSON?.message || jqXHR.statusText}`);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.value.message || `${nisns.length} siswa berhasil dipindahkan.`,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        selectedStudents.clear();
                        table.ajax.reload(null, false);
                    }
                });
            });

            updateCount(); // Panggil saat inisialisasi
        });
    </script>
@endpush
