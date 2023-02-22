<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Home</title>
    <!-- css table -->
    <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="{{asset('style/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

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
                <a class="nav-link" href="">
                    <i class="fas fa-fw fa-list"></i>
                    <span>Kas Keluar Admin</span></a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="/pengajuan">
                    <i class="fas fa-fw fa-file"></i>
                    <span>Pengajuan Dana</span></a>
            </li> -->
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
            @else
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
            @endif

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
                <div class="d-sm-flex align-items-center justify-content-between">
                        <h1 class="h3 mb-2 text-gray-800">{{$title}}</h1>
                </div>

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
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
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
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{Auth::user()->username}}</span>
                                <img class="img-profile rounded-circle" src="{{asset('style/img/undraw_profile.svg')}}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
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
                    <!-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-2 text-gray-800">{{$title}}</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Cetak Laporan</a>
                    </div> -->
                    <!-- Card Saldo -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-success text-uppercase mb-1">
                                                Saldo</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($saldo, 2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <!-- End Card Saldo -->
                         @if ($button_kas==TRUE)
                         <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-danger text-uppercase mb-1">
                                                Belum Diklaim</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($totalPengeluaran, 2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <!-- Card Tunai -->
                        <!-- <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-warning text-uppercase mb-1">
                                                Total Diklaim</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($totalDiklaim, 2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    @endif
                    </div>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        @if ($button_kas == TRUE)
                        <div class="row" style="margin-left:5px">
                            <!-- Dropdown Company -->
                            <div class="dropdown">
                                    <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Pilih Company
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="/kas_keluar">All</a>
                                        @foreach ($company as $company)
                                            <a class="dropdown-item" href="/kas_company/1/{{$company->project_company_id}}">{{$company->name}}</a>
                                        @endforeach
                                    </div>
                                </div>
                        <!-- End Dropdown Company -->
                            @if ($saldo != 0)
                                <a href="{{url('/kas')}}" class="btn btn-warning btn-sm btn-icon-split" style="margin-left:10px">
                                    <span class="text">Catat Kas</span>
                                </a>
                                <!-- <a data-toggle="modal" data-target="#KembalianSaldoModal" class="btn btn-success btn-sm btn-icon-split" style="margin-left:10px">
                                    <span class="text">Pengembalian Saldo</span>
                                </a> -->
                            @endif
                            @if ($companySelected)
                                <button type="button" class="btn btn-outline-primary" style="margin-left:10px" disabled>{{$companySelected->name}}</button>
                            @endif
                            </div>
                        @endif
                        @if ($button_kas==FALSE)
                            <!-- <a href="/pengeluaran.export" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" style="float:right; margin-right:5px"><i
                                class="fas fa-download fa-sm text-white-50"></i> Cetak</a> -->
                        <div class="container">
                            <div class="row">
                                <form action="" method="POST">
                                @csrf
                                <div class="container-fluid">
                                    <div class="form-group row">
                                        <label for="date" class="col-form-label col-sm"></label>
                                        <div class="col-sm">
                                            <input type="date" class="form-control input-sm" id="startDate" value={{$startDate}} name="startDate">
                                        </div>
                                        <label for="date" class="col-form-label col-sm"></label>
                                        <div class="col-sm">
                                            <input type="date" class="form-control input-sm" id="endDate" value={{$endDate}} name="endDate">
                                        </div>
                                        <div class="col-sm">
                                            <button type="submit" class="btn btn-sm btn-primary">Tampil</button>
                                        </div>
                                    </div>
                                </div>
                                </form>
                                <!-- Dropdown Company -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Pilih Company
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="/laporan_kas_keluar">All</a>
                                        @foreach ($company as $company)
                                            <a class="dropdown-item" href="/kas_company/2/{{$company->project_company_id}}">{{$company->name}}</a>
                                        @endforeach
                                    </div>
                                </div>
                        <!-- End Dropdown Company -->
                        <!-- Filter tanggal cetak -->
                        <!-- <form action="/pengeluaran.export" method="POST">
                                @csrf
                                <div class="container-fluid">
                                    <div class="form-group row">
                                        <label for="date" class="col-form-label col-sm"></label>
                                        <div class="col-sm">
                                            <input type="date" class="form-control input-sm" id="startDate" value={{$startDate}} name="startDate">
                                        </div>
                                        <label for="date" class="col-form-label col-sm"></label>
                                        <div class="col-sm">
                                            <input type="date" class="form-control input-sm" id="endDate" value={{$endDate}} name="endDate">
                                        </div>
                                        <div class="col-sm">
                                            <button type="submit" class="btn btn-sm btn-success">Cetak</button>
                                        </div>
                                    </div>
                                </div>
                                </form> -->
                                <!-- End Filter tanggal cetak -->
                                @if ($companySelected)
                                    <p style="margin-left:10px">{{$companySelected->name}}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="myTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            @if ($button_kas==TRUE)
                                            <th><input type="checkbox" id="head-cb"></th>
                                            @endif
                                            <th class="font-weight-bold text-dark">Tanggal</th>
                                            <th class="font-weight-bold text-dark">Keterangan</th>
                                            <th class="font-weight-bold text-dark">Kas Keluar</th>
                                            <th class="font-weight-bold text-dark">COA</th>
                                            <th class="font-weight-bold text-dark">Pembebanan</th>
                                            <th class="font-weight-bold text-dark">Nota Tujuan</th>
                                            <th class="font-weight-bold text-dark">PIC</th>
                                            <th class="font-weight-bold text-dark">Status</th>
                                            <th class="font-weight-bold text-dark">Tanggal Respon</th>
                                            <th class="font-weight-bold text-dark">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataKas as $row)
                                        <tr>
                                            @if ($button_kas==TRUE)
                                            <td><input type="checkbox"  class="cb-child" value="{{$row->id}}"></td>
                                            @endif
                                            <td class="font-weight-bold text-dark">{{Carbon\Carbon::parse($row->tanggal)->format('d-m-Y')}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->deskripsi}}</td>
                                            <td class="font-weight-bold text-dark">{{number_format($row->jumlah,2,",", ".")}}</td>
                                            <td class="font-weight-bold text-dark">@if ($row->coa)
                                                {{$row->COA->code}} {{$row->COA->name}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">@if ($row->pembebanan)
                                                {{$row->Pembebanan->name}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">@if ($row->tujuan)
                                                {{$row->tujuan}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">{{$row->pic}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->Status->nama_status}}</td>
                                            <td class="font-weight-bold text-dark">
                                            @if ($row->tanggal_respon)
                                                {{Carbon\Carbon::parse($row->tanggal_respon)->format('d-m-Y')}}
                                            @endif</td>
                                            <td class="font-weight-bold text-dark">
                                                @if ($row->status != 7 && $row->status != 6 && $row->status != 8)
                                                <a href="/edit_kas_keluar/{{$row->id}}" class="btn btn-primary btn-sm">
                                                    Edit</a>
                                                <a onclick="return confirm ('Apakah yakin untuk menghapus?')" href="/hapus_kas_keluar/{{$row->id}}" class="btn btn-danger btn-sm">
                                                    Hapus</a>
                                                <!-- <a onclick="set_modal_id({{$row->id}})" data-toggle="modal" data-target="#DoneModal" class="btn btn-success btn-sm" data-id="{{ $row->id }}">
                                                    Selesai</a> -->
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if ($button_kas==TRUE)
                                <button id="button-set-bkk" type="button" disabled onclick="done()" class="btn btn-sm btn-success">Selesai</button>
                                @endif
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
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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

    <!-- Done Modal -->
    <div class="modal fade" id="DoneModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Inputkan tanggal penyerahan nota</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/kas_selesai" method="POST">
                        @csrf
                        <input type="hidden" name="modal_id" id="modal_id">
                        <div class="form-group">
                            <label for="tanggal">Tanggal Serah Nota :</label>
                            <input type="date" class="form-control" placeholder="Tanggal Penyerahan Nota" id="tanggal" name="tanggal" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if ($saldo != 0 && $button_kas == TRUE)
    <!-- Kembalikan Saldo Modal -->
    <div class="modal fade" id="KembalianSaldoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pengembalian Sisa Saldo Pengajuan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/pengembalian_saldo" method="GET">
                        @csrf
                        <input type="hidden" name="id" id="id" value="">
                        <div class="form-group">
                            <label for="tanggal">Tanggal Pengembalian :</label>
                            <input type="date" class="form-control" placeholder="Input tanggal pengembalian sisa saldo" id="tanggal" name="tanggal" required>
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah Pengembalian Saldo</label>
                            <input type="text" class="form-control" placeholder="Input jumlah pengembalian saldo" id="jumlah" name="jumlah" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    <script>
        function set_modal_id(id) {
            document.getElementById("modal_id").value = id;
        }
    </script>
    <script type="text/javascript">
        var jumlah = document.getElementById('bank');
        jumlah.addEventListener('keyup', function(e) {
            jumlah.value = currencyIdrUser2(this.value, 'Rp ');
        });
        var jumlah = document.getElementById('tunai');
        jumlah.addEventListener('keyup', function(e) {
            jumlah.value = currencyIdrUser2(this.value, 'Rp ');
        });
        function currencyIdrUser2(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ?'.':'';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }
    </script>
    <script type="text/javascript">
        var jumlah = document.getElementById('jumlah');
        jumlah.addEventListener('keyup', function(e) {
            jumlah.value = currencyIdrUser(this.value, 'Rp ');
        });
        function currencyIdrUser(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ?'.':'';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }
    </script>

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
    <!-- table js -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script> 
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>  
    @if ($button_kas == FALSE)
    <script>
    $(document).ready( function () {
    $('#myTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
            extend: 'excel',
            text: 'Cetak',
            filename: 'Laporan_Kas_Kecil',
            exportOptions: {
                columns: [0,1,2,3,4,5,6,8],
                format: {
                body: function ( data, row, column, node ) {
                    // Strip $ from salary column to make it numeric
                    return column === 2 ?
                        parseFloat(data.replace(/[^\d\,]/, '')) :
                        data;
                    }
                },
                }
            }
        ],
        stateSave: true,
        order: [[9, 'asc']],
    });
    } );
    </script>
    @elseif ($button_kas == TRUE) 
    <script>
    $(document).ready( function () {
    $('#myTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
            extend: 'excel',
            text: 'Cetak',
            filename: 'Laporan_Kas_Kecil',
            exportOptions: {
                columns: [0,1,2,3,4,5,6,8],
                format: {
                body: function ( data, row, column, node ) {
                    // Strip $ from salary column to make it numeric
                    return column === 3 ?
                        parseFloat(data.replace(/[^\d\,]/, '')) :
                        data;
                    }
                },
                }
            }
        ],
        stateSave: true,
        order: [[8, 'asc']],
    });
    } );
    
    // checkbox selesaikan kas
    $("#head-cb").on('click', function(){
        var isChecked = $("#head-cb").prop('checked')
        $(".cb-child").prop('checked',isChecked)
        $("#button-set-bkk").prop('disabled',!isChecked)
    })

    $("#myTable tbody").on('click','.cb-child',function(){
        if($(this).prop('checked')!=true){
            $("#head-cb").prop('checked',false)
        }

        let semua_checkbox = $("#myTable tbody .cb-child:checked")
        let button_bkk = (semua_checkbox.length > 0)
        $("#button-set-bkk").prop('disabled',!button_bkk)
    })

    function done() {
        let checkbox_terpilih = $("#myTable tbody .cb-child:checked")
        let semua_id = []
        $.each(checkbox_terpilih, function(index,elm){
            semua_id.push(checkbox_terpilih[index].value)
        })
        $.ajax({
            url:"{{url('')}}/kas_selesai",
            method:'post',
            data:{ids:semua_id},
            success:function(res){
                table.ajax.reload(null,false)
            }
        })
    }</script>
    @endif
</body>

</html>