
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Create BKK</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
             @if (Auth::user()->kk_access=='1')
             <li class="nav-item">
                <a class="nav-link" href="/home_admin">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="/pengajuan">
                    <i class="fas fa-fw fa-file"></i>
                    <span>Pengajuan Dana</span></a>
            </li> -->
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
             @endif
             @if (Auth::user()->kk_access=='2')
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
                        <h1 class="h3 mb-2 text-gray-800">Form Create BKK</h1>
                </div>
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
                    <livewire:add-bkk></livewire:add-bkk>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        @include('sweetalert::alert')
                        </div>
                        <div class="card-body" width="100%">
                            <div class="table-responsive">
                            <form action="/save_bkk" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="company">Company :</label>
                                    <select name="company" id="company" class="form-control">
                                        <option value="">--</option>
                                        @foreach ($Company as $Company)
                                            <option value="{{$Company->project_company_id}}">{{$Company->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="project">Project :</label>
                                    <select name="project" id="project" class="form-control">
                                        <option value="">--</option>
                                        @foreach ($Project as $Project)
                                            <option value="{{$Project->project_id}}">{{$Project->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="rekening">Rekening :</label>
                                    <select name="rekening" id="rekening" class="form-control">
                                     <option value="">--</option>
                                        @foreach ($Rekening as $Rekening)
                                            <option value="{{$Rekening->bank_id}}">{{$Rekening->name}} {{$Rekening->rekening}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="coa">COA :</label>
                                    <select name="coa" id="coa" class="form-control" required>
                                        <option value="">--</option>
                                        @foreach ($Coa as $Coa)
                                            <option value="{{$Coa->coa_id}}">{{$Coa->code}} {{$Coa->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="partner">Partner :</label>
                                    <select name="partner" id="partner" class="form-control" required>
                                        <option value="">--</option>
                                        @foreach ($Partner as $Partner)
                                            <option value="{{$Partner->name}}">{{$Partner->name}} ({{$Partner->contact_person}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal">Tanggal BKK :</label>
                                    <input type="date" class="form-control" placeholder="Tanggal BKK" id="tanggal" name="tanggal" required>
                                </div>
                                <!-- <div class="form-group">
                                    <label for="deskripsi">Keterangan :</label>
                                    <input type="text" class="form-control" placeholder="Keterangan Pengeluaran" id="deskripsi" name="deskripsi" required>
                                </div> -->
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
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

    <!-- Done Modal -->
    <div class="modal fade" id="PembebananModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Pembebanan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                <form action="/input_pembebanan" method="POST">
                    @csrf
                        <div class="form-group">
                            <label for="pembebanan">Input Pembebanan :</label>
                            <input type="text" class="form-control" placeholder="Input nama pembebanan" id="pembebanan" name="pembebanan" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div> 
                        </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('style/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('style/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{asset('style/vendor/jquery-easing/jquery.easing.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#coa").select2({
                placeholder: 'Masukkan kode atau nama COA',
            });
        });
        $(document).ready(function(){
            $("#company").select2({
                placeholder: 'Masukkan nama company',
            });
        });
        $(document).ready(function(){
            $("#project").select2({
                placeholder: 'Masukkan nama project',
            });
        });
        $(document).ready(function(){
            $("#rekening").select2({
                placeholder: 'Masukkan nama bank atau nomor rekening',
            });
        });
        $(document).ready(function(){
            $("#partner").select2({
                placeholder: 'Masukkan nama partner atau contact person',
            });
        });
    </script>
    <!-- Custom scripts for all pages-->
    <script src="{{asset('style/js/sb-admin-2.min.js')}}"></script>

    <!-- Page level plugins -->
    <script src="{{asset('style/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('style/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

    <!-- Page level custom scripts -->
    <script src="{{asset('style/js/demo/datatables-demo.js')}}"></script>

</body>

</html>