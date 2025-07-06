@extends('layouts.app')

@section('title', 'Detail Presensi Kelas')

@push('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<style>
    th:first-child,
    td:first-child {
        width: 40px;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h4 class="text-uppercase">Detail Presensi Kelas</h4>
        <div class="card border-primary mb-3" style="background: #f0f8ff;">
            <div class="card-header bg-primary text-white">
                <span>Informasi Presensi Kelas</span>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    Kelas:
                    <strong>{{ $classSchedule->schoolClass->name ?? '-' }}</strong>
                </p>
                <p class="mb-2">
                    Jadwal:
                    <strong>{{ $classSchedule->assignment->teacher->name ?? '-' }}</strong>
                </p>
                <p class="mb-2">
                    Mata Pelajaran:
                    <strong>{{ $classSchedule->assignment->subject->subject_name ?? '-' }}</strong>
                </p>
                <p class="mb-0">
                    Tanggal:
                    <strong>
                        {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
                    </strong>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <h5 class="text-uppercase">Presensi Siswa</h5>
        <form id="attendance-form" method="POST" action="{{ route('manage-attendances.update-status') }}">
            @csrf
            <div style="overflow: hidden;">
                <table id="attendance-table" class="table table-bordered table-striped table-hover mb-0"
                    style="border-radius: 0.25rem;">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Status Presensi</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <button type="submit" class="btn btn-success mt-3">Kirim Perubahan</button>
        </form>
    </div>
</div>
@endsection
@push('js')
<script>
    $(document).ready(function() {
            const ajaxUrl = "{{ route('manage-attendances.show-by-class', $classSchedule->schoolClass->id) }}";
            $('#attendance-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: ajaxUrl,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                autoWidth: true,
                pageLength: 25,
                language: {
                    processing: "Memuat data...",
                    emptyTable: "Tidak ada data siswa",
                    zeroRecords: "Tidak ada data yang sesuai"
                }
            });
        });
</script>
<script>
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
</script>
@endpush
