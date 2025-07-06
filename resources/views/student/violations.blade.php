@extends('layouts.app')
@section('title', 'Poin Pelanggaran')
@section('content')
<div class="container-fluid py-4 px-5">
    <h3 class="fw-bold mb-3">POIN PELANGGARAN</h3>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <label for="date">Tanggal</label>
            <form method="get" class="d-inline">
                <input type="date" name="date" id="date" value="{{ $date }}">
                <button type="submit" class="btn btn-primary btn-sm">Pilih</button>
            </form>
        </div>
        <div class="fw-bold text-white bg-tertiary px-3 py-2 rounded">TOTAL POIN : {{ $totalPoint }}</div>
    </div>
    <div class="bg-light p-4 rounded shadow-sm">
        <h5 class="fw-bold mb-2">REKAP DATA PELANGGARAN</h5>
        <div class="mb-2">{{ $student->name }}</div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-tertiary text-white">
                    <tr>
                        <th>No</th>
                        <th>TANGGAL PELAPORAN</th>
                        <th>PELANGGARAN</th>
                        <th>JENIS PELANGGARAN</th>
                        <th>BUKTI</th>
                        <th>POIN</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($violations as $i => $v)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $v->violation_date ? $v->violation_date->format('d/m/Y') : '-' }}</td>
                        <td>{{ optional($v->violationPoint)->violation_type ?? '-' }}</td>
                        <td>{{ optional($v->violationPoint)->violation_level ?? '-' }}</td>
                        <td>
                            @if($v->evidence)
                                <a href="{{ asset('storage/'.$v->evidence) }}" class="btn btn-success btn-sm" download>Download</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ optional($v->violationPoint)->points ?? 0 }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
