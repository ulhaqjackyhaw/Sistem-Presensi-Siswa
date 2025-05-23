@extends('layouts.app')

@section('title', 'Edit Jam')

@section('content')
    <div class="container">
        <h2>Edit Jam</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('manage-hours.update', $hour->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="session_type">Tipe Jam</label>
                <select name="session_type" class="form-control" required>
                    <option value="Jam pelajaran" {{ $hour->session_type == 'Jam pelajaran' ? 'selected' : '' }}>Jam
                        Pelajaran</option>
                    <option value="Jam istirahat" {{ $hour->session_type == 'Jam istirahat' ? 'selected' : '' }}>Jam
                        Istirahat</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="slot_number">Jam ke-</label>
                <input type="number" name="slot_number" class="form-control" value="{{ $hour->slot_number }}"
                    min="1" required>
            </div>
            <div class="mb-3">
                <label for="start_time">Jam Mulai</label>
                <input type="time" name="start_time" class="form-control" value="{{ $hour->start_time }}" required>
            </div>
            <div class="mb-3">
                <label for="end_time">Jam Selesai</label>
                <input type="time" name="end_time" class="form-control" value="{{ $hour->end_time }}" required>
            </div>
            <button class="btn btn-primary">Perbarui</button>
        </form>
    </div>
@endsection