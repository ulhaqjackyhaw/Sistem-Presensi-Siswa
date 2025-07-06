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
                                    <label for="edit_start_year_input">Tahun Mulai</label>
                                    <input type="number" name="start_year" id="edit_start_year_input"
                                        class="form-control @error('start_year') is-invalid @enderror"
                                        placeholder="Contoh: 2025"
                                        value="{{ old('start_year', $academicYear->start_year) }}">
                                    @error('start_year')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Input Tahun Selesai (end_year) dijadikan tersembunyi --}}
                                <input type="hidden" name="end_year" id="edit_end_year_input" value="{{ old('end_year', $academicYear->end_year) }}">
                                {{--
                                    Label dan @error untuk end_year tidak lagi ditampilkan karena inputnya hidden.
                                    Validasi di backend untuk end_year (seperti gt:start_year) tetap akan berjalan.
                                --}}

                                <div class="form-group">
                                    <label>Semester</label>
                                    <select name="semester" class="form-control @error('semester') is-invalid @enderror">
                                        <option value="">-- Pilih Semester --</option>
                                        <option value="0" {{ old('semester', $academicYear->semester) == '0' ? 'selected' : '' }}>Genap</option>
                                        <option value="1" {{ old('semester', $academicYear->semester) == '1' ? 'selected' : '' }}>Ganjil</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Aktifkan Tahun Akademik Ini?</label><br>
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', $academicYear->is_active) == 1 ? 'checked' : '' }}> Ya
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startYearInput = document.getElementById('edit_start_year_input');
        const endYearInput = document.getElementById('edit_end_year_input'); // Tetap mengambil elemen berdasarkan ID

        function updateEndYear() {
            const startYearValue = parseInt(startYearInput.value);
            if (!isNaN(startYearValue) && startYearInput.value.length === 4) {
                endYearInput.value = startYearValue + 1; // Mengisi value input hidden
            } else {
                // Jika start_year dikosongkan, end_year juga dikosongkan
                // Jika tidak, biarkan value lama (dari old() atau database) jika start_year tidak valid tapi tidak kosong
                if (startYearInput.value === '') {
                    endYearInput.value = '';
                }
            }
        }

        // Panggil saat nilai start_year berubah
        startYearInput.addEventListener('input', updateEndYear);

        // Panggil saat halaman dimuat untuk menginisialisasi end_year
        // berdasarkan nilai start_year yang ada (dari $academicYear atau old('start_year'))
        updateEndYear();
    });
</script>
@endpush