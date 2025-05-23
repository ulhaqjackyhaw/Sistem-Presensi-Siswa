@extends('layouts.app')

@push('css')
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Edit Kurikulum</h4>
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
                            <h5 class="card-title m-0">Form Kurikulum</h5>
                            <div class="card-tools">
                                <a href="{{ route('manage-curriculums.index') }}" class="btn btn-tool">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('manage-curriculums.update', $curriculum->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Nama Kurikulum</label>
                                    <input type="text" name="curriculum_name"
                                        class="form-control @error('curriculum_name') is-invalid @enderror" value="{{ old('curriculum_name', $curriculum->curriculum_name) }}" 
                                        placeholder="Contoh: Kurikulum Merdeka 2024"
                                        value="{{ old('currriculum_name', $curriculum->curriculum_name) }}">
                                    @error('curriculum_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- <div class="form-group">
                                    <label>Aktifkan Kurikulum Ini?</label><br>
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', $curriculum->is_active) ? 'checked' : '' }}> Ya
                                </div> --}}
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
