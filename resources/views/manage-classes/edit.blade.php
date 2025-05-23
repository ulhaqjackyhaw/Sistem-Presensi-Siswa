@extends('layouts.app')

@section('title', 'Edit Data Kelas')

@section('content')
<div class="container-fluid px-4">
    <h3 class="fw-bold mb-4">Edit Data Kelas</h3>

    <div class="card shadow-sm rounded">
        <div class="card-header">
            <h5 class="mb-0">Form Edit Data Kelas</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('manage-classes.update', $class->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Kelas</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $class->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="parallel_name" class="form-label">Parallel</label>
                    <input type="text" class="form-control" id="parallel_name" name="parallel_name" value="{{ old('parallel_name', $class->parallel_name) }}" required>
                    @error('parallel_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="academic_year_id" class="form-label">Tahun Akademik</label>
                    <select class="form-control" id="academic_year_id" name="academic_year_id" required>
                        <option value="">-- Pilih Tahun Akademik --</option>
                        @foreach ($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id', $class->academic_year_id) == $year->id ? 'selected' : '' }}>
                                {{ str_replace(['Semester 0', 'Semester 1'], ['Semester Ganjil', 'Semester Genap'], $year->year_label) }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                

                <div class="mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <!-- Hidden input to ensure '0' is sent when checkbox is unchecked -->
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            id="is_active" 
                            name="is_active" 
                            value="1" 
                            {{ old('is_active', $class->is_active) == '1' ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="is_active">
                            Aktif
                        </label>
                    </div>
                    @error('is_active')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('manage-classes.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
