@extends('layouts.app')

@section('title')
    Ubah Penugasan Guru - Mapel
@endsection

@push('css')
<!-- DataTables (jika diperlukan) -->
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Ubah Data Pengajaran Guru ke Mapel</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    {{-- Breadcrumb opsional --}}
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Form Ubah Penugasan</h3>
                        <div class="card-tools">
                            <a href="{{ route('manage-teacher-subject-assignments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('manage-teacher-subject-assignments.update', $teacherAssignment->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="academic_year_id">Tahun Akademik</label>
                                <select name="academic_year_id" id="academic_year_id" class="form-control" required>
                                    <option value="">-- Pilih Tahun Akademik --</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ $year->id == $teacherAssignment->academic_year_id ? 'selected' : '' }}>
                                            {{ $year->start_year }} / {{ $year->end_year }} @if($year->semester) (Sem {{ $year->semester }})@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="class_id">Kelas</label>
                                <select name="class_id" id="class_id" class="form-control" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ $class->id == $teacherAssignment->class_id ? 'selected' : '' }}>
                                            {{ $class->name }} @if($class->parallel_name) - {{ $class->parallel_name }}@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="subject_id">Mata Pelajaran</label>
                                <select name="subject_id" id="subject_id" class="form-control" required>
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ $subject->id == $teacherAssignment->subject_id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="teacher_id">Guru</label>
                                <select name="teacher_id" id="teacher_id" class="form-control" required>
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->nip }}" {{ $teacher->nip == $teacherAssignment->teacher_id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<!-- Jika butuh JS khusus -->
@endpush
