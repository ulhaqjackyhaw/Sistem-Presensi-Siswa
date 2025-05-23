@extends('layouts.app')

@section('title', 'Tambah Data Siswa')

@section('content')
<div class="container-fluid mt-4" style="font-family: 'Roboto', sans-serif; max-width: 1240px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0" style="font-weight: bold; font-size: 2rem; color: #183C70;">Tambah Data Siswa</h1>
    </div>

    <div class="card shadow-sm position-relative">
        <!-- Tombol kembali di pojok kanan atas -->
        <a href="{{ route('manage-students.index') }}" class="position-absolute d-flex align-items-center justify-content-center"
           style="top: 15px; right: 20px; width: 25px; height: 25px; background-color: #1777e5; border-radius: 50%; color: white; text-decoration: none;">
            <i class="fas fa-arrow-left"></i>
        </a>

        <form action="{{ route('manage-students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="nis">NIS</label>
                    <input type="text" class="form-control @error('nis') is-invalid @enderror" id="nis" name="nis" value="{{ old('nis') }}" required>
                    @error('nis')
                        <div class="invalid-feedback">
                            NIS tidak boleh lebih dari 20 karakter.
                        </div>
                    @enderror

                </div>

                <div class="form-group">
                    <label for="nisn">NISN</label>
                    <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn" name="nisn" value="{{ old('nisn') }}" required>
                    @error('nisn')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gender">Jenis Kelamin</label>
                    <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="birth_date">Tanggal Lahir</label>
                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
                    @error('birth_date')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">Alamat</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                    @error('address')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">No. Telepon</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                    @error('phone')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="parent_name">Nama Orang Tua</label>
                    <input type="text" class="form-control @error('parent_name') is-invalid @enderror" id="parent_name" name="parent_name" value="{{ old('parent_name') }}" required>
                    @error('parent_name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="parent_phone">No. Telepon Orang Tua</label>
                    <input type="text" class="form-control @error('parent_phone') is-invalid @enderror" id="parent_phone" name="parent_phone" value="{{ old('parent_phone') }}">
                    @error('parent_phone')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="parent_email">Email Orang Tua</label>
                    <input type="email" class="form-control @error('parent_email') is-invalid @enderror" id="parent_email" name="parent_email" value="{{ old('parent_email') }}" required>
                    @error('parent_email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="enter_year">Tahun Masuk</label>
                    <input type="number" class="form-control @error('enter_year') is-invalid @enderror" id="enter_year" name="enter_year" value="{{ old('enter_year') }}" required>
                    @error('enter_year')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="photo">Foto</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                        <label class="custom-file-label" for="photo">Pilih file</label>
                    </div>
                    @error('photo')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
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
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.css') }}">
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
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
    </script>
@endpush