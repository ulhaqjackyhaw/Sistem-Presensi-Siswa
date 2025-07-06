@extends('layouts.app')

@section('title', 'Presensi Siswa')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h4 class="m-0 text-uppercase">Presensi Siswa</h4>
                    <p class="text-muted mt-1" style="font-size: 0.9rem; text-transform: none;">
                        Guru dapat melihat jadwal presensi untuk kelas dan mata pelajaran yang diajar pada hari ini.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="container-fluid">
                    <div class="row">
                        @forelse($classSchedules as $schedule)
                            <div class="col-12 mb-2">
                                <div class="card border"
                                    style="background: #f0f7ff; border-radius: 10px; border: 1px solid #c8d6e5;">
                                    <div class="row no-gutters align-items-center">

                                        <div class="col-md-3 py-3 px-3 d-flex align-items-center justify-content-center"
                                            style="border-right: 1px solid #007bff;">
                                            <i class="far fa-calendar-alt fa-3x mr-3 text-primary"></i>
                                            <div>
                                                <div class="font-weight-bold text-primary" style="font-size: 1rem;">
                                                    @switch($schedule->day_of_week)
                                                        @case(1)
                                                            Senin
                                                        @break

                                                        @case(2)
                                                            Selasa
                                                        @break

                                                        @case(3)
                                                            Rabu
                                                        @break

                                                        @case(4)
                                                            Kamis
                                                        @break

                                                        @case(5)
                                                            Jumat
                                                        @break

                                                        @case(6)
                                                            Sabtu
                                                        @break

                                                        @case(7)
                                                            Minggu
                                                        @break

                                                        @default
                                                            Tidak diketahui
                                                    @endswitch
                                                </div>
                                                <div class="font-weight-bold text-primary" style="font-size: 1rem;">
                                                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 py-3 px-3">
                                            <div class="mb-1" style="color: #0a3978; font-size: 1rem;">
                                                <strong>Kelas:</strong> {{ $schedule->class_label }}
                                            </div>
                                            <div class="mb-1" style="color: #0a3978; font-size: 1rem;">
                                                <strong>Mata Pelajaran:</strong> {{ $schedule->subject_name }}
                                            </div>
                                            <div style="color: #0a3978; font-size: 1rem;">
                                                <strong>Jam Pelajaran:</strong>
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H.i') }}
                                                &ndash;
                                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('H.i') }}
                                            </div>
                                        </div>

                                        <div class="col-md-3 text-center py-3 px-3">
                                            <form action="{{ route('manage-attendances.create') }}" method="GET">
                                                <input type="hidden" name="class_id" value="{{ $schedule->class_id }}">
                                                <input type="hidden" name="day_of_week"
                                                    value="{{ $schedule->day_of_week }}">

                                                <button type="submit" class="btn btn-primary" @disabled(now()->lt(\Carbon\Carbon::today()->setTimeFromTimeString($schedule->start_time)->subMinutes(30)) ||
                                                        now()->gt(\Carbon\Carbon::today()->setTimeFromTimeString($schedule->end_time)))>
                                                    <i class="fas fa-clipboard-check mr-2"></i> Mulai Presensi
                                                </button>

                                            </form>
                                            <a href="{{ route('manage-attendances.show-by-class', $schedule->class_id) }}">
                                                <button class="btn btn-secondary mt-2">
                                                    <i class="fas fa-eye mr-2"></i> Lihat Presensi
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        Tidak ada sesi presensi hari ini.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
