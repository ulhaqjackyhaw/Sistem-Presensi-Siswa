@extends('layouts.app')

@push('css')
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Edit Data Mata Pelajaran</h4>
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
                            <h5 class="card-title m-0">Form Data Mata Pelajaran</h5>
                            <div class="card-tools">
                                <a href="{{ route('manage-subject.index') }}" class="btn btn-tool">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('manage-subject.update', $subject->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Kode Mata Pelajaran</label>
                                    <input type="text" name="subject_code"
                                        class="form-control @error('subject_code') is-invalid @enderror"
                                        value="{{ old('subject_code', $subject->subject_code) }}"
                                        placeholder="Contoh: MAT01">
                                    @error('subject_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Nama Mata Pelajaran</label>
                                    <input type="text" name="subject_name"
                                        class="form-control @error('subject_name') is-invalid @enderror"
                                        value="{{ old('subject_name', $subject->subject_name) }}"
                                        placeholder="Contoh: Matematika">
                                    @error('subject_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Nama Kurikulum</label>
                                    <select name="curriculum_name" class="form-control @error('curriculum_name') is-invalid @enderror">
                                        <option value="">-- Pilih Kurikulum --</option>
                                        @foreach ($curriculums as $item)
                                            <option value="{{ $item->curriculum_name }}"
                                                {{ old('curriculum_name', isset($subject) ? $subject->curriculum_name : '') == $item->curriculum_name ? 'selected' : '' }}>
                                                {{ $item->curriculum_name }}
                                            </option>
                                        @endforeach
                                     </select>
                                     @error('curriculum_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Deskripsi mata pelajaran">{{ old('description', $subject->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
