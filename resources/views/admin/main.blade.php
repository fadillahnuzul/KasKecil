
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{$title}}</title>

    <!-- Custom fonts for this template -->
    <link href="{{asset('style/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{asset('style/css/sb-admin-2.min.css')}}" rel="stylesheet">
    
    <!-- Custom styles for this page -->
    <link href="{{asset('style/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Kas Kecil</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="/home_admin">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/pengajuan">
                    <i class="fas fa-fw fa-file"></i>
                    <span>Buat Pengajuan</span></a>
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

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>

                    <!-- Topbar Search -->
                    <!-- <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form> -->

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{Auth::user()->username}}</span>
                                <img class="img-profile rounded-circle"
                                    src="{{asset('style/img/undraw_profile.svg')}}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="/logout" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-2 text-gray-800">{{$title}}</h1>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            @if ($laporan == FALSE)
                                            <div class="text-s font-weight-bold text-success text-uppercase mb-1">
                                                Saldo</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($Saldo->saldo)}}</div>
                                            @endif
                                            @if ($laporan == TRUE)
                                            <div class="text-s font-weight-bold text-success text-uppercase mb-1">
                                                Total Pengajuan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($total_pengajuan)}}</div>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Card Saldo -->
                        
                        <!-- Card Tunai -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            @if ($laporan == FALSE)
                                            <div class="text-s font-weight-bold text-warning text-uppercase mb-1">
                                                Tunai</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($Saldo->tunai)}}</div>
                                            
                                            @elseif ($laporan == TRUE)
                                            <div class="text-s font-weight-bold text-warning text-uppercase mb-1">
                                                Total Dipakai</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($total_pengeluaran)}}</div>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Card Tunai -->
                        <!-- Card Bank -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            @if ($laporan == FALSE)
                                            <div class="text-s font-weight-bold text-info text-uppercase mb-1">
                                                Bank</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($Saldo->bank)}}</div>
                                            @elseif ($laporan == TRUE)
                                            <div class="text-s font-weight-bold text-info text-uppercase mb-1">
                                                Sisa</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($sisa)}}</div>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Card Bank -->
                    
                    
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        <!-- Dropdown Divisi -->
                        <div class="dropdown" style="float:right;">
                            <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Pilih Divisi
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="/home_admin">All</a>
                            @foreach ($divisi as $divisi)
                                <a class="dropdown-item" href="/kas_divisi/{{$divisi->id}}">{{$divisi->name}}</a>
                            @endforeach
                            </div>
                        </div>
                        <!-- End Dropdown Divisi -->
                        @if ($laporan == TRUE)
                            <a href="/pengajuan.export" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" style="float:right; margin-right:5px"><i
                                class="fas fa-download fa-sm text-white-50"></i> Cetak</a>
                        @endif
                        <div class="container">
                            <div class="row">
                                @if ($laporan == FALSE)
                                <form action="/filter_pengajuan/1" method="POST">
                                @elseif ($laporan == TRUE)
                                <form action="/filter_pengajuan/2" method="POST">
                                @endif
                                @csrf
                                <div class="container-fluid">
                                    <div class="form-group row">
                                        <label for="date" class="col-form-label col-sm">Tanggal awal</label>
                                        <div class="col-sm">
                                            <input type="date" class="form-control input-sm" id="startDate" value={{$startDate}} name="startDate">
                                        </div>
                                        <label for="date" class="col-form-label col-sm">Tanggal akhir</label>
                                        <div class="col-sm">
                                            <input type="date" class="form-control input-sm" id="endDate" value={{$endDate}} name="endDate">
                                        </div>
                                        <div class="col-sm">
                                            <button type="submit" class="btn btn-sm btn-primary">Tampil</button>
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                            
                        @include('sweetalert::alert')
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Tanggal</th>
                                            <th>Divisi</th>
                                            <th>Keterangan</th>
                                            <th>Pengajuan</th>
                                            <th>Sumber Dana</th>
                                            <th>Total Belanja</th>
                                            <th>Sisa</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php $no=1;?>
                                        @foreach ($dataKas as $row)
                                        <tr>
                                            <td>{{$row->kode}}</td>
                                            <td>{{$row->tanggal}}</td>
                                            <td>{{$row->Divisi->name}}</td>
                                            <td>{{$row->deskripsi}}</td>
                                            <td>Rp. {{number_format($row->jumlah)}}</td>                           
                                            <td>@if ($row->sumber == NULL) 
                                                -
                                                @endif
                                                @if ($row->sumber != NULL)
                                                    {{$row->Sumber->sumber_dana}}
                                                @endif
                                            </td>
                                            <td>Rp. {{number_format($row->total_belanja)}}</td>
                                            <td>Rp. {{number_format($row->sisa)}}</td>
                                            <td>{{$row->Status->nama_status}}</td>
                                            <td>
                                            @if ($row->Status->id != 6)
                                            <a onclick="return confirm ('Apakah yakin untuk menghapus?')" href="/hapus_admin/1/{{$row->id}}" class="btn btn-danger btn-sm">Hapus</a>
                                            <a href="/edit_admin/{{$row->id}}" class="btn btn-info btn-sm">Edit</a>
                                            @if ($row->Status->id == 1)
                                                <a href="/acc/{{$row->id}}" class="btn btn-success btn-sm">
                                                Approve</a> 
                                                <a onclick="return confirm ('Apakah yakin untuk menolak?')" href="/tolak/{{$row->id}}" class="btn btn-warning btn-sm">
                                                Decline</a>
                                            @elseif ($row->Status->id == 2 OR $row->Status->id == 4 OR $row->Status->id == 5)
                                                <a href="/detail_divisi/{{$row->id}}" class="btn btn-primary btn-sm">Detail</a> 
                                            @endif
                                            @endif
                                            </td>
                                        </tr>
                                        <?php $no++ ;?>
                                        @endforeach 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Abdael Nusa 2022</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="/logout">Logout</a>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('style/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('style/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{asset('style/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{asset('style/js/sb-admin-2.min.js')}}"></script>

    <!-- Page level plugins -->
    <script src="{{asset('style/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('style/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

    <!-- Page level custom scripts -->
    <script src="{{asset('style/js/demo/datatables-demo.js')}}"></script>

</body>

</html>