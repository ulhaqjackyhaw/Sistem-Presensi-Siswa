@extends('layouts.app')

@push('css')
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Edit Tahun Akademik</h4>
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
                            <h5 class="card-title m-0">Form Tahun Akademik</h5>
                            <div class="card-tools">
                                <a href="{{ route('manage-academic-years.index') }}" class="btn btn-tool">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('manage-academic-years.update', $academicYear->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Tahun Mulai</label>
                                    <input type="number" name="start_year"
                                        class="form-control @error('start_year') is-invalid @enderror"
                                        placeholder="Contoh: 2025"
                                        value="{{ old('start_year', $academicYear->start_year) }}">
                                    @error('start_year')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Tahun Selesai</label>
                                    <input type="number" name="end_year"
                                        class="form-control @error('end_year') is-invalid @enderror"
                                        placeholder="Contoh: 2026"
                                        value="{{ old('end_year', $academicYear->end_year) }}">
                                    @error('end_year')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Semester</label>
                                    <select name="semester" class="form-control @error('semester') is-invalid @enderror">
                                        <option value="">-- Pilih Semester --</option>
                                        <option value="0" {{ old('semester', $academicYear->semester) == 0 ? 'selected' : '' }}>Ganjil</option>
                                        <option value="1" {{ old('semester', $academicYear->semester) == 1 ? 'selected' : '' }}>Genap</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Aktifkan Tahun Akademik Ini?</label><br>
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', $academicYear->is_active) ? 'checked' : '' }}> Ya
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-block btn-flat text-white" style="background-color: #1777E5">
                                    <i class="fa fa-save"></i> Update
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
