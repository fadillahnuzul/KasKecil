<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text">Kas Kecil</div>
    </a>
    <hr class="sidebar-divider my-0">
    @if (Auth::user()->kk_access=='1')
    <li class="nav-item">
        <a class="nav-link" href="/home_admin">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/admin_kas_keluar">
            <i class="fas fa-fw fa-list"></i>
            <span>Kas Keluar</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/kas_keluar">
            <i class="fas fa-fw fa-list"></i>
            <span>Kas Keluar Admin</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/home">
            <i class="fas fa-fw fa-list"></i>
            <span>Pengajuan Admin</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/admin_laporan">
            <i class="fas fa-fw fa-book"></i>
            <span>Laporan Pengajuan</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/admin_laporan_kas_keluar">
            <i class="fas fa-fw fa-table"></i>
            <span>Laporan Kas Keluar</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/create_bkk">
            <i class="fas fa-fw fa-book"></i>
            <span>Buat BKK</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/list_bkk">
            <i class="fas fa-fw fa-book"></i>
            <span>List BKK</span></a>
    </li>
    @elseif (Auth::user()->kk_access=='2')
    <li class="nav-item">
        <a class="nav-link" href="{{url('/home')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/kas_keluar">
            <i class="fas fa-fw fa-list"></i>
            <span>Kas Keluar</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{url('/pengajuan')}}">
            <i class="fas fa-fw fa-file"></i>
            <span>Pengajuan</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/laporan">
            <i class="fas fa-fw fa-book"></i>
            <span>Laporan Pengajuan</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/laporan_kas_keluar">
            <i class="fas fa-fw fa-table"></i>
            <span>Laporan Kas Keluar</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/create_bkk">
            <i class="fas fa-fw fa-book"></i>
            <span>Buat BKK</span></a>
    </li>
    @elseif (Auth::user()->kk_access=='3')
    <li class="nav-item">
        <a class="nav-link" href="/home_bank">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/bank_laporan">
            <i class="fas fa-fw fa-book"></i>
            <span>Daftar Pengajuan</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/bank_laporan_kas_keluar">
            <i class="fas fa-fw fa-table"></i>
            <span>Daftar Kas Keluar</span></a>
    </li>
    @endif
    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>