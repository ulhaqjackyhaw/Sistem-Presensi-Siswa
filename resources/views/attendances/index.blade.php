@extends('layouts.app')

@section('title', 'Presensi Siswa')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Presensi Siswa</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{--  --}}
                    </ol>
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
                                        {{-- Kalender & Tanggal --}}
                                        <div class="col-md-3 py-3 px-3 d-flex align-items-center justify-content-center"
                                            style="border-right: 1px solid #e0e7ef;">
                                            <i class="far fa-calendar-alt fa-3x mr-3 text-primary"></i>
                                            <div>
                                                <div class="font-weight-bold text-primary" style="font-size: 1rem;">
                                                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd') }},
                                                </div>
                                                <div class="font-weight-bold text-primary" style="font-size: 1rem;">
                                                    {{ \Carbon\Carbon::now()->format('d F Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Info Kelas --}}
                                        <div class="col-md-6 py-3 px-3">
                                            <div class="mb-1" style="color: #0a3978; font-size: 1rem;">
                                                <strong>Kelas: </strong>
                                                {{ $schedule->schoolClass->name }}{{ $schedule->schoolClass->parallel_name ? '-' . $schedule->schoolClass->parallel_name : '' }}
                                            </div>
                                            <div class="mb-1" style="color: #0a3978; font-size: 1rem;">
                                                <strong>Mata Pelajaran: </strong>
                                                {{ $schedule->assignment->subject->name }}
                                            </div>
                                            <div style="color: #0a3978; font-size: 1rem;">
                                                <strong>Jam Pelajaran: </strong>
                                                {{ \Carbon\Carbon::parse($schedule->hour->start_time)->format('H.i') }}
                                                -
                                                {{ \Carbon\Carbon::parse($schedule->hour->end_time)->format('H.i') }}
                                            </div>
                                        </div>
                                        {{-- Tombol Presensi --}}
                                        <div class="col-md-3 text-center py-3 px-3">
                                            <form action="{{ route('manage-attendances.create') }}" method="GET">
                                                <input type="hidden" name="class_schedule_id" value="{{ $schedule->id }}">
                                                <input type="hidden" name="meeting_date"
                                                    value="{{ now()->toDateString() }}">
                                                <button type="submit" class="btn"
                                                    style="background: #0a3978; color: #fff; font-weight: bold; min-width: 120px; border-radius: 6px;"
                                                    @if (now()->lt(\Carbon\Carbon::parse($schedule->hour->start_time)) ||
                                                            now()->gt(\Carbon\Carbon::parse($schedule->hour->end_time))) disabled @endif>
                                                    <i class="fas fa-clipboard-check mr-2"></i> Presensi
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">Tidak ada sesi presensi hari ini.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
