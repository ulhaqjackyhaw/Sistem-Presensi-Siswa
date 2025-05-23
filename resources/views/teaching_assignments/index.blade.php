@extends('layouts.app')

@section('title')
    Manajemen Pengajaran Guru - Mapel
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
                <h4 class="m-0">Manajemen Data Pengajaran Guru ke Mapel</h4>
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
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Kelola Data Pengajaran Guru ke Mapel</h3>
                        <div class="card-tools">
                            <a href="{{ route('manage-teacher-subject-assignments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i> Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="teachingTable" class="table table-bordered table-striped">
                                <thead class="bg-tertiary text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Tahun Akademik</th>
                                        <th>Kelas</th>
                                        <th>Mapel</th>
                                        <th>Nama Guru</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teachingAssignments as $index => $assignment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($assignment->academicYear)
                                                {{ $assignment->academicYear->start_year }}/{{ $assignment->academicYear->end_year }}
                                                {{ isset($assignment->academicYear->semester)
                                                    ? ($assignment->academicYear->semester == 1 ? 'Ganjil' : 'Genap')
                                                    : '' }}
                                            @else
                                                <span class="text-danger">Belum diatur</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($assignment->class)->name }} - {{ optional($assignment->class)->parallel_name }}</td>
                                        <td>{{ $assignment->subject->name ?? '-' }}</td>
                                        <td>{{ $assignment->teacher->name ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-info dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item"
                                                       href="{{ route('manage-teacher-subject-assignments.edit', $assignment->id) }}">Edit</a>
                                                    <form action="{{ route('manage-teacher-subject-assignments.destroy', $assignment->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">Hapus</button>
                                                    </form>
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
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

<script>
    $(function () {
        $('#teachingTable').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            }
        });
    });
</script>
@endpush