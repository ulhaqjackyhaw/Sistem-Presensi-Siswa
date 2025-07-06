@extends('layouts.app')
@section('title', 'Detail Jadwal Kelas')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0" style="font-family: 'Poppins', sans-serif;">Detail Jadwal Kelas</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right" style="font-family: 'Poppins', sans-serif;">
                        <li class="breadcrumb-item">
                            <a href="{{ route('manage-schedules.index') }}">Manajemen Jadwal</a>
                        </li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Informasi Kelas -->
            <div class="card card-primary card-outline mb-4" style="font-family: 'Poppins', sans-serif;">
                <div class="card-header">
                    <h5 class="card-title m-0">Informasi Jadwal</h5>
                    <div class="card-tools">
                        <a href="{{ route('manage-schedules.export-pdf', $schedule->id) }}" class="btn btn-sm btn-danger"
                            target="_blank">
                            <i class="fas fa-file-pdf"></i> Ekspor PDF
                        </a>
                        <a href="{{ route('manage-schedules.edit', $schedule->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('manage-schedules.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless" style="font-family: 'Poppins', sans-serif;">
                                <tr>
                                    <th style="width: 120px;">Kelas</th>
                                    <td>: {{ $class->name ?? 'N/A' }} - {{ $class->parallel_name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jadwal per Hari -->
            @foreach ($days as $day)
                <div class="card mb-4 shadow-sm" style="font-family: 'Poppins', sans-serif;">
                    <div class="card-header text-white d-flex justify-content-between align-items-center"
                        style="background-color: #1D3F72; padding: 12px 20px;">
                        <h6 class="mb-0 fw-bold">{{ $day }}</h6>
                    </div>
                    <div class="card-body p-0">
                        @if (empty($schedulesPerDay[$day]))
                            <div class="p-5 text-center text-muted" style="font-family: 'Poppins', sans-serif;">
                                <i class="fas fa-calendar-times fa-3x mb-3 text-secondary opacity-50"></i>
                                <p class="mb-0 fs-6">Tidak ada jadwal untuk hari {{ $day }}</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0 table-bordered"
                                    style="font-family: 'Poppins', sans-serif;">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th class="text-center align-middle"
                                                style="width: 8%; padding: 12px 8px; border: 1px solid #dee2e6;">No</th>
                                            <th class="text-center align-middle"
                                                style="width: 18%; padding: 12px 15px; border: 1px solid #dee2e6;">Tipe Sesi
                                            </th>
                                            <th class="text-center align-middle"
                                                style="width: 15%; padding: 12px 15px; border: 1px solid #dee2e6;">Jam</th>
                                            <th class="text-center align-middle"
                                                style="width: 18%; padding: 12px 15px; border: 1px solid #dee2e6;">Waktu
                                            </th>
                                            <th class="text-center align-middle"
                                                style="width: 41%; padding: 12px 20px; border: 1px solid #dee2e6;">Mata
                                                Pelajaran & Guru</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $rowNumber = 1; @endphp
                                        @foreach ($schedulesPerDay[$day] as $item)
                                            @if ($item['start_hour_slot'] == $item['end_hour_slot'])
                                                {{-- Single hour slot --}}
                                                <tr>
                                                    <td class="text-center align-middle fw-semibold"
                                                        style="padding: 15px 8px; border: 1px solid #dee2e6;">
                                                        {{ $rowNumber++ }}
                                                    </td>
                                                    <td class="text-center align-middle"
                                                        style="padding: 15px 15px; border: 1px solid #dee2e6;">
                                                        @if ($item['session_type'] == 'Jam Istirahat')
                                                            <span class="badge px-3 py-2"
                                                                style="font-weight: normal; background-color: #ffda3c; color: black;">
                                                                JAM ISTIRAHAT
                                                            </span>
                                                        @else
                                                            <span class="badge bg-primary px-3 py-2"
                                                                style="font-weight: normal;">
                                                                JAM PELAJARAN
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center align-middle text-black"
                                                        style="padding: 15px 15px; border: 1px solid #dee2e6; font-family: 'Poppins', sans-serif;">
                                                        @if ($item['session_type'] == 'Jam Istirahat')
                                                            <span class="text-muted">-</span>
                                                        @else
                                                            <span class="fw-bold text-black"
                                                                style="font-family: 'Poppins', sans-serif;">Jam
                                                                ke-{{ $item['start_hour_slot'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center align-middle text-muted"
                                                        style="padding: 15px 15px; white-space: nowrap; font-family: 'Poppins', sans-serif; border: 1px solid #dee2e6;">
                                                        @if ($item['start_time'] && $item['end_time'])
                                                            {{ substr($item['start_time'], 0, 5) }} -
                                                            {{ substr($item['end_time'], 0, 5) }}
                                                        @else
                                                            {{ $item['start_time'] ?? '-' }}
                                                        @endif
                                                    </td>
                                                    <td class="text-center align-middle"
                                                        style="padding: 15px 20px; border: 1px solid #dee2e6;">
                                                        @if ($item['session_type'] == 'Jam Istirahat')
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <span class="text-muted"> - </span>
                                                            </div>
                                                        @else
                                                            <div>
                                                                <div class="fw-bold text-dark mb-1"
                                                                    style="font-size: 15px; font-family: 'Poppins', sans-serif;">
                                                                    {{ $item['subject_name'] ?? 'N/A' }}
                                                                </div>
                                                                <div class="text-muted"
                                                                    style="font-size: 13px; font-family: 'Poppins', sans-serif;">
                                                                    {{ $item['teacher_name'] ?? 'N/A' }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @else
                                                {{-- Multiple hour slots --}}
                                                @for ($hour = $item['start_hour_slot']; $hour <= $item['end_hour_slot']; $hour++)
                                                    <tr>
                                                        <td class="text-center align-middle fw-semibold"
                                                            style="padding: 15px 8px; border: 1px solid #dee2e6;">
                                                            {{ $rowNumber++ }}
                                                        </td>
                                                        <td class="text-center align-middle"
                                                            style="padding: 15px 15px; border: 1px solid #dee2e6;">
                                                            @if ($item['session_type'] == 'Jam Istirahat')
                                                                <span class="badge px-3 py-2"
                                                                    style="font-weight: normal; background-color: #ffda3c; color: black;">
                                                                    JAM ISTIRAHAT
                                                                </span>
                                                            @else
                                                                <span class="badge bg-primary px-3 py-2"
                                                                    style="font-weight: normal;">
                                                                    JAM PELAJARAN
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center align-middle text-black"
                                                            style="padding: 15px 15px; border: 1px solid #dee2e6; font-family: 'Poppins', sans-serif;">
                                                            @if ($item['session_type'] == 'Jam Istirahat')
                                                                <span class="text-muted">-</span>
                                                            @else
                                                                <span class="fw-bold text-black"
                                                                    style="font-family: 'Poppins', sans-serif;">Jam
                                                                    ke-{{ $hour }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center align-middle text-muted"
                                                            style="padding: 15px 15px; white-space: nowrap; font-family: 'Poppins', sans-serif; border: 1px solid #dee2e6;">
                                                            @php
                                                                $hourStartTime = $item['hour_times'][$hour] ?? '-';
                                                                $hourEndTime = $item['hour_end_times'][$hour] ?? '-';
                                                            @endphp
                                                            @if ($hourStartTime !== '-' && $hourEndTime !== '-')
                                                                {{ substr($hourStartTime, 0, 5) }} -
                                                                {{ substr($hourEndTime, 0, 5) }}
                                                            @else
                                                                {{ $hourStartTime }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center align-middle"
                                                            style="padding: 15px 20px; border: 1px solid #dee2e6;">
                                                            @if ($item['session_type'] == 'Jam Istirahat')
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center">
                                                                    <span class="text-muted"> - </span>
                                                                </div>
                                                            @else
                                                                <div>
                                                                    <div class="text-dark mb-1"
                                                                        style="font-size: 15px; font-family: 'Poppins', sans-serif;">
                                                                        {{ $item['subject_name'] ?? 'N/A' }}
                                                                    </div>
                                                                    <div class="text-muted"
                                                                        style="font-size: 13px; font-family: 'Poppins', sans-serif;">
                                                                        <i class="fas fa-user me-1"></i>
                                                                        {{ $item['teacher_name'] ?? 'N/A' }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endfor
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
