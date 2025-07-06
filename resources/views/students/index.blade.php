@extends('layouts.app')

@section('title', 'Manajemen Data Siswa')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0" style="font-weight: 400;">MANAJEMEN DATA SISWA</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{-- Tambahkan breadcrumb jika diperlukan --}}
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline" style="border-top: 3px solid #009cf3;">
                        <div class="card-header d-flex align-items-center">
                            <span class="card-title m-0 flex-grow-1" style="font-weight: 400;">Kelola Data Siswa</span>
                            <div class="card-tools ms-auto">
                                @can('create_student')
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="tambahDataDropdown"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-plus"></i> Tambah Data
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right shadow-lg rounded border-0" style="min-width: 230px;"
                                            aria-labelledby="tambahDataDropdown">
                                            <a class="dropdown-item d-flex align-items-center py-2"
                                                href="{{ route('manage-students.create') }}">
                                                <i class="fas fa-keyboard text-primary mr-3"></i> <span>Isi Manual</span>
                                            </a>
                                            <a class="dropdown-item d-flex align-items-center py-2" href="#" id="importExcel">
                                                <i class="fas fa-file-excel text-success mr-3"></i> <span>Import Excel</span>
                                            </a>
                                            <div class="dropdown-divider my-1"></div>
                                            <a class="dropdown-item d-flex align-items-center py-2"
                                                href="{{ route('manage-students.template') }}">
                                                <i class="fas fa-download text-info mr-3"></i> <span>Download Template Excel</span>
                                            </a>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="studentsTable" class="table table-bordered table-striped">
                                    <thead class="bg-tertiary text-white" style="border-top: none !important;">
                                        <tr>
                                            <th>No.</th>
                                            <th>NISN</th>
                                            <th>NIS</th>
                                            <th>Nama</th>
                                            <th>Alamat</th>
                                            <th>Telepon</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Tahun Masuk</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    {{-- tbody tetap diisi oleh DataTables --}}
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Siswa -->
    <div class="modal fade" id="studentDetailModal" tabindex="-1" role="dialog" aria-labelledby="studentDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="studentDetailBody"></div>
            </div>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('manage-students.import') }}" method="POST" enctype="multipart/form-data"
                class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importExcelModalLabel">Import Data Siswa dari Excel</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">Pilih File Excel</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file" name="file" required
                                accept=".xlsx, .xls">
                            <label class="custom-file-label" for="file">Pilih file</label>
                        </div>
                        <small class="form-text text-muted">File harus dalam format .xlsx atau .xls</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <style>
        body, .dataTables_wrapper, .dataTables_wrapper label, .dataTables_wrapper input, .dataTables_wrapper select, .dataTables_info, .dataTables_length, .dataTables_filter, .dataTables_paginate, .paginate_button {
            font-family: 'Roboto', Arial, sans-serif !important;
            font-size: 1rem !important;
        }
        .content-header h4, .card-title, h3, h4, h5 {
            color: #222 !important;
            font-family: 'Roboto', Arial, sans-serif !important;
            font-weight: 400 !important;
            letter-spacing: 0.01em;
        }
        #studentsTable thead th {
            font-weight: 700;
            font-size: 1rem;
        }
        #studentsTable thead {
            border-top: none !important;
        }
        #studentsTable tbody td {
            font-size: 1rem;
            color: #495057;
        }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            color: #222 !important;
            font-weight: 400;
        }
        .dataTables_wrapper .dataTables_info {
            color: #222 !important;
        }
        .dataTables_wrapper .paginate_button {
            color: #222 !important;
        }
        .dataTables_wrapper .paginate_button.current {
            background: #009cf3 !important;
            color: #fff !important;
        }
        /* Tambahan agar hover dropdown identik dengan teachers */
        .dropdown-menu a.dropdown-item {
            font-size: 0.95rem;
            padding: 10px 18px;
        }
        .dropdown-menu a.dropdown-item:hover {
            background-color: #f5faff;
            color: #0d6efd;
        }
        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 0.75rem;
        }
        .dropdown-divider {
            margin: 0.5rem 0;
        }
        .dropdown-item .text-primary {
            color: #1777e5 !important;
        }
        .dropdown-item .text-success {
            color: #28a745 !important;
        }
        .dropdown-item .text-info {
            color: #17a2b8 !important;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function() {
            bsCustomFileInput.init();

            $('#studentsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: 'simple_numbers',
                ajax: '{{ route('manage-students.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nisn',
                        name: 'nisn'
                    },
                    {
                        data: 'nis',
                        name: 'nis'
                    }, // Kolom tambahan NIS
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            return `<a href="#" class="student-detail-link" data-nisn="${row.nisn}">${data}</a>`;
                        }
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'enter_year',
                        name: 'enter_year'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                    <a href="/manage-students/${row.nisn}/edit" class="btn btn-sm btn-warning mr-1" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-sm btn-danger" title="Hapus" onclick="deleteStudent('${row.nisn}')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                `;
                        }
                    }
                ]

            });

            $('#importExcel').click(function() {
                $('#importExcelModal').modal('show');
            });

            $('#studentsTable').on('click', '.student-detail-link', function(e) {
                e.preventDefault();
                const nisn = $(this).data('nisn');
                $.ajax({
                    url: `/manage-students/${nisn}`, // pakai route resource: show()
                    method: 'GET',
                    success: function(data) {
                        $('#studentDetailBody').html(`
                <div class="row">
                    <div class="col-md-4 text-center">
                        ${data.photo_url
                            ? `<img src="${data.photo_url}" alt="Foto" class="img-thumbnail" style="max-width: 150px;">`
                            : `<div class="text-muted mt-4">Mohon Edit Data Untuk Menambahkan Foto</div>`
                        }
                    </div>
                    <div class="col-md-8">
                        <table class="table table-sm">
                            <tr><th>NIS</th><td>${data.nis ?? '-'}</td></tr>
                            <tr><th>NISN</th><td>${data.nisn}</td></tr>
                            <tr><th>Nama</th><td>${data.name}</td></tr>
                            <tr><th>Alamat</th><td>${data.address}</td></tr>
                            <tr><th>Telepon</th><td>${data.phone}</td></tr>
                            <tr><th>Jenis Kelamin</th><td>${data.gender}</td></tr>
                            <tr><th>Tahun Masuk</th><td>${data.enter_year}</td></tr>
                            <tr><th>Nama Orang Tua</th><td>${data.parent_name ?? '-'}</td></tr>
                            <tr><th>Email Orang Tua</th><td>${data.parent_email ?? '-'}</td></tr>
                            <tr><th>Telepon Orang Tua</th><td>${data.parent_phone ?? '-'}</td></tr>
                            <tr>
                                <th>Tanggal Lahir</th>
                                <td>
                                    ${
                                        data.birth_date
                                            ? new Date(data.birth_date).toLocaleDateString('id-ID', {
                                                day: '2-digit',
                                                month: 'long',
                                                year: 'numeric'
                                            })
                                            : '-'
                                    }
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            `);
                        $('#studentDetailModal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Tidak dapat mengambil data siswa.', 'error');
                    }
                });
            });

            $('#studentDetailModal').on('click', '.close', function() {
                $('#studentDetailModal').modal('hide');
            });
        });

        function deleteStudent(nisn) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data siswa akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/manage-students/${nisn}`, // URL dengan nisn sebagai parameter
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}" // Menambahkan CSRF token untuk keamanan
                        },
                        success: function(response) {
                            Swal.fire('Terhapus!', response.message, 'success');
                            $('#studentsTable').DataTable().ajax
                                .reload(); // Memuat ulang tabel setelah penghapusan
                        },
                        error: function() {
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                        }
                    });
                }
            });
        }

        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if (session('error'))
            toastr.error('{!! session('error') !!}');
        @endif
    </script>
@endpush
