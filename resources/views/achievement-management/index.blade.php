@extends('layouts.app')

@section('title')
    Kelola Prestasi
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Kelola Prestasi</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Data Prestasi</h3>
                            <div class="card-tools">
                                <a href="{{ route('achievement-management.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i> Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-tertiary text-white">
                                        <tr>
                                            <th>No</th>
                                            <th>Jenis Prestasi</th>
                                            <th>Kategori Prestasi</th>
                                            <th>Poin</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($achievements as $index => $item)
                                            <tr>
                                                <td>{{ $index + $achievements->firstItem() }}</td>
                                                <td>{{ $item->jenis_prestasi }}</td>
                                                <td>{{ $item->kategori_prestasi }}</td>
                                                <td>{{ $item->poin }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-outline-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="{{ route('achievement-management.edit', $item->id) }}">Edit</a>
                                                            <form action="{{ route('achievement-management.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-3">
                                    {{ $achievements->links() }}
                                </div>
                            </div> {{-- table-responsive --}}
                        </div> {{-- card-body --}}
                    </div> {{-- card --}}
                </div>
            </div>
        </div>
    </div>
@endsection
