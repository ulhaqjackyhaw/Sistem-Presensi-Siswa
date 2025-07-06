@extends('layouts.app')

@section('title')
    Laporan Pelanggaran
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Laporan Pelanggaran Siswa</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{-- <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li> --}}
                        {{-- <li class="breadcrumb-item active">Laporan Pelanggaran</li> --}}
                        {{-- Tambahkan breadcrumb jika diperlukan --}}
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    {{-- Card theme changed to primary --}}
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Data Laporan Pelanggaran</h3>
                            <div class="card-tools">
                                <a href="{{ route('violations.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i> Laporkan Pelanggaran
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable-violations" class="table table-bordered table-striped">
                                    {{-- Table header background changed to bg-tertiary --}}
                                    {{-- Ensure .bg-tertiary is defined in your CSS for the desired blue color --}}
                                    <thead class="bg-tertiary text-white">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>Jenis Pelanggaran</th>
                                            <th>Tanggal Pelanggaran</th>
                                            <th>Status Validasi</th>
                                            <th>Divalidasi Oleh</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($violations as $index => $violation)
                                            <tr>
                                                <td>{{ $index + $violations->firstItem() }}</td>
                                                <td>{{ $violation->student ? $violation->student->name : 'Siswa tidak ditemukan' }}</td>
                                                <td>{{ $violation->violationPoint ? $violation->violationPoint->violation_type : 'Jenis tidak ditemukan' }}</td>
                                                <td>{{ $violation->violation_date ? \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    @if($violation->validation_status === 'pending')
                                                        <span class="badge badge-warning">Menunggu Validasi</span>
                                                    @elseif($violation->validation_status === 'approved')
                                                        <span class="badge badge-success">Disetujui</span>
                                                    @elseif($violation->validation_status === 'rejected')
                                                        <span class="badge badge-danger">Ditolak</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $violation->validation_status ?? 'N/A' }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $violation->validator ? $violation->validator->name : '-' }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="{{ route('violations.show', $violation->id) }}">
                                                                <i class="fas fa-eye text-info"></i> Detail
                                                            </a>
                                                            @if($violation->validation_status === 'pending' || Auth::user()->can('edit_validated_violation')) {{-- Ganti dengan permission yang sesuai --}}
                                                                <a class="dropdown-item" href="{{ route('violations.edit', $violation->id) }}">
                                                                    <i class="fas fa-edit text-warning"></i> Edit
                                                                </a>
                                                            @endif
                                                            @if($violation->validation_status === 'pending' || Auth::user()->can('delete_validated_violation')) {{-- Ganti dengan permission yang sesuai --}}
                                                                <button type="button" class="dropdown-item text-danger"
                                                                        onclick="confirmDelete({{ $violation->id }})">
                                                                    <i class="fas fa-trash"></i> Hapus
                                                                </button>
                                                                <form id="delete-form-{{ $violation->id }}"
                                                                      action="{{ route('violations.destroy', $violation->id) }}"
                                                                      method="POST" style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            @endif
                                                            @can('validate_violation_report') {{-- Sesuaikan dengan nama ability/permission Anda --}}
                                                                @if($violation->validation_status === 'pending')
                                                                    <button class="dropdown-item text-success" data-toggle="modal" data-target="#validateModal-{{ $violation->id }}">
                                                                        <i class="fas fa-check-circle"></i> Validasi
                                                                    </button>
                                                                @endif
                                                            @endcan
                                                        </div>
                                                    </div>

                                                    @can('validate_violation_report')
                                                    <div class="modal fade" id="validateModal-{{ $violation->id }}" tabindex="-1" role="dialog" aria-labelledby="validateModalLabel-{{ $violation->id }}" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <form action="{{ route('violations.validate', $violation->id) }}" method="POST"> {{-- Pastikan route ini ada --}}
                                                                @csrf
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="validateModalLabel-{{ $violation->id }}">Validasi Laporan Pelanggaran</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p><strong>Siswa:</strong> {{ $violation->student ? $violation->student->name : '-' }}</p>
                                                                        <p><strong>Pelanggaran:</strong> {{ $violation->violationPoint ? $violation->violationPoint->violation_type : '-' }}</p>
                                                                        <p><strong>Tanggal:</strong> {{ $violation->violation_date ? \Carbon\Carbon::parse($violation->violation_date)->format('d F Y') : '-' }}</p>
                                                                        <hr>
                                                                        <div class="form-group">
                                                                            <label for="validation_status-{{ $violation->id }}">Status Validasi <span class="text-danger">*</span></label>
                                                                            <select name="validation_status" id="validation_status-{{ $violation->id }}" class="form-control" required>
                                                                                <option value="approved" {{ $violation->validation_status == 'approved' ? 'selected' : '' }}>Setujui</option>
                                                                                <option value="rejected" {{ $violation->validation_status == 'rejected' ? 'selected' : '' }}>Tolak</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="validation_notes-{{ $violation->id }}">Catatan Validasi (Opsional)</label>
                                                                            <textarea name="validation_notes" id="validation_notes-{{ $violation->id }}" class="form-control" rows="3">{{ $violation->validation_notes }}</textarea>
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
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    Belum ada laporan pelanggaran yang dibuat.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div> {{-- ./table-responsive --}}
                            <div class="mt-3">
                                {{ $violations->links() }}
                            </div>
                        </div> {{-- ./card-body --}}
                    </div> {{-- ./card --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            $('#datatable-violations').DataTable({
                responsive: true,
                autoWidth: false,
                lengthChange: true,
                pageLength: 10,
                language: {
                    lengthMenu: "Tampilkan _MENU_ entri",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Tidak ada entri yang ditampilkan",
                    infoFiltered: "(disaring dari _MAX_ total entri)",
                    search: "Cari:",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });
        });

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Laporan pelanggaran ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
@endpush
