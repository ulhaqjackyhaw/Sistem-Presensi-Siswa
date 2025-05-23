@extends('layouts.app')

@push('css')
<style>
    body {
        font-family: 'Roboto', sans-serif !important;
    }

    h4.page-title {
        color: #183C70;
        font-weight: bold;
        font-size: 2rem;
        font-family: 'Roboto', sans-serif;
    }

    .btn-custom-blue {
        background-color: #1777E5 !important;
        color: white !important;
        border: none !important;
    }

    .btn-custom-blue:hover {
        background-color: #183C70 !important;
        color: white !important;
    }

    .btn-icon-blue {
        color: #1777E5 !important;
        font-size: 1.5rem;
    }

    .btn-icon-blue:hover {
        color: #183C70 !important;
    }
</style>
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div>
                    <h4 class="m-0 page-title">Tambah Pengguna</h4>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0"></h5>
                            <div class="card-tools">
                                <a href="{{ route('manage-user.index') }}" class="btn btn-tool btn-icon-blue">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>
                        <form action="{{ route('manage-user.store') }}" method="post">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name')is-invalid @enderror" placeholder="Nama Lengkap">
                                    @error('name')
                                        <div class="invalid-feedback" role="alert">
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Alamat Email</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email')is-invalid @enderror"
                                        placeholder="Alamat Email">
                                    @error('email')
                                        <div class="invalid-feedback" role="alert">
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <label>Role Pengguna</label>
                                    @foreach ($roles as $item)
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="role[]" class="custom-control-input"
                                                id="{{ $item->name . $item->id }}" value="{{ strtolower($item->name) }}">
                                            <label class="custom-control-label"
                                                for="{{ $item->name . $item->id }}">{{ strtoupper($item->name) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-group">
                                    <label>Verified</label>
                                    <div class="input-group">
                                        <input type="checkbox" name="verified" data-bootstrap-switch data-off-color="danger"
                                            data-on-color="success">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-custom-blue btn-block btn-flat"><i class="fa fa-save"></i>
                                    Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('') }}plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script>
        $(function() {
            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            })
        })
    </script>
@endpush
