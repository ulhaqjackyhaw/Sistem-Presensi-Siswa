@extends('layouts.app')

@section('title', 'Detail Rekap Kehadiran')
@push('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-papm1p5QwDFi1Cdm42Hcps225y7sY9qsJh0kGxHgd6NqBJ38qJNmPR9U1FVLtZL1NVr7DiIP9N6byN1Nsx3X2w=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .rekap-table th,
    .rekap-table td {
        vertical-align: middle;
        text-align: center;
    }

    .rekap-table th {
        background: #0e3b7c;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="container-fluid py-4">
                <h4 class="fw-bold mb-3">DETAIL RIWAYAT PRESENSI</h4>
                <div class="card p-3 mb-4" style="border: #0e3b7c 1px solid;">
                    <form class="row gx-2 gy-2 align-items-center" method="GET" action="">
                        <input type="hidden" name="kelas" value="{{ $selectedClass }}">
                        <input type="hidden" name="bulan" value="{{ $selectedMonth }}">
                        <input type="hidden" name="tahun" value="{{ $selectedYear }}">
                        <div class="col-auto d-flex align-items-center">
                            <i class="fa-solid fa-calendar-days me-2"></i>
                            <label for="tanggal" class="form-label mb-1 me-2">Tanggal: </label>
                            <select class="form-select select2" id="tanggal" name="tanggal"
                                onchange="this.form.submit()">
                                @foreach ($dates as $date)
                                <option value="{{ $date }}" {{ $selectedDate == $date ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="card bg-primary p-3 d-flex align-items-start justify-content-start">
                    <div class="text-white fw-bold">
                        Rekap Kehadiran {{ optional($classes->find($selectedClass))->name }} -
                        {{ optional($classes->find($selectedClass))->parallel_name }}
                        ({{ strtoupper($months[$selectedMonth] ?? '') }}-{{ $selectedYear }})
                    </div>
                </div>
                <div class="table-responsive mt-3">
                    <table id="rekapTable" class="table table-bordered rekap-table mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NISN</th>
                                <th>Nama</th>
                                <th>Waktu</th>
                                <th>Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $i => $student)
                            @php
                            $att = $attendances[$student->nisn] ?? null;
                            $status = $att->status ?? '-';
                            $waktu = $att ? ($att->time_in ? date('H:i:s', strtotime($att->time_in)) : '-') : '-';
                            $badgeClass = $status === 'Hadir' ? 'text-success' : ($status === 'Absen' ? 'text-danger' :
                            ($status ===
                            'Sakit' ? 'text-info' : ($status === 'Izin' ? 'text-warning' : ($status === 'Terlambat' ?
                            'text-secondary' : 'text-muted'))));
                            @endphp
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $student->nisn }}</td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $waktu }}</td>
                                <td class="fw-semibold {{ $badgeClass }}">{{ $status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#rekapTable').DataTable({
            paging: true,
            searching: true,
            ordering: false,
            info: true,
            lengthChange: false,
            columnDefs: [
                { orderable: false, targets: 0 }
            ]
        });
        $('#tanggal').select2({
            placeholder: "Pilih Tanggal",
            allowClear: false,
            width: 'resolve'
        });
    });
</script>
@endpush
