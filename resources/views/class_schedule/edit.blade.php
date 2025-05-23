@extends('layouts.app')

@section('content')
<!--  -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Edit Jadwal Kelas</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right"></ol>
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
                            <h5 class="card-title m-0">Form Edit Jadwal</h5>
                            <div class="card-tools">
                                <a href="{{ route('manage-schedules.index') }}" class="btn btn-tool">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>

                        <div class="card-body px-4 py-3">
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <form method="POST" action="{{ route('manage-schedules.update', $manageSchedule->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="row mb-3">
    <div class="row mb-3">
    <div class="col-auto me-3">
        <label for="semester" class="form-label">Kurikulum</label>
        <select name="semester" id="semester" class="form-select form-select-sm" required style="min-width: 150px;">
            <option value="">Pilih</option>
            <option value="1" {{ old('semester', $semester) == '1' ? 'selected' : '' }}>2013</option>
            <option value="2" {{ old('semester', $semester) == '2' ? 'selected' : '' }}>Merdeka</option>
        </select>
    </div>
    <div class="col-auto">
        <label for="class_id" class="form-label">Kelas</label>
        <select name="class_id" id="class_id" class="form-select form-select-sm" required style="min-width: 200px;">
            <option value="">Pilih</option>
            @foreach($classes as $classItem)
                <option value="{{ $classItem->id }}" 
                    {{ old('class_id', $class->id) == $classItem->id ? 'selected' : '' }}>
                    {{ $classItem->name }} - {{ $classItem->parallel_name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

                @php $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; @endphp

<div class="container-fluid px-4"> {{-- Full width --}}
    @foreach($days as $day)
        <div class="card mb-3 border-light border">
            <div class="card-header fw-semibold py-2 px-3 text-white" style="background-color: #1D3F72;">{{ $day }}</div>
            <div class="card-body p-3 schedule-body" id="schedule-{{ $day }}">
                {{-- Load existing schedules --}}
                @if(isset($scheduleData[$day]))
                    @foreach($scheduleData[$day] as $index => $schedule)
                        <div class="row g-2 align-items-end mb-3">
                            <div class="col-md-2">
                                <label class="form-label">Tipe Sesi</label>
                                <select 
                                    name="schedules[{{ $day }}][{{ $index }}][session_type]" 
                                    class="form-select session-type" 
                                    onchange="filterHours(this)" 
                                    required
                                >
                                    <option value="">-- Pilih --</option>
                                    <option value="Jam Pelajaran" {{ $schedule['session_type'] == 'Jam Pelajaran' ? 'selected' : '' }}>Jam Pelajaran</option>
                                    <option value="Jam Istirahat" {{ $schedule['session_type'] == 'Jam Istirahat' ? 'selected' : '' }}>Jam Istirahat</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Jam Mulai</label>
                                <select 
                                    name="schedules[{{ $day }}][{{ $index }}][start_hour_id]" 
                                    class="form-select hour-select-start" 
                                    onchange="toggleSubjectTeacher(this)" 
                                    required
                                >
                                    <option value="">Jam ke-</option>
                                    @foreach($hours as $hour)
                                        <option value="{{ $hour->id }}" 
                                            data-type="{{ $hour->session_type }}" 
                                            data-start="{{ $hour->start_time }}" 
                                            data-end="{{ $hour->end_time }}"
                                            {{ $schedule['start_hour_id'] == $hour->id ? 'selected' : '' }}
                                            style="{{ $hour->session_type != $schedule['session_type'] ? 'display:none;' : '' }}">
                                            Jam ke-{{ $hour->slot_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Jam Selesai</label>
                                <select 
                                    name="schedules[{{ $day }}][{{ $index }}][end_hour_id]" 
                                    class="form-select hour-select-end" 
                                    required
                                >
                                    <option value="">Jam ke-</option>
                                    @foreach($hours as $hour)
                                        <option value="{{ $hour->id }}"
                                            data-type="{{ $hour->session_type }}"
                                            {{ $schedule['end_hour_id'] == $hour->id ? 'selected' : '' }}
                                            style="{{ $hour->session_type != $schedule['session_type'] ? 'display:none;' : '' }}">
                                            Jam ke-{{ $hour->slot_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 assignment-container" 
                                 style="{{ $schedule['session_type'] == 'Jam Istirahat' ? 'display: none;' : '' }}">
                                <label class="form-label">Guru & Mata Pelajaran</label>
                                <select 
                                    name="schedules[{{ $day }}][{{ $index }}][assignment_id]" 
                                    class="form-select assignment-select"
                                    {{ $schedule['session_type'] == 'Jam Pelajaran' ? 'required' : '' }}
                                    {{ $schedule['session_type'] == 'Jam Istirahat' ? 'disabled' : '' }}
                                >
                                    <option value="">Pilih</option>
                                    @foreach($teachingAssignments as $assignment)
                                        <option value="{{ $assignment['id'] }}"
                                            {{ $schedule['assignment_id'] == $assignment['id'] ? 'selected' : '' }}>
                                            {{ $assignment['subject_name'] }} - {{ $assignment['teacher_name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-1 text-center">
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-outline-danger mt-4" 
                                    onclick="this.closest('.row').remove()"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="card-footer bg-white text-end py-2 px-3">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addScheduleRow('{{ $day }}')">+ Tambah</button>
            </div>
        </div>
    @endforeach

    <button type="submit" class="btn btn-block btn-flat text-white" style="background-color: #1D3F72">
        <i class="fa fa-save"></i> Update Jadwal
    </button>
</div>



{{-- SCRIPT --}}
<script>
    const subjects = @json($subjects);
    const hours = @json($hours);
    const teachingAssignments = @json($teachingAssignments);

    function addScheduleRow(day) {
        const container = document.getElementById('schedule-' + day);
        const index = container.children.length;

        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end mb-3';

        row.innerHTML = `
            <div class="col-md-2">
                <label class="form-label">Tipe Sesi</label>
                <select 
                    name="schedules[${day}][${index}][session_type]" 
                    class="form-select session-type" 
                    onchange="filterHours(this)" 
                    required
                >
                    <option value="">-- Pilih --</option>
                    <option value="Jam Pelajaran">Jam Pelajaran</option>
                    <option value="Jam Istirahat">Jam Istirahat</option>
                </select>
            </div>

                    <div class="col-md-2">
            <label class="form-label d-block">Jam Mulai</label>
            <select 
                name="schedules[${day}][${index}][start_hour_id]" 
                class="form-select hour-select-start" 
                onchange="toggleSubjectTeacher(this)" 
                required
            >
                <option value="">Jam ke-</option>
            </select>
            </div>

            <div class="col-md-2">
            <label class="form-label d-block">Jam Selesai</label>
            <select 
                name="schedules[${day}][${index}][end_hour_id]" 
                class="form-select hour-select-end" 
                required
            >
                <option value="">Jam ke-</option>
            </select>
            </div>

            <div class="col-md-4 assignment-container">
                <label class="form-label">Mata Pelajaran & Guru</label>
                <select 
                    name="schedules[${day}][${index}][assignment_id]" 
                    class="form-select assignment-select"
                >
                    <option value="">Pilih</option>
                    ${teachingAssignments.map(a => `
                        <option value="${a.id}">
                            ${a.subject_name} - ${a.teacher_name}
                        </option>
                    `).join('')}
                </select>
            </div>

            <div class="col-md-1 text-center">
                <button 
                    type="button" 
                    class="btn btn-sm btn-outline-danger mt-4" 
                    onclick="this.closest('.row').remove()"
                >
                    ✖
                </button>
            </div>
        `;

        container.appendChild(row);
    }

    function populateHourSelect(select, sessionType) {
        const filtered = hours.filter(h => h.session_type === sessionType);
        const options = filtered.map(h =>
            `<option value="${h.id}" data-type="${h.session_type}" data-start="${h.start_time}" data-end="${h.end_time}">Jam ke-${h.slot_number}</option>`
        ).join('');
        select.innerHTML = `<option value="">Jam ke-</option>` + options;
    }

    function filterHours(select) {
        const sessionType = select.value;
        const row = select.closest('.row');

        const startSelect = row.querySelector('.hour-select-start');
        const endSelect = row.querySelector('.hour-select-end');

        populateHourSelect(startSelect, sessionType);
        populateHourSelect(endSelect, sessionType);

        toggleSubjectTeacher(startSelect); // panggil ulang agar sesuai kondisi baru
    }

    function toggleSubjectTeacher(select) {
        const row = select.closest('.row');
        const startSelect = row.querySelector('.hour-select-start');
        const assignmentDiv = row.querySelector('.assignment-container');
        const assignmentSelect = row.querySelector('.assignment-select');

        const hourType = startSelect.selectedOptions[0]?.dataset.type;
        const isBreak = (hourType === 'Jam Istirahat');

        if (isBreak) {
            assignmentDiv.style.display = 'none';
            assignmentSelect.disabled = true;
            assignmentSelect.removeAttribute('required');
            assignmentSelect.value = '';
        } else {
            assignmentDiv.style.display = '';
            assignmentSelect.disabled = false;
            assignmentSelect.setAttribute('required', 'required');
        }
    }

    // Initialize existing rows
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize session type filters for existing schedules
        document.querySelectorAll('.session-type').forEach(select => {
            const sessionType = select.value;
            if (sessionType) {
                const row = select.closest('.row');
                const startSelect = row.querySelector('.hour-select-start');
                const endSelect = row.querySelector('.hour-select-end');
                
                // Show only relevant hours
                startSelect.querySelectorAll('option').forEach(option => {
                    if (option.value === '') return; // keep empty option
                    const hourType = option.dataset.type;
                    option.style.display = (hourType === sessionType) ? '' : 'none';
                });
                
                endSelect.querySelectorAll('option').forEach(option => {
                    if (option.value === '') return; // keep empty option
                    const hourType = option.dataset.type || sessionType; // fallback to sessionType
                    option.style.display = (hourType === sessionType) ? '' : 'none';
                });
                
                // Initialize subject/teacher visibility
                toggleSubjectTeacher(startSelect);
            }
        });
    });
</script>
@endsection