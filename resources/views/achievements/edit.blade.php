@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Prestasi</h1>
    <form action="{{ route('achievements.update', $achievement) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="student_id">Siswa</label>
            <input type="text" id="student_autocomplete" class="form-control" placeholder="Ketik nama siswa..." autocomplete="off" required value="{{ old('student_name', $achievement->student ? $achievement->student->name : '') }}">
            <input type="hidden" name="student_id" id="student_id" value="{{ old('student_id', $achievement->student_id) }}">
        </div>
        <div class="form-group">
            <label for="achievements_name">Nama Prestasi</label>
            <input type="text" name="achievements_name" id="achievements_name" class="form-control" value="{{ old('achievements_name', $achievement->achievements_name) }}" required>
        </div>
        <div class="form-group">
            <label for="achievement_points_id">Jenis Prestasi</label>
            <select name="achievement_points_id" id="achievement_points_id" class="form-control" required>
                <option value="">Pilih Jenis Prestasi</option>
                @foreach($achievementPoints as $point)
                    <option value="{{ $point->id }}" {{ old('achievement_points_id', $achievement->achievement_points_id) == $point->id ? 'selected' : '' }}>{{ $point->achievement_type }} ({{ $point->achievement_category }}, {{ $point->points }} poin)</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="achievement_date">Tanggal Prestasi</label>
            <input type="date" name="achievement_date" id="achievement_date" class="form-control" value="{{ old('achievement_date', $achievement->achievement_date ? $achievement->achievement_date->format('Y-m-d') : '') }}" required>
        </div>
        <div class="form-group">
            <label for="academic_year_id">Tahun Akademik</label>
            <select name="academic_year_id" id="academic_year_id" class="form-control" required>
                <option value="">-- Pilih Tahun Akademik --</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ old('academic_year_id', $achievement->academic_year_id) == $year->id ? 'selected' : '' }}>{{ $year->start_year }}/{{ $year->end_year }} {{ $year->semester == 0 ? 'Genap' : 'Ganjil' }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description', $achievement->description) }}</textarea>
        </div>
        <div class="form-group">
            <label for="evidence">Bukti (opsional, jpg/jpeg/png/pdf)</label>
            <input type="file" name="evidence" id="evidence" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf">
            @if($achievement->evidence)
                <br><a href="{{ asset('storage/' . $achievement->evidence) }}" target="_blank" class="btn btn-info btn-sm mt-2">Lihat Bukti Lama</a>
            @endif
        </div>
        <input type="hidden" name="status" value="{{ old('status', $achievement->status) }}">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('achievements.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

@push('js')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function() {
    $("#student_autocomplete").autocomplete({
        source: "{{ route('autocomplete.siswa') }}",
        minLength: 0, // ubah dari 2 ke 0 agar langsung muncul saat klik/fokus
        select: function(event, ui) {
            $('#student_id').val(ui.item.id);
            $('#student_autocomplete').val(ui.item.value);
        }
    }).on('focus', function () {
        $(this).autocomplete("search", "");
    });

    @if ($achievement->student && $achievement->student->currentAssignment && $achievement->student->currentAssignment->schoolClass)
        var studentName = "{{ $achievement->student->name }}";
        var className = "{{ $achievement->student->currentAssignment->schoolClass->name }}";
        var parallelName = "{{ $achievement->student->currentAssignment->schoolClass->parallel_name }}";
        var classInfo = '';

        if (className && parallelName) {
            classInfo = ' - ' + className + ' ' + parallelName;
        } else if (className) {
            classInfo = ' - ' + className;
        }
        $('#student_autocomplete').val(studentName + classInfo);
    @endif
});
</script>
@endpush
