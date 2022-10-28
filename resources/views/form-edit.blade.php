
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Edit Kas</title>

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
             @if (Auth::user()->role_id==1)
             <li class="nav-item">
                <a class="nav-link" href="/home_admin">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/pengajuan">
                    <i class="fas fa-fw fa-file"></i>
                    <span>Pengajuan Dana</span></a>
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
             @endif
             @if (Auth::user()->role_id!=1)
             <li class="nav-item">
                <a class="nav-link" href="{{url('/home')}}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
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

                    <!-- Sidebar Toggle (Topbar) -->
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

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
                        <h1 class="h3 mb-2 text-gray-800">Form Edit Kas</h1>
                    </div>
                    
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        </div>
                    
                        <div class="card-body" width="100%">
                            <div class="table-responsive">
                            <form action="/kas_update/{{$kas->id}}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="tanggal">Tanggal :</label>
                                    <input type="date" class="form-control" placeholder="Tanggal Kas" id="tanggal" name="tanggal" value="{{$kas->tanggal}}" required>
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi">Keterangan :</label>
                                    <input type="text" class="form-control" placeholder="Keterangan Kas" id="deskripsi" name="deskripsi" value="{{$kas->deskripsi}}" required>
                                </div>
                                <div class="form-group">
                                    <label for="jumlah">Kas Keluar :</label>
                                    <input type="text" class="form-control" placeholder="Nominal Kas Keluar" id="jumlah" name="jumlah" value="{{$kas->jumlah}}">
                                </div>
                                <div class="form-group">
                                    <label for="kategori">Kategori :</label>
                                    <select name="kategori" id="kategori" class="form-control">
                                        <option value="{{$kas->kategori}}">{{$kas->Kategori->nama_kategori}}</option>
                                        @foreach ($kategori as $kategori)
                                            <option value="{{$kategori->id}}">{{$kategori->nama_kategori}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="pembebanan">Pembebanan :</label>
                                    <select name="pembebanan" id="pembebanan" class="form-control">
                                        <option value="{{$kas->pembebanan}}">{{$kas->Pembebanan->nama_pembebanan}}</option>
                                        @foreach ($pembebanan as $pembebanan)
                                            <option value="{{$pembebanan->id}}">{{$pembebanan->nama_pembebanan}}</option>
                                        @endforeach
                                    </select>
                                </div> 
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                            <script type="text/javascript">
                                var jumlah_keluar = document.getElementById('jumlah');
                                    jumlah_keluar.addEventListener('keyup', function(e) {
                                        jumlah_keluar.value = currencyIdr(this.value, 'Rp ');
                                    });

                                function currencyIdr(angka, prefix) {
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