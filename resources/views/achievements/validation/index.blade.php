@extends('layouts.app')

@section('title')
    Validasi Laporan Prestasi
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-uppercase">Validasi Laporan Prestasi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Validasi Prestasi</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">
        <div class="row">        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Data Laporan</h3>
                    <div class="w-100 mt-3 d-flex align-items-center justify-content-end flex-wrap">
                        <form method="GET" class="form-inline" style="gap: 6px;">
                            <input type="date" name="tanggal" class="form-control form-control-sm mr-2" value="{{ request('tanggal') }}">
                            <select name="status" class="form-control form-control-sm mr-2">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Validasi</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        </form>
                    </div>
                </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table id="datatable-achievements" class="table table-bordered table-striped">
                                <thead class="bg-tertiary text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Siswa</th>
                                        <th>Nama Prestasi</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Dilaporkan Oleh</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($achievements as $achievement)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $achievement->student ? $achievement->student->name : '-' }}</td>
                                            <td>{{ $achievement->achievements_name ?: '-' }}</td>
                                            <td>{{ $achievement->achievement_date ? \Carbon\Carbon::parse($achievement->achievement_date)->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                @if($achievement->validation_status === 'pending')
                                                    <span class="badge badge-warning">Menunggu Validasi</span>
                                                @elseif($achievement->validation_status === 'approved')
                                                    <span class="badge badge-success">Disetujui</span>                                            @elseif($achievement->validation_status === 'rejected')
                                                <span class="badge badge-danger">Ditolak</span>
                                            @else
                                                <span class="badge badge-secondary">{{ Str::title($achievement->validation_status ?? 'N/A') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $achievement->teacher ? $achievement->teacher->name : '-' }}</td>
                                            <td>
                                                <a href="{{ route('achievement-validations.show', $achievement) }}" class="btn btn-info btn-sm">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-gray-500">
                                                Tidak ada prestasi yang perlu divalidasi
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($achievements->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $achievements->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script>
$(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    $('#datatable-achievements').DataTable({
        "ordering": true,
        "responsive": true,
        "autoWidth": false,
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "zeroRecords": "Tidak ada data ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Tidak ada data",
            "infoFiltered": "(disaring dari _MAX_ total data)"
        }
    });
});
</script>
@endpush
