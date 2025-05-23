@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah Jam</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('manage-hours.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="session_type">Tipe Jam</label>
            <select name="session_type" class="form-control" required>
                <option value="">Pilih</option>
                <option value="Jam pelajaran">Jam Pelajaran</option>
                <option value="Jam istirahat">Jam Istirahat</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="slot_number">Jam ke-</label>
            <input type="number" name="slot_number" class="form-control" min="1" required>
        </div>
        <div class="mb-3">
            <label for="start_time">Jam Mulai</label>
            <input type="time" name="start_time" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="end_time">Jam Selesai</label>
            <input type="time" name="end_time" class="form-control" required>
        </div>
        <button class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
