@extends('layouts.app')

@section('title')
    Manajemen Kurikulum
@endsection

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Manajemen Kurikulum</h4>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Kelola Data Kurikulum</h3>
                            <div class="card-tools">
                                <a href="{{ route('manage-curriculums.create') }}" class="btn btn-primary">
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
                                            <th>Nama Kurikulum</th>
                                            {{-- <th>Status</th> --}}
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($curriculums as $curriculum)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $curriculum->curriculum_name }}</td>
                                                {{-- <td>{{ $curriculum->is_active ? 'Aktif' : 'Tidak Aktif' }}</td> --}}
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
                                                                href="{{ route('manage-curriculums.edit', $curriculum->id) }}">Edit</a>
                                                            <form id="delete-form-{{ $curriculum->id }}"
                                                                action="{{ route('manage-curriculums.destroy', $curriculum->id) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>

                                                            <button type="button" class="dropdown-item text-danger"
                                                                onclick="confirmDelete({{ $curriculum->id }})">
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> {{-- table-responsive --}}
                        </div> {{-- card-body --}}
                    </div> {{-- card --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- DataTables Scripts -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
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
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
