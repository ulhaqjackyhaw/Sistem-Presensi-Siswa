@extends('layouts.app')

@section('content')
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
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <form method="POST" action="{{ route('manage-schedules.update', $manageSchedule->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-auto">
                                        <label for="class_id" class="form-label">Kelas</label>
                                        <select name="class_id" id="class_id" class="form-select form-select-sm" required
                                            style="min-width: 200px;">
                                            <option value="">Pilih</option>
                                            @foreach ($classes as $classItem)
                                                <option value="{{ $classItem->id }}"
                                                    {{ old('class_id', $class->id) == $classItem->id ? 'selected' : '' }}>
                                                    {{ $classItem->name }} - {{ $classItem->parallel_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @php $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; @endphp

                                <div class="container-fluid px-2">
                                    @foreach ($days as $day)
                                        <div class="card mb-3 border-light border">
                                            <div class="card-header fw-semibold py-2 px-3 text-white"
                                                style="background-color: #1D3F72;">{{ $day }}</div>
                                            <div class="card-body p-2 schedule-body" id="schedule-{{ $day }}">
                                                {{-- Load existing schedules --}}
                                                @if (isset($scheduleData[$day]))
                                                    @foreach ($scheduleData[$day] as $index => $schedule)
                                                        <div class="row g-2 align-items-end mb-2 schedule-row">
                                                            <div class="col-md-2">
                                                                <label class="form-label small mb-1">Tipe Sesi</label>
                                                                <select
                                                                    name="schedules[{{ $day }}][{{ $index }}][session_type]"
                                                                    class="form-select form-select-sm session-type"
                                                                    onchange="filterHours(this)" required>
                                                                    <option value="">-- Pilih --</option>
                                                                    <option value="Jam Pelajaran"
                                                                        {{ $schedule['session_type'] == 'Jam Pelajaran' ? 'selected' : '' }}>
                                                                        Jam Pelajaran</option>
                                                                    <option value="Jam Istirahat"
                                                                        {{ $schedule['session_type'] == 'Jam Istirahat' ? 'selected' : '' }}>
                                                                        Jam Istirahat</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <label class="form-label small mb-1">Jam Mulai</label>
                                                                <select
                                                                    name="schedules[{{ $day }}][{{ $index }}][start_hour_id]"
                                                                    class="form-select form-select-sm hour-select-start"
                                                                    onchange="updateEndHours(this); toggleSubjectTeacher(this)"
                                                                    data-day="{{ $day }}"
                                                                    data-selected="{{ $schedule['start_hour_id'] }}" required>
                                                                    <option value="">-- Pilih Jam Mulai --</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <label class="form-label small mb-1">Jam Selesai</label>
                                                                <select
                                                                    name="schedules[{{ $day }}][{{ $index }}][end_hour_id]"
                                                                    class="form-select form-select-sm hour-select-end"
                                                                    data-day="{{ $day }}"
                                                                    data-selected="{{ $schedule['end_hour_id'] }}" required>
                                                                    <option value="">-- Pilih Jam Selesai --</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-5 assignment-container"
                                                                style="{{ $schedule['session_type'] == 'Jam Istirahat' ? 'display: none;' : '' }}">
                                                                <label class="form-label small mb-1">Mata Pelajaran & Guru</label>
                                                                <div class="d-flex gap-2 align-items-center">
                                                                <input type="text"
                                                                class="form-control form-control-sm assignment-search"
                                                                placeholder="Ketik untuk mencari..."
                                                                autocomplete="off"
                                                                onkeyup="searchAssignment(this)"
                                                                onclick="showSearchResults(this)"
                                                                data-class-id="{{ $class->id }}"
                                                                @php
                                                                    $assignmentText = '';
                                                                    if (isset($schedule['assignment_id']) && $schedule['assignment_id']) {
                                                                        $assignment = collect($teachingAssignments)->firstWhere('id', $schedule['assignment_id']);
                                                                        if ($assignment) {
                                                                            $assignmentText = $assignment['subject_name'] . ' - ' . $assignment['teacher_name'];
                                                                        }
                                                                    }
                                                                @endphp
                                                                value="{{ $assignmentText }}">
                                                            <input type="hidden"
                                                                name="schedules[{{ $day }}][{{ $index }}][assignment_id]"
                                                                class="assignment-id"
                                                                value="{{ $schedule['assignment_id'] ?? '' }}">
                                                            <div class="search-results"></div>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-danger"
                                                                        onclick="this.closest('.schedule-row').remove()">
                                                                        Hapus
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-1 text-center assignment-hidden"
                                                                style="{{ $schedule['session_type'] == 'Jam Pelajaran' ? 'display: none;' : '' }}">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    onclick="this.closest('.schedule-row').remove()">
                                                                    Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="card-footer bg-white text-end py-2 px-3">
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                    onclick="addScheduleRow('{{ $day }}')">
                                                    <i class="fas fa-plus"></i> Tambah
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-block btn-flat text-white" style="background-color: #1D3F72">
                                            <i class="fa fa-save"></i> Update Jadwal
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .schedule-row {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem !important;
            background-color: transparent;
        }

        .search-input {
            position: relative;
        }

        .search-results {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            z-index: 1000;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            top: 100%;
            left: 0;
        }

        .search-result-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.875rem;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .form-label.small {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.25rem;
        }

        .card-body.schedule-body {
            min-height: 60px;
        }

        .assignment-container .d-flex {
            min-height: 31px;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .gap-2 {
            gap: 0.5rem !important;
        }
        .schedule-row {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 1rem !important;
            margin-bottom: 1.5rem !important; 
            background-color: #fafafa; 
            min-height: 90px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
        }

        .schedule-row .col-md-2,
        .schedule-row .col-md-5,
        .schedule-row .col-md-1 {
            margin-bottom: 0.75rem;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        .schedule-body {
            padding: 1.5rem !important;
        }

        .schedule-row.row {
            margin-left: -0.5rem !important;
            margin-right: -0.5rem !important;
            margin-bottom: 1.5rem !important;
        }
        .schedule-row .form-select,
        .schedule-row .form-control {
            height: 31px; 
            line-height: 1.25;
        }

        .schedule-row .form-label.small {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.375rem !important;
            display: block;
            min-height: 18px; 
        }

        .assignment-container .d-flex {
            min-height: 31px;
            align-items: stretch; 
        }

        .schedule-row .btn-sm {
            padding: 0.375rem 0.75rem; 
            font-size: 0.75rem;
            line-height: 1.25;
            height: 31px;
        }

        .search-input {
            position: relative;
            flex-grow: 1;
        }

        .search-results {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            z-index: 1000;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            top: 100%;
            left: 0;
            margin-top: 2px;
        }

        .search-result-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.875rem;
            line-height: 1.4;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }
        .card-body.schedule-body {
            min-height: 120px !important; 
            padding: 1.5rem !important; 
        }
        .card.mb-3.border-light.border {
            margin-bottom: 2rem !important;
        }

        .schedule-body:empty {
            min-height: 80px !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .schedule-body:empty::after {
            content: "Belum ada jadwal untuk hari ini";
            color: #6c757d;
            font-style: italic;
            font-size: 0.875rem;
        }
        .gap-2 {
            gap: 0.75rem !important;
        }

        @media (max-width: 768px) {
            .schedule-row {
                padding: 0.5rem;
                margin-bottom: 0.75rem !important;
            }
            
            .schedule-row .col-md-2,
            .schedule-row .col-md-5,
            .schedule-row .col-md-1 {
                margin-bottom: 0.75rem;
            }
        }
        .schedule-row.row.g-2.align-items-end {
            align-items: flex-end !important;
        }
        .schedule-row .form-select,
        .schedule-row .form-control,
        .schedule-row .btn {
            margin-top: auto; 
        }
    </style>

    <script>
        const subjects = @json($subjects);
        const hoursData = @json($hoursData);
        const teachingAssignments = @json($teachingAssignments);
        const scheduleData = @json($scheduleData);

        function getHoursForDay(day) {
            return day === 'Jumat' ? hoursData.friday : hoursData.weekdays;
        }

        function addScheduleRow(day) {
    const container = document.getElementById('schedule-' + day);
    const index = container.children.length;
    const classId = document.getElementById('class_id').value;

    const row = document.createElement('div');
    row.className = 'row g-2 align-items-end mb-2 schedule-row';

    row.innerHTML = `
        <div class="col-md-2">
            <label class="form-label small mb-1">Tipe Sesi</label>
            <select
                name="schedules[${day}][${index}][session_type]"
                class="form-select form-select-sm session-type"
                onchange="filterHours(this)"
                required
            >
                <option value="">-- Pilih --</option>
                <option value="Jam Pelajaran">Jam Pelajaran</option>
                <option value="Jam Istirahat">Jam Istirahat</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small mb-1">Jam Mulai</label>
            <select
                name="schedules[${day}][${index}][start_hour_id]"
                class="form-select form-select-sm hour-select-start"
                onchange="updateEndHours(this); toggleSubjectTeacher(this)"
                data-day="${day}"
                required
            >
                <option value="">-- Pilih Jam Mulai --</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small mb-1">Jam Selesai</label>
            <select
                name="schedules[${day}][${index}][end_hour_id]"
                class="form-select form-select-sm hour-select-end"
                data-day="${day}"
                required
            >
                <option value="">-- Pilih Jam Selesai --</option>
            </select>
        </div>

        <div class="col-md-5 assignment-container">
            <label class="form-label small mb-1">Mata Pelajaran & Guru</label>
            <div class="d-flex gap-2 align-items-center">
                <div class="search-input flex-grow-1">
                    <input type="text"
                        class="form-control form-control-sm assignment-search"
                        placeholder="Ketik untuk mencari..."
                        autocomplete="off"
                        onkeyup="searchAssignment(this)"
                        onclick="showSearchResults(this)"
                        data-class-id="${classId}">
                    <input type="hidden"
                        name="schedules[${day}][${index}][assignment_id]"
                        class="assignment-id">
                    <div class="search-results"></div>
                </div>
                <button type="button"
                    class="btn btn-sm btn-outline-danger"
                    onclick="this.closest('.schedule-row').remove()">
                    Hapus
                </button>
            </div>
        </div>

        <div class="col-md-1 text-center assignment-hidden" style="display: none;">
            <button type="button"
                class="btn btn-sm btn-outline-danger"
                onclick="this.closest('.schedule-row').remove()">
                Hapus
            </button>
        </div>
    `;

    container.appendChild(row);
}

        // Assignment search handlers
        function searchAssignment(input) {
    const value = input.value.toLowerCase();
    const resultsBox = input.parentElement.querySelector('.search-results');
    const hiddenInput = input.parentElement.querySelector('.assignment-id');
    const classId = input.dataset.classId || document.getElementById('class_id').value; // Fallback ke class_id dari select

    resultsBox.innerHTML = '';
    resultsBox.style.display = 'none';

    if (value.length < 2) return;

            // Filter berdasarkan pencarian dan class_id
            const filtered = teachingAssignments.filter(a =>
        (a.subject_name.toLowerCase().includes(value) ||
         a.teacher_name.toLowerCase().includes(value)) &&
        (classId ? a.class_id == classId : true) // Tampilkan semua jika tidak ada classId
    );

    if (filtered.length === 0) {
        const div = document.createElement('div');
        div.className = 'search-result-item';
        div.style.color = '#6c757d';
        div.style.fontStyle = 'italic';
        div.textContent = 'Tidak ada hasil ditemukan';
        resultsBox.appendChild(div);
        resultsBox.style.display = 'block';
        return;
    }

    filtered.forEach(item => {
        const div = document.createElement('div');
        div.className = 'search-result-item';
        div.textContent = `${item.subject_name} - ${item.teacher_name}`;
        div.onclick = () => {
            input.value = div.textContent;
            hiddenInput.value = item.id;
            resultsBox.style.display = 'none';
        };
        resultsBox.appendChild(div);
    });

    resultsBox.style.display = 'block';
}

        function showSearchResults(input) {
            const box = input.parentElement.querySelector('.search-results');
            if (box && box.innerHTML.trim() !== '') {
                box.style.display = 'block';
            }
        }

        // Close search results when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-input')) {
                document.querySelectorAll('.search-results').forEach(el => el.style.display = 'none');
            }
        });

        function populateHourSelect(select, sessionType, day, selectedValue = '') {
            const hours = getHoursForDay(day);
            const filtered = hours.filter(h => h.session_type === sessionType);

            const options = filtered.map(h =>
                `<option value="${h.id}" data-type="${h.session_type}" data-start="${h.start_time}" data-end="${h.end_time}" data-slot="${h.slot_number}" ${selectedValue == h.id ? 'selected' : ''}>Jam ke-${h.slot_number}</option>`
            ).join('');

            select.innerHTML = `<option value="">-- Pilih Jam Mulai --</option>` + options;
        }

        function filterHours(select) {
            const sessionType = select.value;
            const row = select.closest('.schedule-row');
            const day = row.querySelector('.hour-select-start').dataset.day;

            const startSelect = row.querySelector('.hour-select-start');
            const endSelect = row.querySelector('.hour-select-end');

            // Clear previous selections
            startSelect.value = '';
            endSelect.value = '';

            populateHourSelect(startSelect, sessionType, day);
            populateHourSelect(endSelect, sessionType, day);

            toggleSubjectTeacher(startSelect);
        }

        function updateEndHours(startSelect) {
            const row = startSelect.closest('.schedule-row');
            const endSelect = row.querySelector('.hour-select-end');
            const day = startSelect.dataset.day;
            const sessionType = row.querySelector('.session-type').value;

            if (!startSelect.value || !sessionType) return;

            const hours = getHoursForDay(day);
            const startHourId = parseInt(startSelect.value);
            const startHour = hours.find(h => h.id === startHourId);

            if (!startHour) return;

            // Filter hours that are >= start hour and same session type
            const availableEndHours = hours.filter(h =>
                h.session_type === sessionType && h.id >= startHourId
            );

            const options = availableEndHours.map(h =>
                `<option value="${h.id}" data-type="${h.session_type}">Jam ke-${h.slot_number}</option>`
            ).join('');

            endSelect.innerHTML = `<option value="">-- Pilih Jam Selesai --</option>` + options;

            // Auto-select start hour as minimum end hour
            endSelect.value = startHourId;
        }

        function toggleSubjectTeacher(select) {
            const row = select.closest('.schedule-row');
            const sessionTypeSelect = row.querySelector('.session-type');
            const assignmentDiv = row.querySelector('.assignment-container');
            const assignmentHiddenDiv = row.querySelector('.assignment-hidden');
            const assignmentInput = row.querySelector('.assignment-search');
            const assignmentHiddenInput = row.querySelector('.assignment-id');

            const sessionType = sessionTypeSelect.value;
            const isBreak = (sessionType === 'Jam Istirahat');

            if (isBreak) {
                assignmentDiv.style.display = 'none';
                assignmentHiddenDiv.style.display = '';
                assignmentInput.removeAttribute('required');
                assignmentInput.value = '';
                assignmentHiddenInput.value = '';
            } else {
                assignmentDiv.style.display = '';
                assignmentHiddenDiv.style.display = 'none';
                assignmentInput.setAttribute('required', 'required');
            }
        }

        // Filter assignments based on selected class
        function filterAssignmentsByClass(classId) {
    // Update class_id pada semua input assignment yang belum memiliki nilai
    document.querySelectorAll('.assignment-search').forEach(input => {
        // Hanya reset jika input kosong (untuk row baru)
        if (!input.value) {
            input.dataset.classId = classId;
        }
    });

    // Sembunyikan semua hasil pencarian
    document.querySelectorAll('.search-results').forEach(el => el.style.display = 'none');
}

        document.addEventListener('DOMContentLoaded', function() {
    // Initialize class filter
    const classSelect = document.getElementById('class_id');
    if (classSelect.value) {
        filterAssignmentsByClass(classSelect.value);
    }

    // Add event listener for class change
    classSelect.addEventListener('change', function() {
        // Konfirmasi jika user mengubah kelas dan ada data assignment yang sudah terisi
        const hasAssignments = document.querySelectorAll('.assignment-search').some(input => input.value);
        if (hasAssignments) {
            if (confirm('Mengubah kelas akan menghapus semua mata pelajaran dan guru yang sudah dipilih. Lanjutkan?')) {
                // Reset semua assignment
                document.querySelectorAll('.assignment-search').forEach(input => {
                    input.value = '';
                    input.nextElementSibling.value = '';
                });
                filterAssignmentsByClass(this.value);
            } else {
                // Kembalikan ke nilai sebelumnya
                this.value = this.dataset.previousValue || '';
                return;
            }
        } else {
            filterAssignmentsByClass(this.value);
        }
        
        // Simpan nilai saat ini untuk rollback
        this.dataset.previousValue = this.value;
    });

    // Set initial previous value
    classSelect.dataset.previousValue = classSelect.value;

    // Initialize semua assignment input dengan class_id yang benar
    document.querySelectorAll('.assignment-search').forEach(input => {
        if (!input.dataset.classId) {
            input.dataset.classId = classSelect.value;
        }
    });

    // Initialize all existing schedule rows
    document.querySelectorAll('.session-type').forEach(select => {
        const sessionType = select.value;
        if (sessionType) {
            const row = select.closest('.schedule-row');
            const day = row.querySelector('.hour-select-start').dataset.day;
            const startSelect = row.querySelector('.hour-select-start');
            const endSelect = row.querySelector('.hour-select-end');

            // Get the current selected values
            const startValue = startSelect.dataset.selected || '';
            const endValue = endSelect.dataset.selected || '';

            // Populate hour selects with current values
            populateHourSelect(startSelect, sessionType, day, startValue);
            populateHourSelect(endSelect, sessionType, day, endValue);

            // Set the selected values
            if (startValue) startSelect.value = startValue;
            if (endValue) endSelect.value = endValue;

            // Initialize subject/teacher visibility
            toggleSubjectTeacher(startSelect);
        }
    });
});
    </script>
@endsection