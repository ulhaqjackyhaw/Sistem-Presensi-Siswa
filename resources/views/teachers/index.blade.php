@extends('layouts.app')

@section('title', 'Manajemen Data Guru')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Manajemen Data Guru</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Kelola Data Guru</h3>
                            <div class="card-tools">
                                @can('create_teacher')
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                            id="tambahDataDropdown" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fas fa-plus"></i> Tambah Data
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="tambahDataDropdown">
                                            <a class="dropdown-item d-flex align-items-center py-2"
                                                href="{{ route('manage-teachers.create') }}">
                                                <i class="fas fa-keyboard mr-3 text-primary"></i> <span>Isi Manual</span>
                                            </a>
                                            <a class="dropdown-item d-flex align-items-center py-2" href="#"
                                                id="importExcel">
                                                <i class="fas fa-file-excel mr-3 text-success"></i> <span>Import Excel</span>
                                            </a>
                                            <div class="dropdown-divider my-1"></div>
                                            <a class="dropdown-item d-flex align-items-center py-2"
                                                href="{{ route('manage-teachers.template') }}">
                                                <i class="fas fa-download mr-3 text-info"></i> <span>Download Template
                                                    Excel</span>
                                            </a>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="teachersTable" class="table table-bordered table-striped"
                                    style="border-radius: 10px;">
                                    <thead style="background-color: #009cf3; color: white;">
                                        <tr>
                                            <th>No.</th>
                                            <th>NIP</th>
                                            <th>Nomor Dapodik</th>
                                            <th>Nama Guru</th>
                                            <th>Email</th>
                                            <th>No. Telp</th>
                                            <th>Alamat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importExcelModalLabel">Import Data Guru dari Excel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('manage-teachers.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
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
    </div>

    <!-- Modal Detail Guru -->
    <div class="modal fade" id="teacherDetailModal" tabindex="-1" role="dialog" aria-labelledby="teacherDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Guru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="teacherDetailBody"></div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .table thead th,
        .table tbody td {
            vertical-align: middle;
            text-align: left;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.3rem;
        }

        .btn-primary {
            background-color: #1777e5;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1266c4;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2rem 0.4rem;
            margin-left: 2px;
            background-color: #f4f4f4;
            border-radius: 6px;
            border: 1px solid #ddd;
            color: #1777e5 !important;
            font-size: 0.75rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #1777e5 !important;
            color: white !important;
            border: 1px solid #1777e5;
            font-size: 0.75rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0.5rem;
            display: flex;
            justify-content: end;
            align-items: center;
            font-size: 0.75rem;
        }

        .dataTables_info {
            color: #1777e5;
            font-size: 0.90rem;
            margin-top: 0.5rem;
        }

        .dropdown-menu {
            min-width: 230px;
            z-index: 1050 !important;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            border: none;
            /* Remove default border */
        }

        .dropdown-menu a.dropdown-item {
            font-size: 0.95rem;
            padding: 10px 18px;
            /* Tambahkan padding */
        }

        .dropdown-menu a.dropdown-item:hover {
            background-color: #f5faff;
            /* Warna latar saat hover */
            color: #0d6efd;
            /* Warna teks saat hover */
        }

        .dropdown-item i {
            width: 20px;
            /* Atur lebar ikon */
            text-align: center;
            margin-right: 0.75rem;
            /* Tambahkan margin kanan */
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            /* Sesuaikan margin divider */
        }

        /* Tambahkan gaya untuk warna ikon */
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
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- DataTables Buttons -->
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <!-- BS Custom File Input -->
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Add Bootstrap Bundle JS for modal functionality --}}
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        $(function() {
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error('{{ $error }}');
                @endforeach
            @endif
        });
    </script>

    <script>
        $(function() {
            bsCustomFileInput.init();

            $('#teachersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('manage-teachers.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nip',
                        name: 'nip'
                    },
                    {
                        data: 'dapodik_number',
                        name: 'dapodik_number'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            return `<a href="#" class="teacher-detail-link" data-nip="${row.nip}">${data}</a>`;
                        }
                    },
                    {
                        data: 'email',
                        name: 'users.email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Handle Import Excel button
            $('#importExcel').click(function() {
                $('#importExcelModal').modal('show');
            });

            // Handler klik nama guru
            $('#teachersTable').on('click', '.teacher-detail-link', function(e) {
                e.preventDefault();
                const nip = $(this).data('nip');
                $.ajax({
                    url: `/manage-teachers/${nip}`,
                    method: 'GET',
                    success: function(data) {
                        let subjectsHtml = '';
                        // if (data.subjects && data.subjects.length > 0) {
                        //     subjectsHtml = '<tr><th>Mata Pelajaran</th><td><ul class="list-unstyled mb-0">';
                        //     data.subjects.forEach(function(item) {
                        //         subjectsHtml += `<li>${item.subject} (${item.class})</li>`;
                        //     });
                        //     subjectsHtml += '</ul></td></tr>';
                        // } else {
                        //     subjectsHtml = '<tr><th>Mata Pelajaran</th><td>Belum ada penugasan mengajar</td></tr>';
                        // }

                        $('#teacherDetailBody').html(`
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    ${data.photo_url
                                        ? `<img src="${data.photo_url}" alt="Foto" class="img-thumbnail" style="max-width: 150px;">`
                                        : `<div class="text-muted mt-4">Belum ada foto</div>`
                                    }
                                </div>
                                <div class="col-md-8">
                                    <table class="table table-sm">
                                        <tr><th>NIP</th><td>${data.nip ?? '-'}</td></tr>
                                        <tr><th>Nomor Dapodik</th><td>${data.dapodik_number ?? '-'}</td></tr>
                                        <tr><th>Nama</th><td>${data.name}</td></tr>
                                        <tr><th>Email</th><td>${data.email ?? '-'}</td></tr>
                                        <tr><th>No. Telp</th><td>${data.phone ?? '-'}</td></tr>
                                        <tr><th>Alamat</th><td>${data.address ?? '-'}</td></tr>
                                        <tr><th>Jenis Kelamin</th><td>${data.gender ?? '-'}</td></tr>
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
                                        <tr><th>Status</th><td>${data.status ?? '-'}</td></tr>
                                        ${subjectsHtml}
                                    </table>
                                </div>
                            </div>
                        `);
                        $('#teacherDetailModal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Tidak dapat mengambil data guru.', 'error');
                    }
                });
            });

            $('#teacherDetailModal').on('click', '.close', function() {
                $('#teacherDetailModal').modal('hide');
            });
        });

        function confirmDelete(nip) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + nip).submit();
                }
            });
        }

        // Handle success message
        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        function jadikanGuruBk(nip) {
            Swal.fire({
                title: 'Jadikan Guru BK?',
                text: 'Guru akan mendapatkan role Guru BK.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, jadikan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/manage-teachers/' + nip + '/jadikan-guru-bk',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire('Berhasil', response.message, 'success');
                            } else {
                                Swal.fire('Info', response.message, 'info');
                            }
                            $('#teachersTable').DataTable().ajax.reload();
                        },
                        error: function() {
                            Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
