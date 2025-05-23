@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Manajemen Jadwal</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right"></ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title m-0">Form Buat Jadwal</h5>
                    <div class="card-tools">
                        <a href="{{ route('manage-schedules.index') }}" class="btn btn-tool" title="Kembali">
                            <i class="fas fa-arrow-alt-circle-left"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body px-4 py-3">
                    <form method="POST" action="{{ route('manage-schedules.store') }}">
                        @csrf

                                            <div class="row mb-3">
                        <div class="col-auto me-3">
                            <label for="semester" class="form-label">Kurikulum</label>
                            <select name="semester" id="semester" class="form-select form-select-sm" required style="min-width: 150px;">
                                <option value="">Pilih</option>
                                <option value="1" {{ old('semester') == 1 ? 'selected' : '' }}>2013</option>
                                <option value="2" {{ old('semester') == 2 ? 'selected' : '' }}>Merdeka</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <label for="class_id" class="form-label">Kelas</label>
                            <select name="class_id" id="class_id" class="form-select form-select-sm" required style="min-width: 200px;">
                                <option value="">Pilih</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} - {{ $class->parallel_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                @php $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; @endphp

                @foreach($days as $day)
                    <div class="card mb-3 border-light border">
                        <div class="card-header fw-semibold py-2 px-3 text-white" style="background-color: #1D3F72">{{ $day }}</div>
                        <div class="card-body p-3" id="schedule-{{ $day }}"></div>
                        <div class="card-footer bg-white text-end py-2 px-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addScheduleRow('{{ $day }}')">+ Tambah</button>
                        </div>
                    </div>
                @endforeach

                <button type="submit" class="btn btn-block btn-flat text-white" style="background-color: #1D3F72">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
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
</script>
@endsection