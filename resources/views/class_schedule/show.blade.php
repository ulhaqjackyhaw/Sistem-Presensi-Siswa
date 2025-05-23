<!-- @extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3" style="margin-top: 30px;">Lihat Jadwal Kelas {{ $class->name }} - {{ $class->parallel_name }}</h4>

    @php
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    @endphp

    @foreach($days as $day)
        @php
            $dailySchedules = $schedules->where('day_of_week', $day)->sortBy('hour.slot_number');
        @endphp

        <div class="card mb-3 border-light border">
            <div class="card-header bg-light fw-semibold py-2 px-3">{{ $day }}</div>
            <div class="card-body p-3">
                @if($dailySchedules->isEmpty())
                    <p class="text-muted">Tidak ada jadwal</p>
                @else
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr class="table-light">
                                <th style="width: 15%">Jam</th>
                                <th style="width: 20%">Waktu</th>
                                <th>Mapel</th>
                                <th>Guru</th>
                                <th style="width: 15%">Tipe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailySchedules as $schedule)
                                <tr>
                                    <td>Jam ke-{{ $schedule->hour->slot_number }}</td>
                                    <td>{{ $schedule->hour->start_time }} - {{ $schedule->hour->end_time }}</td>
                                    @if($schedule->hour->session_type === 'Jam Istirahat')
                                        <td colspan="2" class="text-center text-muted">-- Jam Istirahat --</td>
                                        <td>Jam Istirahat</td>
                                    @else
                                        <td>{{ $schedule->assignment->subject->name }}</td>
                                        <td>{{ $schedule->assignment->teacher->name }}</td>
                                        <td>Jam Pelajaran</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection -->
