@extends('layouts.app')

@section('title', 'Detail Prestasi')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Detail Prestasi</h4>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Prestasi</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <td>{{ $achievement->student->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Prestasi</th>
                                        <td>{{ $achievement->achievements_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Prestasi</th>
                                        <td>{{ $achievement->achievementPoint->achievement_type ?? '-' }} ({{ $achievement->achievementPoint->points ?? '-' }} poin)</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Prestasi</th>
                                        <td>{{ $achievement->achievement_date->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tahun Ajaran</th>
                                        <td>
                                            {{ $achievement->academicYear ? $achievement->academicYear->start_year . '/' . $achievement->academicYear->end_year . ' ' . ($achievement->academicYear->semester == 0 ? 'Genap' : 'Ganjil') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <td>{{ $achievement->description }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dilaporkan Oleh</th>
                                        <td>{{ $achievement->teacher->name }}</td>
                                    </tr>
                                    @if($achievement->evidence)
                                    <tr>
                                        <th>Bukti</th>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#evidenceModal-{{ $achievement->id }}">Lihat Bukti</button>
                                            <!-- Modal Bukti Prestasi -->
                                            <div class="modal fade" id="evidenceModal-{{ $achievement->id }}" tabindex="-1" role="dialog" aria-labelledby="evidenceModalLabel-{{ $achievement->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="evidenceModalLabel-{{ $achievement->id }}">Bukti Prestasi</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <div class="mb-3"></div>
                                                            <div class="evidence-container" style="max-height: 70vh; overflow: auto;">
                                                                <img src="{{ asset('storage/' . $achievement->evidence) }}" alt="Bukti Prestasi" class="img-fluid" style="max-height:60vh; opacity:0; transition:opacity 0.2s;" />
                                                            </div>
                                                            <div class="mt-3"></div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a href="{{ asset('storage/' . $achievement->evidence) }}" target="_blank" class="btn btn-primary">Buka di Tab Baru</a>
                                                            <a href="{{ asset('storage/' . $achievement->evidence) }}" download class="btn btn-success">Download</a>
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Status Validasi</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($achievement->validation_status === 'pending')
                                                <span class="badge badge-warning">Menunggu Validasi</span>
                                            @elseif($achievement->validation_status === 'approved')
                                                <span class="badge badge-success">Disetujui</span>
                                            @else
                                                <span class="badge badge-danger">Ditolak</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($achievement->validation_status !== 'pending')
                                    <tr>
                                        <th>Hasil Validasi</th>
                                        <td>
                                            @if($achievement->validation_status === 'approved')
                                                <span class="badge badge-success">Disetujui</span>
                                            @else
                                                <span class="badge badge-danger">Ditolak</span>
                                            @endif
                                            <br>
                                            <small>Oleh: {{ $achievement->validator->name ?? '-' }} pada {{ $achievement->validated_at ? $achievement->validated_at->format('d/m/Y H:i') : '-' }}</small>
                                            @if($achievement->validation_notes)
                                                <br><b>Catatan:</b> {{ $achievement->validation_notes }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if($achievement->validation_status === 'rejected')
                                    <tr>
                                        <th>Catatan Penolakan</th>
                                        <td>{{ $achievement->validation_notes }}</td>
                                    </tr>
                                    @endif
                                    @endif
                                </table>
                            </div>
                        </div>
                        <div class="mt-4 text-right">
                            <a href="{{ route('achievements.index') }}" class="btn btn-secondary">Kembali</a>
                            @if($achievement->validation_status === 'pending')
                                <a href="{{ route('achievements.edit', $achievement) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('achievements.destroy', $achievement) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus prestasi ini?')">
                                        Hapus
                                    </button>
                                </form>
                                @can('update_laporan_prestasi')
                                <!-- Tombol validasi hanya untuk Guru BK -->
                                <button class="btn btn-success" data-toggle="modal" data-target="#validateModalShow-{{ $achievement->id }}">Validasi</button>
                                <!-- Modal Validasi -->
                                <div class="modal fade" id="validateModalShow-{{ $achievement->id }}" tabindex="-1" role="dialog" aria-labelledby="validateModalLabelShow" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('achievements.validate', $achievement->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="validateModalLabelShow">Validasi Prestasi</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Status Validasi</label>
                                                        <select name="validation_status" class="form-control" required>
                                                            <option value="approved">Setujui</option>
                                                            <option value="rejected">Tolak</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Catatan (Opsional)</label>
                                                        <textarea name="validation_notes" class="form-control" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
$(document).ready(function() {
    // Handle modal bukti prestasi
    $('[id^="evidenceModal-"]').on('show.bs.modal', function() {
        var modal = $(this);
        var img = modal.find('img');
        // Show loading state
        modal.find('.evidence-container').append('<div class="loading-overlay text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Memuat gambar...</div>');
        // Hide loading when image loaded
        img.on('load', function() {
            modal.find('.loading-overlay').fadeOut(300, function() { $(this).remove(); });
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
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-body text-center p-0">
                            <img src="${src}" alt="${alt}" class="img-fluid" style="cursor:zoom-out; max-height:80vh;" />
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
        $('#zoomModal').on('hidden.bs.modal', function() { $(this).remove(); });
        // Close zoom on image click
        $('#zoomModal img').on('click', function() { $('#zoomModal').modal('hide'); });
    });
});
</script>
@endpush
@endsection
