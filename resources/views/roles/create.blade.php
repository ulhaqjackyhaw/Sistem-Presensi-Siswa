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
            <div class="col-sm-6">
                <h4 class="m-0 page-title">Tambah Role</h4> 
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
                        <h5 class="m-0"></h5>
                        <div class="card-tools">
                            <a href="{{ route('manage-role.index') }}" class="btn btn-tool btn-icon-blue">
                                <i class="fas fa-arrow-alt-circle-left"></i>
                            </a>
                        </div>
                    </div>
                    <form action="{{ route('manage-role.store') }}" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Role Akses</label>
                                <input type="text" name="name"
                                    class="form-control @error('name')is-invalid @enderror"
                                    placeholder="Nama Role Akses">
                                @error('name')
                                <div class="invalid-feedback" role="alert">
                                    <span>{{ $message }}</span>
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Roles Menu Permission</label>
                                <ul class="checktree">
                                    @foreach ($menus as $menu)
                                        @if (count($menu->submenus) == '0')
                                            <li>
                                                <input type="checkbox" name="menu_id[]" value="{{ $menu->id }}"> <b>{{ $menu->nama_menu }}</b>
                                                @if (count($menu->permissions) > 0)
                                                    <ul>
                                                        @foreach ($menu->permissions as $permission)
                                                            <li>
                                                                <input type="checkbox" name="permission_id[]" value="{{ $permission->name }}">
                                                                {!! $permission->detail . '<i>( ' . $permission->name . ' )</i>' !!}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @else
                                            <li>
                                                <input type="checkbox" name="menu_id[]" value="{{ $menu->id }}"> <b>{{ $menu->nama_menu }}</b>
                                                <ul>
                                                    @foreach ($menu->submenus as $submenu)
                                                        @if (count($submenu->submenus) == 0)
                                                            <li>
                                                                <input type="checkbox" name="menu_id[]" value="{{ $submenu->id }}"> <b>{{ ucwords($submenu->nama_menu) }}</b>
                                                                @if (count($submenu->permissions) > 0)
                                                                    <ul>
                                                                        @foreach ($submenu->permissions as $permission)
                                                                            <li>
                                                                                <input type="checkbox" name="permission_id[]" value="{{ $permission->name }}">
                                                                                {!! $permission->detail . '<i>( ' . $permission->name . ')</i>' !!}
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </li>
                                                        @else
                                                            <li>
                                                                <input type="checkbox" name="menu_id[]" value="{{ $submenu->id }}"> <b>{{ $submenu->nama_menu }}</b>
                                                                <ul>
                                                                    @foreach ($submenu->submenus as $submenu2)
                                                                        @if (count($submenu2->submenus) == 0)
                                                                            <li>
                                                                                <input type="checkbox" name="menu_id[]" value="{{ $submenu2->id }}"> <b>{{ $submenu2->nama_menu }}</b>
                                                                                @if (count($submenu2->permissions) > 0)
                                                                                    <ul>
                                                                                        @foreach ($submenu2->permissions as $permission)
                                                                                            <li>
                                                                                                <input type="checkbox" name="permission_id[]" value="{{ $permission->name }}">
                                                                                                {!! $permission->detail . '<i>(' . $permission->name . ' )</i>' !!}
                                                                                            </li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                @endif
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-custom-blue btn-block btn-flat">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
@endpush
