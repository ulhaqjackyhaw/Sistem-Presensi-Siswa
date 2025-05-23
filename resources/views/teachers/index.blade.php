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
                                            <a class="dropdown-item" href="{{ route('manage-teachers.create') }}">
                                                <i class="fas fa-keyboard mr-2"></i> Isi Manual
                                            </a>
                                            <a class="dropdown-item" href="#" id="importExcel">
                                                <i class="fas fa-file-excel mr-2"></i> Import Excel
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('manage-teachers.template') }}">
                                                <i class="fas fa-download mr-2"></i> Download Template Excel
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
                                            <th>Nama Guru</th>
                                            <th>Email</th>
                                            <th>No. Telp</th>
                                            <th>Alamat</th>
                                            <th>Role</th>
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
@endsection

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <style>
        .dropdown-menu { min-width: 100px; z-index: 1050 !important; }
        .dropdown-menu a { font-size: 0.95rem; }
        .dropdown-menu .dropdown-item.text-danger { color: #e74c3c !important; font-weight: 500; }
        .dropdown-menu .dropdown-item.text-danger:hover { background: #ffeaea; color: #c0392b !important; }
        .dropdown-menu .dropdown-item { padding: 10px 18px; }
        .dropdown-menu { border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .dropdown .d-flex.bg-white { background: #fff !important; border: 1.5px solid #009cf3 !important; color: #009cf3 !important; }
        .dropdown .d-flex.bg-white i { color: #fff; background: #009cf3; border-radius: 50%; padding: 4px; font-size: 18px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; }
        .dropdown .d-flex.bg-white:focus { outline: none; box-shadow: 0 0 0 2px #009cf333; }
    </style>
@endpush

@push('js')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- BS Custom File Input -->
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                responsive: true,
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
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
                        data: 'role',
                        name: 'role'
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
