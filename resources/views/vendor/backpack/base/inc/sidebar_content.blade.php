{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('karyawan') }}"><i class="nav-icon la la-user"></i>Master Karyawan</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('shifts') }}"><i class="nav-icon las la-user-clock"></i>Master Shift</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('absensi') }}'><i class='nav-icon las la-calendar'></i> Absensi</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('kriteria') }}"><i class="nav-icon la la-list-ol"></i>Kriteria Penilaian</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('performance') }}'><i class='nav-icon las la-poll'></i> Penilaian Kinerja</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('performance-detail') }}'><i class='nav-icon la la-question'></i> Performance details</a></li>