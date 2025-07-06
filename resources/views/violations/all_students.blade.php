@extends('layouts.app')

@section('title', 'Daftar Pelanggaran Siswa')

@push('css')
<style>
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    .table th {
        font-weight: 600;
    }
    .badge-point {
        font-size: 0.9em;
        padding: 5px 10px;
        border-radius: 15px;
        background-color: #dc3545;
        color: white;
    }
    .search-section {
        background: #fff;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    #violations-table thead tr th {
        vertical-align: middle;
    }
</style>
<!-- DataTables CSS -->
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daftar Pelanggaran Siswa</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Daftar Pelanggaran Siswa</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-danger">
            <div class="card-header position-relative">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Daftar Siswa dengan Pelanggaran
                </h3>
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary position-absolute" style="right: 15px; top: 10px;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="search-section mb-3">
                    <div class="row">
                        <div class="col-md-8">
                            <form action="{{ route('violations.all_students') }}" method="GET" class="form-inline">
                                <div class="form-group mr-2">
                                    <label for="search" class="mr-2">Cari:</label>
                                    <input type="text" class="form-control" id="search" name="search"
                                        placeholder="Nama siswa..." value="{{ request('search') }}">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="class" class="mr-2">Kelas:</label>
                                    <select class="form-control" id="class" name="class">
                                        <option value="">Semua Kelas</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class }}" {{ request('class') == $class ? 'selected' : '' }}>
                                                {{ $class }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('violations.all_students') }}" class="btn btn-secondary ml-2">Reset</a>
                            </form>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="form-inline justify-content-end">
                                <label class="mr-2">Total Siswa: <span class="font-weight-bold">{{ $students->total() }}</span></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="violations-table" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr class="text-center bg-tertiary text-white">
                                <th style="width: 50px">#</th>
                                <th>Nama Siswa</th>
                                <th>NISN</th>
                                <th>Kelas</th>
                                <th>Total Pelanggaran</th>
                                <th>Total Poin</th>
                                <th style="width: 120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $key => $student)
                            <tr>
                                <td class="text-center">{{ $students->firstItem() + $key }}</td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->nisn }}</td>
                                <td class="text-center">{{ $student->class_name }}</td>
                                <td class="text-center">{{ $student->violations_count }}</td>
                                <td class="text-center"><span class="badge badge-point">{{ $student->total_point }}</span></td>
                                <td class="text-center">
                                    <a href="{{ route('student.detail', $student->nisn) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data pelanggaran siswa.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $students->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
<!-- DataTables & Plugins -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

<script>
    $(function() {
        $('#violations-table').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "emptyTable": "Tidak ada data pelanggaran siswa"
            },
            "columnDefs": [
                { "orderable": false, "targets": [6] },
                { "className": "text-center", "targets": [0, 3, 4, 5, 6] }
            ]
        });

        // Tambahkan hover effect pada tombol
        $('.btn').hover(
            function() { $(this).addClass('shadow-sm') },
            function() { $(this).removeClass('shadow-sm') }
        );
    });
</script>
@endpush
