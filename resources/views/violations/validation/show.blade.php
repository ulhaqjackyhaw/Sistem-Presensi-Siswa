@extends('layouts.app')

@section('title')
    Detail Laporan Pelanggaran
@endsection

@push('css')
    Tambahan CSS jika diperlukan
    <style>
        .table-borderless th,
        .table-borderless td {
            border: 0 !important;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        .table-borderless th {
            width: 35%; /* Lebar kolom label */
            font-weight: 600;
        }
        .badge {
            font-size: 0.9em;
        }
        .card-title {
            font-size: 1.1rem; /* Sedikit perbesar judul kartu */
        }

        /* Styling untuk modal bukti */
        .evidence-container {
            position: relative;
        }
        .loading-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #6c757d;
            z-index: 10;
        }
        .evidence-container img {
            cursor: zoom-in;
            transition: transform 0.2s ease;
        }
        .evidence-container img:hover {
            transform: scale(1.02);
        }

        /* Zoom modal styling */
        #zoomModal .modal-content {
            border: none;
        }
        #zoomModal img {
            cursor: zoom-out;
        }

        /* Button evidence styling */
        .btn-xs {
            padding: 0.2rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.2;
            border-radius: 0.2rem;
        }
    </style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-uppercase">Detail Laporan Pelanggaran</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li> {{-- Sesuaikan route 'home' --}}
                    <li class="breadcrumb-item"><a href="{{ route('violation-validations.index') }}">Validasi Pelanggaran</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10"> {{-- Atau col-md-12 untuk lebar penuh --}}
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Laporan Pelanggaran</h3>
                        <div class="card-tools">
                            <a href="{{ route('violation-validations.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Kolom Informasi Utama Pelanggaran --}}
                            <div class="col-md-7">
                                <h5 class="mb-3 text-primary">Rincian Pelanggaran</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <td>: {{ $violation->student ? $violation->student->name : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>NISN / ID Siswa</th>
                                        <td>: {{ $violation->student ? $violation->student->nisn : 'N/A' }}</td> {{-- Asumsi field nisn --}}
                                    </tr>
                                    <tr>
                                        <th>Jenis Pelanggaran</th>
                                        <td>: {{ $violation->violationPoint ? $violation->violationPoint->violation_type : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Poin Pelanggaran</th>
                                        <td>: {{ $violation->violationPoint ? $violation->violationPoint->points : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Pelanggaran</th>
                                        <td>: {{ $violation->violation_date ? \Carbon\Carbon::parse($violation->violation_date)->isoFormat('DD MMMM YYYY') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tahun Akademik</th>
                                        <td>
                                            : {{ $violation->academicYear ? $violation->academicYear->start_year . '/' . $violation->academicYear->end_year : '-' }}
                                            {{-- @if($violation->academicYear && isset($violation->academicYear->semester))
                                                (Semester: {{ $violation->academicYear->semester == 1 ? 'Ganjil' : ($violation->academicYear->semester == 2 ? 'Genap' : $violation->academicYear->semester) }})
                                            @endif --}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi/Kronologi</th>
                                        <td>: {{ $violation->description ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sanksi/Penalti Diberikan</th>
                                        <td>: {{ $violation->penalty ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dilaporkan Oleh</th>
                                        <td>: {{ $violation->teacher ? $violation->teacher->name : ($violation->reported_by ?: 'N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Laporan Dibuat</th>
                                        <td>: {{ $violation->created_at ? $violation->created_at->isoFormat('DD MMMM YYYY, HH:mm') : '-' }}</td>
                                    </tr>
                                    @if($violation->evidence)
                                    <tr>
                                        <th>Bukti Pelanggaran</th>
                                        <td>: <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#evidenceModal-{{ $violation->id }}" title="Klik untuk melihat bukti">
                                               <i class="fas fa-search-plus"></i> Lihat Bukti
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            {{-- Kolom Informasi Status dan Validasi --}}
                            <div class="col-md-5">
                                <h5 class="mb-3 text-primary">Status & Validasi</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th>Status Utama Laporan</th>
                                        <td>:
                                            @if($violation->validation_status === 'pending')
                                                @if(empty($violation->viewed_at))
                                                    <span class="badge badge-info">Menunggu Diproses</span>
                                                @else
                                                    <span class="badge badge-primary">Sedang Diproses</span>
                                                @endif
                                            @else
                                                <span class="badge badge-success">Selesai Diproses</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status Validasi Detail</th>
                                        <td>:
                                            @if($violation->validation_status === 'pending')
                                                <span class="badge badge-warning">Menunggu Validasi</span>
                                            @elseif($violation->validation_status === 'approved')
                                                <span class="badge badge-success">Disetujui</span>
                                            @elseif($violation->validation_status === 'rejected')
                                                <span class="badge badge-danger">Ditolak</span>
                                            @else
                                                <span class="badge badge-secondary">{{ Str::title($violation->validation_status ?? 'N/A') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($violation->validation_status !== 'pending')
                                    <tr>
                                        <th>Divalidasi Oleh</th>
                                        <td>: {{ $violation->validator ? $violation->validator->name : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Validasi</th>
                                        <td>: {{ $violation->validated_at ? \Carbon\Carbon::parse($violation->validated_at)->isoFormat('DD MMMM YYYY, HH:mm') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Catatan Validasi</th>
                                        <td>: {{ $violation->validation_notes ?: '-' }}</td>
                                    </tr>
                                    @endif
                                </table>

                                {{-- Tombol Aksi Tambahan --}}
                                <div class="mt-4">
                                    @can('update', $violation) {{-- Sesuaikan dengan Policy/Gate Anda --}}
                                        @if($violation->validation_status === 'pending' || Auth::user()->can('edit_any_violation_report')) {{-- Sesuaikan permission --}}
                                            <a href="{{ route('violations.edit', $violation->id) }}" class="btn btn-warning btn-sm mb-1">
                                                <i class="fas fa-edit"></i> Edit Laporan
                                            </a>
                                        @endif
                                    @endcan

                                    @role('Guru BK')
                                        @if($violation->validation_status === 'pending')
                                            <button class="btn btn-success btn-sm mb-1" data-toggle="modal" data-target="#validateModalShow-{{ $violation->id }}">
                                                <i class="fas fa-check-circle"></i> Validasi Laporan
                                            </button>
                                        @endif

                                        @if($violation->validator_id === Auth::user()->teacher->nip)
                                            <button class="btn btn-warning btn-sm mb-1" data-toggle="modal" data-target="#editValidationModal-{{ $violation->id }}">
                                                <i class="fas fa-edit"></i> Edit Keputusan Validasi
                                            </button>
                                        @endif
                                    @endrole

                                     @can('delete', $violation) {{-- Sesuaikan dengan Policy/Gate Anda --}}
                                        @if($violation->validation_status === 'pending' || Auth::user()->can('delete_any_violation_report')) {{-- Sesuaikan permission --}}
                                            <form action="{{ route('violations.destroy', $violation->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan pelanggaran ini?')">
                                                    <i class="fas fa-trash"></i> Hapus Laporan
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Validasi --}}
@role('Guru BK')
@if($violation->validation_status === 'pending')
<div class="modal fade" id="validateModalShow-{{ $violation->id }}" tabindex="-1" role="dialog" aria-labelledby="validateModalLabelShow-{{ $violation->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="form-validate-{{ $violation->id }}" action="{{ route('violations.validate', $violation->id) }}" method="POST">
            @csrf
            <input type="hidden" name="validation_status" id="validation_status_modal-{{ $violation->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validateModalLabelShow-{{ $violation->id }}">Validasi Laporan Pelanggaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p>Anda akan memvalidasi laporan untuk siswa: <strong>{{ $violation->student ? $violation->student->name : 'N/A' }}</strong></p>
                    <p>Pelanggaran: <strong>{{ $violation->violationPoint ? $violation->violationPoint->violation_type : 'N/A' }}</strong></p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-success btn-validate-action mx-2" data-id="{{ $violation->id }}" data-status="approved">
                            <i class="fas fa-check-circle"></i> Validasi (Setujui)
                        </button>
                        <button type="button" class="btn btn-danger btn-validate-action mx-2" data-id="{{ $violation->id }}" data-status="rejected">
                            <i class="fas fa-times-circle"></i> Tolak Laporan
                        </button>
                    </div>                    <div class="form-group mt-4">
                        <label for="validation_notes_modal-{{ $violation->id }}">Catatan Validasi <span class="text-danger" id="required-indicator-{{ $violation->id }}" style="display: none;">*</span></label>
                        <textarea name="validation_notes" id="validation_notes_modal-{{ $violation->id }}" class="form-control" rows="3" placeholder="Berikan catatan jika diperlukan...">{{ old('validation_notes') }}</textarea>
                        <small class="text-muted">* Catatan wajib diisi jika laporan ditolak</small>
                    </div>
                </div>
                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div> --}}
            </div>
        </form>
    </div>
</div>
@endif
@endrole

{{-- Modal Edit Validasi --}}
@role('Guru BK')
@if($violation->validation_status !== 'pending' && $violation->validator_id === Auth::user()->teacher->nip)
<div class="modal fade" id="editValidationModal-{{ $violation->id }}" tabindex="-1" role="dialog" aria-labelledby="editValidationModalLabel-{{ $violation->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="form-edit-validate-{{ $violation->id }}" action="{{ route('violation-validations.updateValidation', $violation->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="validation_status" id="edit_validation_status_modal-{{ $violation->id }}" value="{{ $violation->validation_status }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editValidationModalLabel-{{ $violation->id }}">Edit Keputusan Validasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Edit keputusan validasi untuk siswa: <strong>{{ $violation->student ? $violation->student->name : 'N/A' }}</strong></p>
                    <p>Pelanggaran: <strong>{{ $violation->violationPoint ? $violation->violationPoint->violation_type : 'N/A' }}</strong></p>

                    <div class="form-group mb-3">
                        <label>Status Validasi Saat Ini:</label>
                        @if($violation->validation_status === 'approved')
                            <span class="badge badge-success ml-2">Disetujui</span>
                        @elseif($violation->validation_status === 'rejected')
                            <span class="badge badge-danger ml-2">Ditolak</span>
                        @endif
                    </div>

                    <div class="form-group mb-3">
                        <label>Ubah Status Ke:</label>
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <button type="button" class="btn btn-success btn-edit-validate-action mx-2" data-id="{{ $violation->id }}" data-status="approved"
                                {{ $violation->validation_status === 'approved' ? 'disabled' : '' }}>
                                <i class="fas fa-check-circle"></i> Setujui
                            </button>
                            <button type="button" class="btn btn-danger btn-edit-validate-action mx-2" data-id="{{ $violation->id }}" data-status="rejected"
                                {{ $violation->validation_status === 'rejected' ? 'disabled' : '' }}>
                                <i class="fas fa-times-circle"></i> Tolak
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_validation_notes_modal-{{ $violation->id }}">Catatan Validasi <span class="text-danger" id="edit-required-indicator-{{ $violation->id }}" style="display: none;">*</span></label>
                        <textarea name="validation_notes" id="edit_validation_notes_modal-{{ $violation->id }}" class="form-control" rows="3" placeholder="Berikan catatan jika diperlukan...">{{ old('validation_notes', $violation->validation_notes) }}</textarea>
                        <small class="text-muted">* Catatan wajib diisi jika laporan ditolak</small>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endrole

{{-- Modal Bukti Pelanggaran --}}
@if($violation->evidence)
<div class="modal fade" id="evidenceModal-{{ $violation->id }}" tabindex="-1" role="dialog" aria-labelledby="evidenceModalLabel-{{ $violation->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="evidenceModalLabel-{{ $violation->id }}">
                    <i class="fas fa-image"></i> Bukti Pelanggaran
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <strong>Siswa:</strong> {{ $violation->student ? $violation->student->name : 'N/A' }}<br>
                    <strong>Pelanggaran:</strong> {{ $violation->violationPoint ? $violation->violationPoint->violation_type : 'N/A' }}<br>
                    <strong>Tanggal:</strong> {{ $violation->violation_date ? \Carbon\Carbon::parse($violation->violation_date)->isoFormat('DD MMMM YYYY') : '-' }}
                </div>
                <div class="evidence-container" style="max-height: 70vh; overflow: auto;">
                    <img src="{{ Storage::url($violation->evidence) }}"
                         alt="Bukti Pelanggaran"
                         class="img-fluid evidence-image"
                         style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); opacity: 0; transition: opacity 0.3s;"
                         onload="this.style.opacity='1'"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkdhbWJhciB0aWRhayB0ZXJzZWRpYTwvdGV4dD48L3N2Zz4='; this.alt='Gambar tidak tersedia';"
                         title="Klik untuk memperbesar gambar">
                </div>
                <div class="mt-3">
                    <small class="text-muted">Klik gambar untuk memperbesar atau klik tombol download untuk mengunduh</small>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ Storage::url($violation->evidence) }}" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt"></i> Buka di Tab Baru
                </a>
                <a href="{{ Storage::url($violation->evidence) }}" download class="btn btn-success">
                    <i class="fas fa-download"></i> Download
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Ganti tombol aksi validasi/penolakan
        $('.btn-validate-action').on('click', function(e) {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var notes = $('#validation_notes_modal-' + id).val().trim();

            // Validasi dinamis: catatan wajib hanya jika status = rejected
            if (status === 'rejected' && !notes) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Validasi Wajib Diisi',
                    text: 'Catatan validasi wajib diisi ketika menolak laporan.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary mx-2' },
                    buttonsStyling: false
                });
                return;
            }

            var select = $('#validation_status_modal-' + id);
            select.val(status); // Set value select sesuai tombol
            var actionText = status === 'approved' ? 'memvalidasi (MENYETUJUI)' : 'menolak';
            var confirmText = status === 'approved' ? 'Ya, Validasi!' : 'Ya, Tolak!';
            var dangerMode = status === 'approved' ? 'success' : 'warning';

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin ' + actionText + ' laporan pelanggaran ini?',
                icon: dangerMode,
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'swal2-confirm btn btn-primary mx-2',
                    cancelButton: 'swal2-cancel btn btn-secondary mx-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#form-validate-' + id).submit();
                }
            });
        });

        // Update indikator required berdasarkan tombol yang diklik
        $('.btn-validate-action').on('mouseenter', function() {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var indicator = $('#required-indicator-' + id);
            var textarea = $('#validation_notes_modal-' + id);

            if (status === 'rejected') {
                indicator.show();
                textarea.attr('placeholder', 'Catatan wajib diisi ketika menolak laporan...');
            } else {
                indicator.hide();
                textarea.attr('placeholder', 'Berikan catatan jika diperlukan...');
            }
        });

        // Ganti tombol aksi edit validasi
        $('.btn-edit-validate-action').on('click', function(e) {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var notes = $('#edit_validation_notes_modal-' + id).val().trim();

            // Validasi dinamis: catatan wajib hanya jika status = rejected
            if (status === 'rejected' && !notes) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Validasi Wajib Diisi',
                    text: 'Catatan validasi wajib diisi ketika menolak laporan.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary mx-2' },
                    buttonsStyling: false
                });
                return;
            }

            var select = $('#edit_validation_status_modal-' + id);
            select.val(status); // Set value select sesuai tombol
            var actionText = status === 'approved' ? 'mengubah keputusan menjadi DISETUJUI' : 'mengubah keputusan menjadi DITOLAK';
            var confirmText = status === 'approved' ? 'Ya, Setujui!' : 'Ya, Tolak!';
            var dangerMode = status === 'approved' ? 'success' : 'warning';

            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: 'Anda yakin ingin ' + actionText + '?',
                icon: dangerMode,
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'swal2-confirm btn btn-primary mx-2',
                    cancelButton: 'swal2-cancel btn btn-secondary mx-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#form-edit-validate-' + id).submit();
                }
            });
        });

        // Update indikator required untuk edit validasi berdasarkan tombol yang diklik
        $('.btn-edit-validate-action').on('mouseenter', function() {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var indicator = $('#edit-required-indicator-' + id);
            var textarea = $('#edit_validation_notes_modal-' + id);

            if (status === 'rejected') {
                indicator.show();
                textarea.attr('placeholder', 'Catatan wajib diisi ketika menolak laporan...');
            } else {
                indicator.hide();
                textarea.attr('placeholder', 'Berikan catatan jika diperlukan...');
            }
        });

        // Inisialisasi status edit validasi saat modal dibuka
        $('[id^="editValidationModal-"]').on('show.bs.modal', function() {
            var modalId = $(this).attr('id');
            var violationId = modalId.replace('editValidationModal-', '');
            var currentStatus = $('#edit_validation_status_modal-' + violationId).val();

            // Update visual indicator berdasarkan status saat ini
            var indicator = $('#edit-required-indicator-' + violationId);
            var textarea = $('#edit_validation_notes_modal-' + violationId);

            if (currentStatus === 'rejected') {
                indicator.show();
                textarea.attr('placeholder', 'Catatan wajib diisi ketika menolak laporan...');
            } else {
                indicator.hide();
                textarea.attr('placeholder', 'Berikan catatan jika diperlukan...');
            }
        });

        // Tampilkan alert sukses jika ada session flash message
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                customClass: { container: 'swal2-success' }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary mx-2' },
                buttonsStyling: false
            });
        @endif

        // Auto buka modal edit jika ada session flag
        @if(session('open_edit_modal'))
            // Delay sedikit untuk memastikan DOM sudah ready
            setTimeout(function() {
                $('[id^="editValidationModal-"]').modal('show');
            }, 500);
        @endif

        // Handle modal bukti pelanggaran
        $('[id^="evidenceModal-"]').on('show.bs.modal', function() {
            var modal = $(this);
            var img = modal.find('img');

            // Show loading state
            modal.find('.evidence-container').append('<div class="loading-overlay text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Memuat gambar...</div>');

            // Hide loading when image loaded
            img.on('load', function() {
                modal.find('.loading-overlay').fadeOut(300, function() {
                    $(this).remove();
                });
                $(this).css('opacity', '1');
            });
        });

        // Handle image click untuk zoom
        $('[id^="evidenceModal-"] img').on('click', function() {
            var src = $(this).attr('src');
            var alt = $(this).attr('alt');

            // Create zoom modal
            var zoomModal = `
                <div class="modal fade" id="zoomModal" tabindex="-1" role="dialog" style="z-index: 1060;">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content" style="background: rgba(0,0,0,0.9);">
                            <div class="modal-header border-0">
                                <h5 class="modal-title text-white">
                                    <i class="fas fa-search-plus"></i> ${alt}
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center p-0">
                                <img src="${src}" class="img-fluid" style="max-height: 90vh; width: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing zoom modal
            $('#zoomModal').remove();

            // Add new zoom modal
            $('body').append(zoomModal);
            $('#zoomModal').modal('show');

            // Remove modal when closed
            $('#zoomModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        });
    });
</script>
@endpush
