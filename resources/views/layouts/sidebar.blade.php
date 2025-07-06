@php
    $user = Auth::user();
    $isHomeroomTeacher = false;
    if ($user && $user->teacher) {
        $isHomeroomTeacher = \App\Models\HomeroomAssignment::where('teacher_id', $user->teacher->nip)
            ->whereHas('academicYear', function($q) { $q->where('is_active', true); })
            ->exists();
    }
@endphp
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    @foreach (json_decode(MenuHelper::Menu()) as $menu)
        <li class="nav-header">{{ strtoupper($menu->nama_menu) }}</li>
        @foreach ($menu->submenus as $submenu)
            @php
                $isLaporPrestasi = strtolower($submenu->nama_menu) === 'lapor prestasi';
            @endphp
            @if ($isLaporPrestasi && !$isHomeroomTeacher)
                @continue
            @endif
            @if (count($submenu->submenus) == '0')
                @php
                    $isPoinPrestasi = strtolower($submenu->nama_menu) === 'poin prestasi';
                    $isPoinPelanggaran = strtolower($submenu->nama_menu) === 'poin pelanggaran';
                @endphp
                <li class="nav-item text-sm">
                    <a href="{{ $isPoinPrestasi ? route('student.achievements') : ($isPoinPelanggaran ? route('student.violations') : url($submenu->url)) }}"
                        class="nav-link {{ (Request::url() == route('student.achievements') && $isPoinPrestasi) || (Request::url() == route('student.violations') && $isPoinPelanggaran) || Request::segment(1) == $submenu->url ? 'active' : '' }}">
                        <i class="nav-icon {{ $submenu->icon }}"></i>
                        <p>
                            {{ ucwords($submenu->nama_menu) }}
                        </p>
                    </a>
                </li>
            @else
                @foreach ($submenu->submenus as $url)
                    @php
                        $urls[] = $url->url;
                        $isLaporPrestasiSub = strtolower($url->nama_menu) === 'lapor prestasi';
                    @endphp
                    @if ($isLaporPrestasiSub && !$isHomeroomTeacher)
                        @continue
                    @endif
                @endforeach
                <li class="nav-item text-sm {{ in_array(Request::segment(1), $urls) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ in_array(Request::segment(1), $urls) ? 'active' : '' }}">
                        <i class="nav-icon {{ $submenu->icon }}"></i>
                        <p>
                            {{ ucwords($submenu->nama_menu) }}
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @foreach ($submenu->submenus as $endmenu)
                            @php
                                $isPoinPrestasi = strtolower($endmenu->nama_menu) === 'poin prestasi';
                                $isPoinPelanggaran = strtolower($endmenu->nama_menu) === 'poin pelanggaran';
                                $isLaporPrestasiEnd = strtolower($endmenu->nama_menu) === 'lapor prestasi';
                            @endphp
                            @if ($isLaporPrestasiEnd && !$isHomeroomTeacher)
                                @continue
                            @endif
                            <li class="nav-item text-sm">
                                <a href="{{ $isPoinPrestasi ? route('student.achievements') : ($isPoinPelanggaran ? route('student.violations') : url($endmenu->url)) }}"
                                    class="nav-link {{ (Request::url() == route('student.achievements') && $isPoinPrestasi) || (Request::url() == route('student.violations') && $isPoinPelanggaran) || Request::segment(1) == $endmenu->url ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ ucwords($endmenu->nama_menu) }}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif
        @endforeach
    @endforeach
</ul>
