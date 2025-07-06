@extends('layouts.app')

@section('title', 'Detail Siswa')

@push('css')
<style>
    .profile-header {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .profile-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        object-fit: cover;
    }
    .student-name {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .student-info {
        color: #6c757d;
        font-size: 16px;
    }
    .point-card {
        text-align: center;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .point-value {
        font-size: 28px;
        font-weight: 600;
    }
    .achievement-card {
        background: #e8f5e9;
    }
    .violation-card {
        background: #ffebee;
    }
    .balance-card {
        background: #e3f2fd;
    }
    .tab-content {
        padding: 20px;
        background: white;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .badge-achievement {
        background-color: #4caf50;
    }
    .badge-violation {
        background-color: #f44336;
    }
    .achievement-item, .violation-item {
        border-left: 4px solid;
        padding: 10px 15px;
        margin-bottom: 15px;
        border-radius: 4px;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .achievement-item {
        border-left-color: #4caf50;
    }
    .violation-item {
        border-left-color: #f44336;
    }
    .item-title {
        font-weight: 600;
        margin-bottom: 4px;
    }
    .item-date, .item-teacher {
        font-size: 14px;
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        font-weight: 600;
        border-bottom: 3px solid #007bff;
    }
    .pagination {
        margin-bottom: 0;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Detail Siswa</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('achievements.all_students') }}">Daftar Prestasi Siswa</a></li>
                    <li class="breadcrumb-item active">Detail Siswa</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="row">
                <div class="col-md-2 text-center mb-3">
                    @if ($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" class="profile-img" alt="Foto Siswa">
                    @else
                        <img src="{{ asset('assets/img/default-avatar.png') }}" class="profile-img" alt="Default Avatar">
                    @endif
                </div>
                <div class="col-md-5">
                    <div class="student-name">{{ $student->name }}</div>
                    <div class="student-info">
                        <p class="mb-1"><i class="fas fa-id-card mr-2"></i>NISN: {{ $student->nisn }}</p>
                        <p class="mb-1"><i class="fas fa-school mr-2"></i>Kelas: {{ $student->class_name }}</p>
                        <p class="mb-1"><i class="fas fa-phone mr-2"></i>{{ $student->phone ?? 'N/A' }}</p>
                        <p class="mb-1"><i class="fas fa-envelope mr-2"></i>{{ $student->email }}</p>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="point-card achievement-card">
                                <div class="point-value text-success">{{ $totalAchievementPoints }}</div>
                                <div>Poin Prestasi</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="point-card violation-card">
                                <div class="point-value text-danger">{{ $totalViolationPoints }}</div>
                                <div>Poin Pelanggaran</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="point-card balance-card">
                                <div class="point-value text-primary">{{ $totalAchievementPoints - $totalViolationPoints }}</div>
                                <div>Poin Total</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="studentTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="achievement-tab" data-toggle="tab" href="#achievement" role="tab" aria-controls="achievement" aria-selected="true">
                    <i class="fas fa-trophy mr-1"></i> Prestasi <span class="badge badge-pill badge-achievement">{{ $achievements->total() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="violation-tab" data-toggle="tab" href="#violation" role="tab" aria-controls="violation" aria-selected="false">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Pelanggaran <span class="badge badge-pill badge-violation">{{ $violations->total() }}</span>
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="studentTabContent">
            <!-- Achievement Tab -->
            <div class="tab-pane fade show active" id="achievement" role="tabpanel" aria-labelledby="achievement-tab">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Riwayat Prestasi</h5>
                        @if($achievements->count() > 0)
                            @foreach($achievements as $achievement)
                                <div class="achievement-item">
                                    <div class="item-title">
                                        {{ $achievement->achievements_name }}
                                        <span class="float-right badge badge-success">+{{ $achievement->achievementPoint->points }} poin</span>
                                    </div>
                                    <div>{{ $achievement->description }}</div>
                                    <div class="item-date">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        {{ \Carbon\Carbon::parse($achievement->achievement_date)->format('d M Y') }}
                                    </div>
                                    <div class="item-teacher">
                                        <i class="fas fa-user-tie mr-1"></i>
                                        Dilaporkan oleh: {{ $achievement->teacher->name }}
                                    </div>
                                </div>
                            @endforeach
                            <div class="d-flex justify-content-center">
                                {{ $achievements->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">
                                Belum ada prestasi yang tercatat untuk siswa ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Violation Tab -->
            <div class="tab-pane fade" id="violation" role="tabpanel" aria-labelledby="violation-tab">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Riwayat Pelanggaran</h5>
                        @if($violations->count() > 0)
                            @foreach($violations as $violation)
                                <div class="violation-item">
                                    <div class="item-title">
                                        {{ $violation->violationPoint->violation_type }}
                                        <span class="float-right badge badge-danger">{{ $violation->violationPoint->points }} poin</span>
                                    </div>
                                    <div>{{ $violation->description }}</div>
                                    <div class="item-date">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        {{ \Carbon\Carbon::parse($violation->violation_date)->format('d M Y') }}
                                    </div>
                                    <div class="item-teacher">
                                        <i class="fas fa-user-tie mr-1"></i>
                                        Dilaporkan oleh: {{ $violation->teacher->name }}
                                    </div>
                                </div>
                            @endforeach
                            <div class="d-flex justify-content-center">
                                {{ $violations->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">
                                Belum ada pelanggaran yang tercatat untuk siswa ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('achievements.all_students') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</section>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Activate current tab based on URL hash
        var hash = window.location.hash;
        if (hash) {
            $('.nav-tabs a[href="' + hash + '"]').tab('show');
        }

        // Change URL hash when tab changes
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        });
    });
</script>
@endpush
