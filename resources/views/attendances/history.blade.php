@extends('layouts.app')

@section('title', 'Riwayat Presensi')
@push('css')
<style>
    .filter-bar {
        background: #fff;
        border: 1px solid #0e3b7c;
        padding: 1rem;
        border-radius: .5rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .1);
    }

    .filter-bar .form-label {
        margin-bottom: 0;
        font-weight: 500;
    }

    .student-name {
        flex: 0 0 200px;
        color: #212529;
    }

    .progress-wrapper {
        position: relative;
        flex: 1;
        margin-left: 1rem;
    }

    .progress-wrapper .percent-text {
        position: absolute;
        top: 50%;
        right: 0.75rem;
        transform: translateY(-50%);
        font-size: .875rem;
        color: #000000;
    }

    .progress {
        height: 12px;
        background-color: #e9ecef;
    }

    .progress-bar {
        background-color: #0d6efd;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="content">
    <div class="container-fluid py-4">
        <h3 class="fw-bold">RIWAYAT PRESENSI</h3>
        <p class="text-muted">Guru dapat melihat riwayat presensi siswa berdasarkan kelas, bulan, dan tahun</p>

        <div class="filter-bar mb-4">
            <form method="GET" class="row gx-3 gy-2 justify-content-end" action="">
                <div class="col-auto d-flex align-items-center justify-content-center">
                    <label for="kelas" class="form-label me-2 mb-0 mr-1">Kelas:</label>
                    <select name="kelas" id="kelas" class="form-select select2" required style="min-width:160px;">
                        <option value="">Pilih Kelas</option>
                        @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ $selectedClass == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}{{ $class->parallel_name ? ' - ' . $class->parallel_name : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto d-flex align-items-center justify-content-center">
                    <label for="bulan" class="form-label me-2 mb-0 mr-1">Bulan:</label>
                    <select name="bulan" id="bulan" class="form-select select2" required style="min-width:140px;">
                        <option value="">Pilih Bulan</option>
                        @foreach ($months as $num => $nama)
                        <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun --}}
                <div class="col-auto d-flex align-items-center justify-content-center">
                    <label for="tahun" class="form-label me-2 mb-0 mr-1">Tahun: </label>
                    <select name="tahun" id="tahun" class="form-select select2" required style="min-width:120px;">
                        @foreach ($years as $yr)
                        <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                            {{ $yr }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto align-self-end">
                    <button type="submit" class="btn btn-primary px-4" style="height:38px;">Pilih</button>
                </div>
            </form>
        </div>

        @if ($showResult)
        <div class="card" style="border-radius: .5rem; overflow: hidden;">
            <div class="p-3 bg-primary text-white d-flex align-items-center justify-content-between">
                <div class="fw-semibold">
                    Presentase Kehadiran
                    {{ optional($classes->find($selectedClass))->name }} -
                    {{ optional($classes->find($selectedClass))->parallel_name }}
                    ({{ strtoupper($months[$selectedMonth] ?? '') }} {{ $selectedYear }})
                </div>
                <a
                    href="{{ route('attendances.history-detail', ['kelas' => $selectedClass, 'bulan' => $selectedMonth, 'tahun' => $selectedYear]) }}">
                    <button class="btn btn-primary btn-sm btn-detail">Lihat Detail</button>
                </a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($students as $student)
                @php $pct = $presentase[$student->nisn] ?? 0; @endphp
                <li class="list-group-item d-flex align-items-center">
                    <div class="student-name">{{ $student->name }}</div>
                    <div class="progress-wrapper">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $pct }}%;"
                                aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="percent-text">{{ $pct }}%</div>
                    </div>
                </li>
                @empty
                <li class="list-group-item text-center text-muted py-4">
                    Tidak ada data siswa untuk kelas & bulan ini.
                </li>
                @endforelse
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
            $('#kelas').select2({
                placeholder: "Pilih Kelas",
                allowClear: false,
                width: '100%'
            });
            $('#bulan').select2({
                placeholder: "Pilih bulan",
                allowClear: false,
                width: '100%'
            });
            $('#tahun').select2({
                placeholder: "Pilih Tahun",
                allowClear: false,
                width: '100%'
            });
        });
</script>
@endpush
