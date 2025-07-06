@extends('layouts.app')

@section('title')
Jadwal Mengajar {{ $teacherName ?? 'Guru' }}
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<style>
    .table-schedule th, .table-schedule td {
        vertical-align: middle;
        text-align: center;
    }
    .filter-label {
        font-weight: 600;
        color: #1D3F72;
    }
    .card-header {
        background: #fff;
        border-bottom: none;
    }
    .table-schedule thead th {
        background: #0094e0;
        color: #fff;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Jadwal Mengajar {{ $teacherName ?? 'Guru' }}</h4>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-4">
                <form method="GET" id="filterForm">
                    <label class="filter-label mb-1">Tahun Akademik</label>
                    <select name="academic_year_id" class="form-control" id="academicYearSelect" onchange="document.getElementById('filterForm').submit()">
                        <option value="">-- Pilih Tahun Akademik --</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id', $activeAcademicYearId) == $year->id ? 'selected' : '' }}>
                                {{ $year->start_year }}/{{ $year->end_year }} {{ $year->semester == 1 ? '- Ganjil' : '- Genap' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <span class="fw-bold">Jadwal Mingguan</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-schedule">
                                <thead>
                                    <tr>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($weeklySchedules as $item)
                                        <tr>
                                            <td>{{ $item['day'] }}</td>
                                            <td>{{ $item['time'] }}</td>
                                            <td>{{ $item['subject'] }}</td>
                                            <td>{{ $item['class'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Tidak ada jadwal ditemukan.</td>
                                        </tr>
                                    @endforelse
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
