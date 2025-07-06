@extends('layouts.app')

@section('title')
    Edit Keputusan Validasi
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-uppercase">Edit Keputusan Validasi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('violation-validations.index') }}">Validasi Pelanggaran</a></li>
                    <li class="breadcrumb-item active">Edit Validasi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Form Edit Keputusan Validasi</h3>
                    </div>
                    <form action="{{ route('violation-validations.updateValidation', $violation->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="validation_status">Status Validasi <span class="text-danger">*</span></label>
                                <select name="validation_status" id="validation_status" class="form-control" required>
                                    <option value="approved" {{ $violation->validation_status == 'approved' ? 'selected' : '' }}>Setujui Laporan</option>
                                    <option value="rejected" {{ $violation->validation_status == 'rejected' ? 'selected' : '' }}>Tolak Laporan</option>
                                </select>
                            </div>                            <div class="form-group">
                                <label for="validation_notes">Catatan Validasi <span class="text-danger" id="required-indicator" style="display: none;">*</span></label>
                                <textarea name="validation_notes" id="validation_notes" class="form-control" rows="3" placeholder="Berikan catatan jika diperlukan...">{{ old('validation_notes', $violation->validation_notes) }}</textarea>
                                <small class="text-muted">* Catatan wajib diisi jika laporan ditolak</small>
                            </div>
                        </div>                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('violation-validations.show', $violation->id) }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Update indikator required dan validasi dinamis
        function updateValidationRequirement() {
            var status = $('#validation_status').val();
            var indicator = $('#required-indicator');
            var textarea = $('#validation_notes');

            if (status === 'rejected') {
                indicator.show();
                textarea.attr('placeholder', 'Catatan wajib diisi ketika menolak laporan...');
                textarea.prop('required', true);
            } else {
                indicator.hide();
                textarea.attr('placeholder', 'Berikan catatan jika diperlukan...');
                textarea.prop('required', false);
            }
        }

        // Panggil fungsi saat halaman dimuat
        updateValidationRequirement();

        // Update ketika status berubah
        $('#validation_status').on('change', updateValidationRequirement);

        // Validasi sebelum submit
        $('form').on('submit', function(e) {
            var status = $('#validation_status').val();
            var notes = $('#validation_notes').val().trim();

            if (status === 'rejected' && !notes) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Validasi Wajib Diisi',
                    text: 'Catatan validasi wajib diisi ketika menolak laporan.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary mx-2' },
                    buttonsStyling: false
                });
                return false;
            }

            // Konfirmasi sebelum submit
            e.preventDefault();
            var actionText = status === 'approved' ? 'menyetujui' : 'menolak';

            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: 'Anda yakin ingin ' + actionText + ' laporan pelanggaran ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'swal2-confirm btn btn-primary mx-2',
                    cancelButton: 'swal2-cancel btn btn-secondary mx-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
