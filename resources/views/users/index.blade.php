@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col">
                <h4 class="m-0" style="color: #183C70; font-weight: bold; font-size: 2rem; font-family: 'Roboto', sans-serif;">Manajemen Pengguna</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right"></ol>
            </div>
        </div>
    </div>
</div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title" style="font-weight: 600; font-size: 1.25rem; color: #183C70; font-family: 'Roboto', sans-serif;">Data Pengguna</h3>
                            <a href="{{ route('manage-user.create') }}" class="btn ml-auto" class="btn" style="background-color: #1777e5; color: white">
                                <i class="fas fa-user-plus mr-1"></i> Tambah Pengguna
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label style="color: #1777e5; font-weight: normal;">Show
                                        <select class="form-select d-inline-block w-auto mx-2" style="color: #1777e5">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50" selected>50</option>
                                            <option value="100">100</option>
                                        </select>
                                        entries
                                    </label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <label for="search" class="me-3 mb-0" style="margin-right: 10px !important; color: #979797; font-weight: normal;">Search : </label>
                                    <input id="search" type="search" class="form-control d-inline-block" aria-label="Search" style="width: 180px; color: #979797; border: 1px solid #979797;">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-center">
                                    <thead style="background-color: #1777e5; color: white">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pengguna</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->email }}</td>
                                                <td>
                                                    @foreach ($item->roles->pluck('name') as $role)
                                                        {{ $role }}
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-outline-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="{{ route('manage-user.edit', $item->id) }}">Edit</a>
                                                            <a class="dropdown-item text-danger" href="#">Hapus</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between mt-3" style="color: #1777e5">
                                <div>
                                    Showing 1 to {{ count($users) }} of {{ count($users) }} entries
                                </div>
                                <div>
                                    <nav>
                                        <ul class="pagination mb-0">
                                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <li class="page-item"><a class="page-link" href="#">10</a></li>
                                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
