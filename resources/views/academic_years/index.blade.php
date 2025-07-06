@extends('layouts.app')

@section('title')
    Manajemen Tahun Akademik
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .dropdown-menu { min-width: 100px; z-index: 1050 !important; }
        .dropdown-menu a, .dropdown-menu button.dropdown-item { font-size: 0.95rem; padding: 10px 18px; }
        .dropdown-menu .dropdown-item.text-danger { color: #e74c3c !important; font-weight: 500; }
        .dropdown-menu .dropdown-item.text-danger:hover { background: #ffeaea; color: #c0392b !important; }
        .dropdown-menu { border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .dropdown-toggle.btn-sm { min-width: 100px; }
    </style>
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Manajemen Tahun Akademik</h4>
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
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Kelola Data Tahun Akademik</h3>
                            <div class="card-tools">
                                <a href="{{ route('manage-academic-years.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus-circle"></i> Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable-main" class="table table-bordered table-striped">
                                    <thead class="bg-tertiary text-white">
                                        <tr>
                                            <th>No</th>
                                            <th>Tahun Mulai</th>
                                            <th>Tahun Selesai</th>
                                            <th>Semester</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($academicYears as $academicYear)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $academicYear->start_year }}</td>
                                                <td>{{ $academicYear->end_year }}</td>
                                                <td>{{ $academicYear->semester == 0 ? 'Genap' : 'Ganjil' }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm dropdown-toggle {{ $academicYear->is_active ? 'btn-success' : 'btn-secondary' }}"
                                                                type="button"
                                                                id="dropdownMenuStatus{{ $academicYear->id }}"
                                                                data-toggle="dropdown"
                                                                aria-haspopup="true"
                                                                aria-expanded="false">
                                                            {{ $academicYear->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuStatus{{ $academicYear->id }}">
                                                            <a class="dropdown-item status-option {{ $academicYear->is_active ? 'disabled' : '' }}"
                                                               href="#"
                                                               data-id="{{ $academicYear->id }}"
                                                               data-status="1">Aktif</a>
                                                            <a class="dropdown-item status-option {{ !$academicYear->is_active ? 'disabled' : '' }}"
                                                               href="#"
                                                               data-id="{{ $academicYear->id }}"
                                                               data-status="0">Tidak Aktif</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-info dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item"
                                                                href="{{ route('manage-academic-years.edit', $academicYear->id) }}">
                                                                <i class="fas fa-edit mr-2"></i>Edit
                                                            </a>
                                                            <form id="delete-form-{{ $academicYear->id }}"
                                                                action="{{ route('manage-academic-years.destroy', $academicYear->id) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                            <button type="button" class="dropdown-item text-danger"
                                                                onclick="confirmDelete('{{ $academicYear->id }}', '{{ $academicYear->start_year }}/{{ $academicYear->end_year }} - {{ $academicYear->semester == 0 ? 'Genap' : 'Ganjil' }}')">
                                                                <i class="fas fa-trash mr-2"></i>Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('turbolinks:load', function () {

            if ($.fn.DataTable.isDataTable('#datatable-main')) {
                $('#datatable-main').DataTable().destroy();
            }

            $('#datatable-main').DataTable({
                responsive: true,
                autoWidth: false,
                language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" },
                columnDefs: [{ "orderable": false, "targets": 5 }]
            });

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error('{{ $error }}');
                @endforeach
            @endif

            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });

        function confirmDelete(academicYearId, academicYearName) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                html: "Anda akan menghapus tahun akademik: <br><strong>" + academicYearName +
                    "</strong><br>Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + academicYearId).submit();
                }
            });
        }

        $(document).on('click', '.status-option', function (e) {
            e.preventDefault();

            const academicYearId = $(this).data('id');
            const newStatus = $(this).data('status');

            $.ajax({
                url: `/manage-academic-years/${academicYearId}`,
                type: 'POST',
                data: {
                    _method: 'PUT',
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => { location.reload(); }, 800);
                    } else {
                        toastr.error("Gagal memperbarui status.");
                    }
                },
                error: function () {
                    toastr.error("Terjadi kesalahan saat memperbarui status.");
                }
            });
        });
    </script>
@endpush
