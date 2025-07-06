@extends('layouts.app')

@section('title', 'Edit Data Siswa')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Edit Data Siswa</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right"></ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title m-0">Form Data Siswa</h5>
                            <div class="card-tools ml-auto">
                                <a href="{{ route('manage-students.index') }}" class="btn btn-tool" title="Kembali">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>
                        <form action="{{ route('manage-students.update', $student->nisn) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nis">NIS</label>
                                    <input type="text" class="form-control @error('nis') is-invalid @enderror" id="nis"
                                        name="nis" value="{{ old('nis', $student->nis) }}" maxlength="20" readonly required>
                                    @error('nis')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="nisn">NISN</label>
                                    <input type="text" class="form-control @error('nisn') is-invalid @enderror"
                                        id="nisn" name="nisn" value="{{ old('nisn', $student->nisn) }}" maxlength="10"
                                        required readonly>
                                    @error('nisn')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="name">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $student->name) }}">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="gender">Jenis Kelamin</label>
                                    <select class="form-control @error('gender') is-invalid @enderror" id="gender"
                                        name="gender">
                                        <option value="L" {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>
                                            Laki-laki</option>
                                        <option value="P" {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="birth_date">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                        id="birth_date" name="birth_date"
                                        value="{{ old('birth_date', $student->birth_date) }}">
                                    @error('birth_date')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="address">Alamat</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                        name="address" rows="3">{{ old('address', $student->address) }}</textarea>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="phone">No. Telepon</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="parent_name">Nama Orang Tua</label>
                                    <input type="text" class="form-control @error('parent_name') is-invalid @enderror"
                                        id="parent_name" name="parent_name"
                                        value="{{ old('parent_name', $student->parent_name) }}">
                                    @error('parent_name')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="parent_phone">No. Telepon Orang Tua</label>
                                    <input type="text" class="form-control @error('parent_phone') is-invalid @enderror"
                                        id="parent_phone" name="parent_phone"
                                        value="{{ old('parent_phone', $student->parent_phone) }}">
                                    @error('parent_phone')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="parent_email">Email Orang Tua</label>
                                    <input type="email" class="form-control @error('parent_email') is-invalid @enderror"
                                        id="parent_email" name="parent_email"
                                        value="{{ old('parent_email', $student->parent_email) }}">
                                    @error('parent_email')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="enter_year">Tahun Masuk</label>
                                    <input type="number" class="form-control @error('enter_year') is-invalid @enderror"
                                        id="enter_year" name="enter_year"
                                        value="{{ old('enter_year', $student->enter_year) }}">
                                    @error('enter_year')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="photo">Foto</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('photo') is-invalid @enderror"
                                            id="photo" name="photo" accept="image/*">
                                        <label class="custom-file-label" for="photo">Pilih file</label>
                                    </div>
                                    @error('photo')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
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
                                <button type="submit" class="btn btn-block btn-flat text-white"
                                    style="background-color: #1777E5">
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

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@push('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init();
            $('#photo').on('change', function() {
                const file = this.files[0];
                if (file && file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File terlalu besar',
                        text: 'Ukuran file maksimal 2MB!',
                        timer: 2500,
                        showConfirmButton: false
                    });
                    $(this).val('');
                    $(this).next('.custom-file-label').html('Pilih file (maks 2 MB)');
                }
            });
        });
    </script>
@endpush