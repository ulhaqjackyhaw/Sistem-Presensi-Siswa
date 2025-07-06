@extends('layouts.app')

@section('title')
    Form Lapor Prestasi
@endsection

@push('css')
<style>
    .form-group label {
        font-weight: 500;
    }
    .card-header h4, .card-header .card-title {
        margin-bottom: 0;
    }
    .form-text.text-muted {
        font-size: 0.875em;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-uppercase">Lapor Prestasi Baru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('achievements.index') }}">Laporan Prestasi</a></li>
                    <li class="breadcrumb-item active">Lapor Baru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Formulir Laporan Prestasi</h3>
                    </div>
                    <form id="form-lapor-prestasi" action="{{ route('achievements.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan:</strong>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="student_id">Siswa</label>
                                <input type="text" id="student_autocomplete" class="form-control" placeholder="Ketik nama siswa..." autocomplete="off" required>
                                <input type="hidden" name="student_id" id="student_id" value="{{ old('student_id') }}">
                            </div>
                            <div class="form-group">
                                <label for="achievements_name">Nama Prestasi</label>
                                <input type="text" name="achievements_name" id="achievements_name" class="form-control" value="{{ old('achievements_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="achievement_points_id">Jenis Prestasi</label>
                                <select name="achievement_points_id" id="achievement_points_id" class="form-control" required>
                                    <option value="">Pilih Jenis Prestasi</option>
                                    @foreach($achievementPoints as $point)
                                        <option value="{{ $point->id }}" {{ old('achievement_points_id') == $point->id ? 'selected' : '' }}>{{ $point->achievement_type }} ({{ $point->achievement_category }}, {{ $point->points }} poin)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="achievement_date">Tanggal Prestasi</label>
                                <input type="date" name="achievement_date" id="achievement_date" class="form-control" value="{{ old('achievement_date') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="academic_year_id">Tahun Akademik</label>
                                <select name="academic_year_id" id="academic_year_id" class="form-control" required>
                                    <option value="">-- Pilih Tahun Akademik --</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->start_year }}/{{ $year->end_year }} {{ $year->semester == 0 ? 'Genap' : 'Ganjil' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="evidence">Bukti (opsional, jpg/jpeg/png/pdf)</label>
                                <input type="file" name="evidence" id="evidence" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf">
                            </div>
                            <input type="hidden" name="status" value="pending">
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('achievements.index') }}" class="btn btn-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
                                <button type="button" id="btn-lapor-prestasi" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> Laporkan Prestasi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function() {
    $("#student_autocomplete").autocomplete({
        source: "{{ route('autocomplete.siswa') }}",
        minLength: 0,
        select: function(event, ui) {
            $('#student_id').val(ui.item.id);
            $('#student_autocomplete').val(ui.item.value);
        }
    }).on('focus', function () {
        $(this).autocomplete("search", "");
    });

    // SweetAlert konfirmasi sebelum submit
    $("#btn-lapor-prestasi").on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Laporan',
            text: 'Anda yakin ingin melaporkan prestasi ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Laporkan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'swal2-confirm btn btn-primary mx-2',
                cancelButton: 'swal2-cancel btn btn-secondary mx-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $('#form-lapor-prestasi').submit();
            }
        });
    });
});
</script>
@endpush
