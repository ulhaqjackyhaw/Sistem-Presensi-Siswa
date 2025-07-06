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
    </style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0 text-uppercase">Detail Laporan Pelanggaran</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li> {{-- Sesuaikan route 'home' --}}
                    <li class="breadcrumb-item"><a href="{{ route('violations.index') }}">Laporan Pelanggaran</a></li>
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
                            <a href="{{ route('violations.index') }}" class="btn btn-sm btn-outline-secondary">
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
                                        <td>: <a href="{{ Storage::url($violation->evidence) }}" target="_blank" class="btn btn-info btn-xs">
                                               <i class="fas fa-search-plus"></i> Lihat Bukti
                                            </a>
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

                                    @can('validate_violation_report') {{-- Sesuaikan permission --}}
                                        @if($violation->validation_status === 'pending')
                                            <button class="btn btn-success btn-sm mb-1" data-toggle="modal" data-target="#validateModalShow-{{ $violation->id }}">
                                                <i class="fas fa-check-circle"></i> Validasi Laporan
                                            </button>
                                        @endif
                                    @endcan

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
@can('validate_violation_report') {{-- Hanya tampilkan modal jika user berhak validasi --}}
@if($violation->validation_status === 'pending')
<div class="modal fade" id="validateModalShow-{{ $violation->id }}" tabindex="-1" role="dialog" aria-labelledby="validateModalLabelShow-{{ $violation->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('violations.validate', $violation->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validateModalLabelShow-{{ $violation->id }}">Validasi Laporan Pelanggaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda akan memvalidasi laporan untuk siswa: <strong>{{ $violation->student ? $violation->student->name : 'N/A' }}</strong></p>
                    <p>Pelanggaran: <strong>{{ $violation->violationPoint ? $violation->violationPoint->violation_type : 'N/A' }}</strong></p>
                    <hr>
                    <div class="form-group">
                        <label for="validation_status_modal-{{ $violation->id }}">Status Validasi <span class="text-danger">*</span></label>
                        <select name="validation_status" id="validation_status_modal-{{ $violation->id }}" class="form-control" required>
                            <option value="approved">Setujui Laporan</option>
                            <option value="rejected">Tolak Laporan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="validation_notes_modal-{{ $violation->id }}">Catatan Validasi (Opsional)</label>
                        <textarea name="validation_notes" id="validation_notes_modal-{{ $violation->id }}" class="form-control" rows="3" placeholder="Berikan catatan jika diperlukan...">{{ old('validation_notes') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Validasi</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endcan

@endsection

@push('js')
{{-- Jika Anda memerlukan JavaScript khusus untuk halaman ini --}}
<script>
    $(document).ready(function() {
        // Script untuk menangani konfirmasi hapus jika diperlukan (meskipun sudah ada onclick confirm)
        // $('.delete-violation-btn').on('click', function(e) {
        //     e.preventDefault();
        //     var form = $(this).closest('form');
        //     Swal.fire({
        //         title: 'Apakah Anda yakin?',
        //         text: "Laporan pelanggaran ini akan dihapus secara permanen!",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         cancelButtonColor: '#6c757d',
        //         confirmButtonText: 'Ya, hapus!',
        //         cancelButtonText: 'Batal'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             form.submit();
        //         }
        //     });
        // });
    });
</script>
@endpush
