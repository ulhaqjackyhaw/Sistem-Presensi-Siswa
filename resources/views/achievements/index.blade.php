@extends('layouts.app')

@section('title', 'Daftar Prestasi')

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
                <h1 class="m-0 text-uppercase">Daftar Prestasi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Daftar Prestasi</li>
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
                        <h3 class="card-title">Data Prestasi</h3>
                        <div class="card-tools">
                            <a href="{{ route('achievements.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i> Laporkan Prestasi
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable-achievements" class="table table-bordered table-striped">
                                <thead class="bg-tertiary text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Siswa</th>
                                        <th>Nama Prestasi</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Divalidasi Oleh</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($achievements as $achievement)
                                        <tr>
                                            <td>{{ ($achievements instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($achievements->currentPage() - 1) * $achievements->perPage() : 0) + $loop->iteration }}</td>
                                            <td>{{ $achievement->student ? $achievement->student->name : '-' }}</td>
                                            <td>{{ $achievement->achievements_name }}</td>
                                            <td>{{ $achievement->achievement_date ? $achievement->achievement_date->format('d/m/Y') : '-' }}</td>
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
                                            <td>{{ $achievement->validator ? $achievement->validator->name : '-' }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('achievements.show', $achievement) }}">
                                                            <i class="fas fa-eye text-info mr-1"></i> Detail
                                                        </a>
                                                        @if($achievement->validation_status === 'pending')
                                                            <a class="dropdown-item" href="{{ route('achievements.edit', $achievement) }}">
                                                                <i class="fas fa-edit text-warning mr-1"></i> Edit
                                                            </a>
                                                            <form action="{{ route('achievements.destroy', $achievement) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus prestasi ini?')">
                                                                    <i class="fas fa-trash-alt text-danger mr-1"></i> Hapus
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-gray-500">
                                                Belum ada prestasi yang dilaporkan
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
