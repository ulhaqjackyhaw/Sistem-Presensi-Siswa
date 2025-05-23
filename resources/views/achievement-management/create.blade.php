@extends('layouts.app')
@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

    body, input, button, select, textarea {
        font-family: 'Roboto', sans-serif !important;
    }

    .content-section {
        background-color: #f4f6f9;
        padding: 60px 0;
    }

    .title-prestasi {
        font-size: 36px;
        font-weight: 700;
        color: #003366;
        margin-bottom: 32px;
        text-align: left;
        margin-left: 60px;
    }

    .prestasi-card-wrapper {
        margin: 0 auto;
        padding: 0 60px;
    }

    .prestasi-card {
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        padding: 48px;
        position: relative;
        width: 100%;
    }

    .prestasi-card .form-group {
        margin-bottom: 28px;
    }

    .prestasi-card .form-group label {
        font-size: 18px;
        font-weight: 500;
        color: #003366;
        display: block;
        margin-bottom: 12px;
    }

    .prestasi-card .form-control {
        font-size: 18px;
        padding: 5px 20px;
        border-radius: 10px;
        border: 1px solid #ced4da;
        width: 100%;
        background-color: #fff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .prestasi-card .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .prestasi-card .form-control.is-invalid {
        border-color: #dc3545;
    }

    .prestasi-card .invalid-feedback {
        font-size: 14px;
        margin-top: 8px;
        color: #dc3545;
    }

    .select-wrapper {
        position: relative;
    }

    .select-wrapper select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;utf8,<svg fill='%23003366' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 20px;
        padding-right: 48px;
        z-index: 1;
        position: relative;
        cursor: pointer;
    }

    .btn-save {
        background-color: #007bff;
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        border: none;
        padding: 16px;
        border-radius: 10px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: background-color 0.2s ease;
        margin-top: 32px;
    }

    .btn-save:hover {
        background-color: #0056b3;
    }

    .back-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: #e9f2ff;
        border: none;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #007bff;
        font-size: 20px;
        transition: background-color 0.2s ease;
    }

    .back-btn:hover {
        background-color: #d0e7ff;
    }
</style>

<div class="content-section">
    <div class="container-fluid">
        <div class="title-prestasi">Tambah Data Prestasi</div>
        <div class="prestasi-card-wrapper">
            <div class="prestasi-card">
                <button type="button" onclick="window.history.back()" class="back-btn">
                    <i class="fa fa-arrow-left"></i>
                </button>

                <form action="{{ route('achievement-management.store') }}" method="POST">
                    @csrf

                    <!-- Dropdown Jenis Prestasi -->
                    <div class="form-group">
                        <label for="jenis_prestasi">Jenis Prestasi</label>
                        <select id="jenis_prestasi" name="jenis_prestasi" class="form-control @error('jenis_prestasi') is-invalid @enderror" required>
                            <option value="" disabled {{ old('jenis_prestasi') ? '' : 'selected' }}>Pilih jenis prestasi</option>
                            @php
                                $jenisOptions = [
                                    'Juara 1 Internasional',
                                    'Juara 2 Internasional',
                                    'Juara 3 Internasional',
                                    'Juara 1 Nasional',
                                    'Juara 2 Nasional',
                                    'Juara 3 Nasional',
                                    'Juara 1 Kota/Kabupaten',
                                    'Juara 2 Kota/Kabupaten',
                                    'Juara 3 Kota/Kabupaten',
                                ];
                            @endphp
                            @foreach ($jenisOptions as $option)
                                <option value="{{ $option }}" {{ old('jenis_prestasi') == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('jenis_prestasi')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Dropdown Kategori Prestasi -->
                    <div class="form-group">
                        <label for="kategori_prestasi">Kategori Prestasi</label>
                        <select id="kategori_prestasi" name="kategori_prestasi" class="form-control @error('kategori_prestasi') is-invalid @enderror" required>
                            <option value="" disabled {{ old('kategori_prestasi') ? '' : 'selected' }}>Pilih kategori prestasi</option>
                            <option value="Akademik" {{ old('kategori_prestasi') == 'Akademik' ? 'selected' : '' }}>Akademik</option>
                            <option value="Non Akademik" {{ old('kategori_prestasi') == 'Non Akademik' ? 'selected' : '' }}>Non Akademik</option>
                        </select>
                        @error('kategori_prestasi')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Input Poin -->
                    <div class="form-group">
                        <label for="poin">Poin</label>
                        <input type="number" id="poin" name="poin" class="form-control @error('poin') is-invalid @enderror" placeholder="0" value="{{ old('poin') }}" required>
                        @error('poin')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Tombol Simpan -->
                    <button type="submit" class="btn-save">
                        <i class="fa fa-save"></i> SIMPAN
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
