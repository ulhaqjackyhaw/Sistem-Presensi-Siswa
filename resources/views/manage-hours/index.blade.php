@extends('layouts.app')

@section('title', 'Manajemen Jam')

@section('content')
    <div class="container-fluid px-4">
        <h3 class="fw-bold mb-4">Manajemen Jam</h3>

        <div class="card shadow-sm rounded">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0 flex-grow-1">Kelola Jam Pelajaran</h5>
                @can('create_hours')
                    <a href="{{ route('manage-hours.create') }}" class="btn text-white ms-auto"
                        style="background-color: #1777e5;">Tambah Jam</a>
                @endcan
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="hourTable" class="table table-bordered table-striped align-middle">
                        <thead class="text-center text-white" style="background-color: #009cf3;">
                            <tr>
                                <th>No.</th>
                                <th>Tipe Jam</th>
                                <th>Jam ke-</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
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
            min-width: 100px;
            z-index: 1050 !important;
        }

        .dropdown-menu a {
            font-size: 0.85rem;
        }

        .dataTables_length label {
            font-weight: 500;
            color: #1777e5;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .dataTables_length select {
            min-width: 50px;
            margin: 0 0.3rem;
            padding: 0.3rem 0.5rem;
            font-size: 0.85rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
            color: #1777e5;
            appearance: none;
        }

        .dataTables_length select:focus {
            outline: none;
            border-color: #1777e5;
            box-shadow: 0 0 0 0.1rem rgba(23, 119, 229, 0.25);
        }

        .dropdown-menu a:hover {
            background-color: #f5faff;
            color: #0d6efd;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        #hourTable tbody tr:nth-child(odd) {
            background-color: #f4f4f4;
        }

        #hourTable tbody tr:nth-child(even) {
            background-color: #ffffff;
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

            $('#hourTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: 'simple_numbers',
                ajax: '{{ route('manage-hours.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'session_type',
                        name: 'session_type'
                    },
                    {
                        data: 'slot_number',
                        name: 'slot_number'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });

        function deleteHour(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data jam akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/manage-hours/${id}`,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire('Terhapus!', response.message ?? 'Jam berhasil dihapus.',
                                'success');
                            $('#hourTable').DataTable().ajax.reload();
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