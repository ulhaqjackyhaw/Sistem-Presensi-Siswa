@extends('layouts.app')

@section('title')
    Tambah Data Pengajaran Guru - Mapel
@endsection

@push('css')
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Tambah Data Pengajaran Guru ke Mapel</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    </ol>
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
                            <h5 class="card-title m-0">Form Penugasan Guru ke Mapel</h5>
                            <div class="card-tools">
                                <a href="{{ route('manage-teacher-subject-assignments.index') }}" class="btn btn-tool">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('manage-teacher-subject-assignments.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label>Mata Pelajaran</label>
                                    <select name="subject_id" class="form-control @error('subject_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Guru</label>
                                    <select name="teacher_id" class="form-control @error('teacher_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->nip }}" {{ old('teacher_id') == $teacher->nip ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('teacher_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Kelas</label>
                                    <select name="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} - {{ $class->parallel_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Tahun Akademik</label>
                                    <select name="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Tahun Akademik --</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->start_year }}/{{ $year->end_year }}
                                                {{ $year->semester == 1 ? '- Ganjil' : '- Genap' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(count($academicYears) == 0)
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle"></i> Tidak ada data tahun akademik.
                                        Tambahkan tahun akademik terlebih dahulu.
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-block btn-flat btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush