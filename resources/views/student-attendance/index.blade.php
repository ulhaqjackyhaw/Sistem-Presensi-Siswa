@extends('layouts.app')

@section('title', 'Riwayat Presensi')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Riwayat Presensi</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    {{-- Breadcrumb jika perlu --}}
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
                        <h3 class="card-title">Data Presensi - 
                            @if(auth()->user()->student && auth()->user()->student->name)
                                {{ auth()->user()->student->name }}
                            @elseif(auth()->user()->name)
                                {{ auth()->user()->name }}
                            @else
                                Siswa
                            @endif
                        </h3>
                        <div class="card-tools">
                            <form method="GET" action="{{ route('student.attendance') }}" class="form-inline">
                                <div class="input-group input-group-sm">
                                    <input type="date" name="tanggal" class="form-control" 
                                           value="{{ request('tanggal') }}" placeholder="Filter Tanggal">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Tampilkan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="studentAttendanceTable" class="table table-bordered table-striped">
                                <thead class="bg-tertiary text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th>Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($attendances as $attendance)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $attendance->classSchedule->assignment->subject->name ?? '-' }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($attendance->meeting_date)->format('d/m/Y') }}
                                            </td>
                                            <td class="text-center">{{ $attendance->time_in ?? '-' }}</td>
                                            <td class="text-center">{{ $attendance->status ?? '-' }}</td>
                                            <td class="text-center">
                                                @switch($attendance->status)
                                                    @case('hadir')
                                                        <span class="badge badge-success">Hadir</span>
                                                    @break

                                                    @case('sakit')
                                                        <span class="badge badge-warning">Sakit</span>
                                                    @break

                                                    @case('izin')
                                                        <span class="badge badge-info">Izin</span>
                                                    @break

                                                    @case('alpha')
                                                        <span class="badge badge-danger">Tidak Hadir</span>
                                                    @break

                                                    @default
                                                        <span class="badge badge-secondary">{{ ucfirst($attendance->status) }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-gray-500">
                                                Belum ada data presensi
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(isset($attendances) && method_exists($attendances, 'links'))
                        <div class="mt-3">
                            {{ $attendances->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush

@push('js')
    <!-- DataTables JS -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#studentAttendanceTable').DataTable({
                responsive: true,
                autoWidth: false,
                ordering: false,
                language: {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(difilter dari total _MAX_ data)",
                    "search": "Cari:",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    },
                }
            });
        });
    </script>
@endpush

@endsection