<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Jadwal Kelas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 8px;
            color: #333;
            line-height: 1.2;
        }

        h2, h3, .academic-year {
            text-align: center;
            margin: 5px 0;
            color: #000;
            font-size: 13px;
        }

        .header-info {
            text-align: center;
            margin-bottom: 8px;
        }

        .day-title {
            margin-top: 12px;
            margin-bottom: 4px;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            background-color: #f8f9fa;
            padding: 4px;
            border: 1px solid #333;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .day-title:first-of-type {
            margin-top: 6px;
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 8px;
            border-radius: 3px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            background-color: white;
            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th, td {
            border: 1px solid #333;
            padding: 3px 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 8.5px;
            word-wrap: break-word;
            line-height: 1.2;
        }

        th {
            background-color: #e9ecef;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 4px;
        }

        .break-cell {
            background-color: #fff3cd;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            color: #856404;
        }

        .subject-cell {
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5px;
            font-weight: 600;
            color: #2c3e50;
        }

        .teacher-cell {
            text-align: center;
            font-size: 8.5px;
            color: #34495e;
        }

        .no-schedule {
            font-style: italic;
            color: #666;
            text-align: center;
            padding: 6px;
            background-color: #f8f9fa;
        }

        .hour-cell {
            background-color: #f1f3f4;
            font-weight: bold;
            color: #2c3e50;
        }

        .time-cell {
            background-color: #f8f9fa;
            font-size: 8.5px;
        }

        @media print {
            @page {
                size: 210mm 330mm; 
                margin: 15mm 18mm;
            }

            body {
                margin: 0;
                font-size: 7.5px;
                line-height: 1.15;
            }

            h2, h3, .academic-year {
                font-size: 11px;
                margin: 3px 0;
            }

            .day-title {
                page-break-before: avoid;
                page-break-after: avoid;
                margin-top: 8px;
                margin-bottom: 3px;
                font-size: 9px;
                padding: 3px;
            }

            .day-title:first-of-type {
                margin-top: 4px;
            }

            .table-container {
                page-break-inside: avoid;
                box-shadow: none;
                margin-bottom: 5px;
            }

            table {
                font-size: 7.5px;
            }

            th, td {
                padding: 2px 3px;
                font-size: 7.5px;
                border: 0.5px solid #333;
                line-height: 1.15;
            }

            th {
                padding: 3px;
            }

            .subject-cell, .teacher-cell {
                font-size: 7.5px;
            }

            .no-schedule {
                padding: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="header-info" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="margin: 0;">
            JADWAL PELAJARAN - KELAS
            {{ $class->name ?? 'N/A' }}{{ isset($class->parallel_name) ? ' ' . $class->parallel_name : '' }}
        </h2>

    </div>
    <div class="academic-year">
        TAHUN AKADEMIK {{ $schedule->schoolClass->academicYear->start_year }} / {{ $schedule->schoolClass->academicYear->end_year }}
    </div>

    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
        <div class="day-title">{{ $day }}</div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 12%;">Jam</th>
                        <th style="width: 20%;">Waktu</th>
                        <th style="width: 34%;">Mata Pelajaran</th>
                        <th style="width: 34%;">Guru Pengampu</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($schedulesPerDay[$day]) && count($schedulesPerDay[$day]) > 0)
                        @foreach($schedulesPerDay[$day] as $item)
                            @for($hour = $item['start_hour_slot']; $hour <= $item['end_hour_slot']; $hour++)
                                @if($item['session_type'] == 'Jam Istirahat' && $hour == $item['start_hour_slot'])
                                    <tr>
                                        <td class="break-cell">-</td>
                                        <td class="break-cell time-cell">
                                            @php
                                                $hourStartTime = $item['hour_times'][$hour] ?? $item['start_time'];
                                                $hourEndTime = $item['hour_end_times'][$item['end_hour_slot']] ?? $item['end_time'];
                                            @endphp
                                            {{ $hourStartTime && $hourEndTime ? substr($hourStartTime, 0, 5) . ' - ' . substr($hourEndTime, 0, 5) : ($hourStartTime ?? '-') }}
                                        </td>
                                        <td class="break-cell" colspan="2">ISTIRAHAT</td>
                                    </tr>
                                @elseif($item['session_type'] != 'Jam Istirahat')
                                    <tr>
                                        <td class="hour-cell">{{ $hour }}</td>
                                        <td class="time-cell">
                                            @php
                                                $hourStartTime = $item['hour_times'][$hour] ?? $item['start_time'];
                                                $hourEndTime = $item['hour_end_times'][$hour] ?? $item['end_time'];
                                            @endphp
                                            {{ $hourStartTime && $hourEndTime ? substr($hourStartTime, 0, 5) . ' - ' . substr($hourEndTime, 0, 5) : ($hourStartTime ?? '-') }}
                                        </td>
                                        <td class="subject-cell">{{ $item['subject_name'] ?? 'N/A' }}</td>
                                        <td class="teacher-cell">{{ $item['teacher_name'] ?? 'N/A' }}</td>
                                    </tr>
                                @endif
                            @endfor
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="no-schedule">Tidak ada jadwal</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>