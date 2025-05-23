@extends('layouts.app')

@section('title', 'Edit Data Siswa')

@section('content')
    <div class="container-fluid mt-4" style="font-family: 'Roboto', sans-serif; max-width: 1240px;">
        <h1 class="mt-4" style="font-weight: bold; font-size: 2rem; color: #183C70;">Edit Data Siswa</h1>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <form action="{{ route('manage-students.update', $student->nisn) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">

                            <!-- Tombol Kembali -->
                            <div class="d-flex justify-content-end mb-3">
                                <a href="{{ route('manage-students.index') }}"
                                    style="width: 25px; height: 25px; background-color: #1777e5; border-radius: 50%; display: flex; justify-content: center; align-items: center; color: white; text-decoration: none;">
                                    <i class="fas fa-arrow-left" style="font-size: 14px;"></i>
                                </a>
                            </div>

                            <!-- NIS -->
                            <div class="form-group">
                                <label for="nis" class="font-weight-bold">NIS</label>
                                <input type="text" class="form-control @error('nis') is-invalid @enderror"
                                    id="nis" name="nis" value="{{ old('nis', $student->nis) }}" maxlength="25" required>
                                @error('nis')
                                    <div class="invalid-feedback">
                                        NIS tidak boleh lebih dari 20 karakter.
                                    </div>
                                @enderror

                            </div>

                            <!-- NISN -->
                            <div class="form-group">
                                <label for="nisn" class="font-weight-bold">NISN</label>
                                <input type="text" class="form-control @error('nisn') is-invalid @enderror"
                                    id="nisn" name="nisn" value="{{ old('nisn', $student->nisn) }}" maxlength="10" required readonly>
                                @error('nisn')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Nama Lengkap -->
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $student->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Jenis Kelamin -->
                            <div class="form-group">
                                <label for="gender" class="font-weight-bold">Jenis Kelamin</label>
                                <select class="form-control @error('gender') is-invalid @enderror" id="gender"
                                    name="gender">
                                    <option value="L" {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="P" {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Tanggal Lahir -->
                            <div class="form-group">
                                <label for="birth_date" class="font-weight-bold">Tanggal Lahir</label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                    id="birth_date" name="birth_date" value="{{ old('birth_date', $student->birth_date) }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Alamat -->
                            <div class="form-group">
                                <label for="address" class="font-weight-bold">Alamat</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $student->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- No. Telepon -->
                            <div class="form-group">
                                <label for="phone" class="font-weight-bold">No. Telepon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Nama Orang Tua -->
                            <div class="form-group">
                                <label for="parent_name" class="font-weight-bold">Nama Orang Tua</label>
                                <input type="text" class="form-control @error('parent_name') is-invalid @enderror"
                                    id="parent_name" name="parent_name"
                                    value="{{ old('parent_name', $student->parent_name) }}">
                                @error('parent_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- No. Telepon Orang Tua -->
                            <div class="form-group">
                                <label for="parent_phone" class="font-weight-bold">No. Telepon Orang Tua</label>
                                <input type="text" class="form-control @error('parent_phone') is-invalid @enderror"
                                    id="parent_phone" name="parent_phone"
                                    value="{{ old('parent_phone', $student->parent_phone) }}">
                                @error('parent_phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Email Orang Tua -->
                            <div class="form-group">
                                <label for="parent_email" class="font-weight-bold">Email Orang Tua</label>
                                <input type="email" class="form-control @error('parent_email') is-invalid @enderror"
                                    id="parent_email" name="parent_email"
                                    value="{{ old('parent_email', $student->parent_email) }}">
                                @error('parent_email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Tahun Masuk -->
                            <div class="form-group">
                                <label for="enter_year" class="font-weight-bold">Tahun Masuk</label>
                                <input type="number" class="form-control @error('enter_year') is-invalid @enderror"
                                    id="enter_year" name="enter_year"
                                    value="{{ old('enter_year', $student->enter_year) }}">
                                @error('enter_year')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Foto -->
                            <div class="form-group">
                                <label for="photo" class="font-weight-bold">Foto</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('photo') is-invalid @enderror"
                                        id="photo" name="photo" accept="image/*">
                                    <label class="custom-file-label" for="photo">Pilih file</label>
                                </div>
                                @error('photo')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                @if ($student->photo)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto Siswa"
                                            class="img-thumbnail" style="max-height: 200px">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> SIMPAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
@endpush
