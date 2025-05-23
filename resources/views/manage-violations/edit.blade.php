@extends('layouts.app')

@push('css')
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Edit Pelanggaran</h4>
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
                            <h5 class="card-title m-0">Form Edit Pelanggaran</h5>
                            <div class="card-tools">
                                <a href="{{ route('violation-management.index') }}" class="btn btn-tool">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('violation-management.update', $violationPoint->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="violation_type">Nama Pelanggaran</label>
                                    <input type="text" name="violation_type"
                                           class="form-control @error('violation_type') is-invalid @enderror"
                                           placeholder="Contoh: Atribut sekolah tidak lengkap"
                                           value="{{ old('violation_type', $violationPoint->violation_type) }}">
                                    @error('violation_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="violation_level">Jenis Pelanggaran</label>
                                    <select name="violation_level"
                                            class="form-control @error('violation_level') is-invalid @enderror">
                                        <option value="">-- Pilih Jenis Pelanggaran --</option>
                                        <option value="Ringan" {{ old('violation_level', $violationPoint->violation_level) == 'Ringan' ? 'selected' : '' }}>Ringan</option>
                                        <option value="Sedang" {{ old('violation_level', $violationPoint->violation_level) == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                        <option value="Berat" {{ old('violation_level', $violationPoint->violation_level) == 'Berat' ? 'selected' : '' }}>Berat</option>
                                    </select>
                                    @error('violation_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="points">Poin</label>
                                    <input type="number" name="points"
                                           class="form-control @error('points') is-invalid @enderror"
                                           placeholder="Contoh: 10"
                                           value="{{ old('points', $violationPoint->points) }}">
                                    @error('points')
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
