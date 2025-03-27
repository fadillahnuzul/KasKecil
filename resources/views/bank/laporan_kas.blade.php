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
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{asset('style/css/sb-admin-2.min.css')}}" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="{{asset('style/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        @include('asset.sidebar')
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


                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <a href="/pengeluaran.export" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" style="float:right; margin-right:5px"><i class="fas fa-download fa-sm text-white-50"></i> Cetak</a>
                            <div class="container">
                                <div class="row">
                                    <form action="" method="POST">
                                        @csrf
                                        <div class="container-fluid">
                                            <div class="form-group row">
                                                <label for="date" class="col-form-label">Mulai</label>
                                                <div class="form-group-row" style="margin-inline: 5px;">
                                                    <input type="date" class="datepicker form-control input-sm" id="startDate" value={{$startDate}} name="startDate">
                                                </div>
                                                <label for="date" class="col-form-label">Selesai</label>
                                                <div class="form-group-row" style="margin-inline: 5px;">
                                                    <input type="date" class="datepicker form-control input-sm" id="endDate" value={{$endDate}} name="endDate">
                                                </div>
                                                <div class="form-group-row" style="margin-inline: 5px;">
                                                    <select name="status" id="status">
                                                        @if ($selectedStatus)
                                                        <option selected value="{{$selectedStatus->id}}">{{$selectedStatus->nama_status}}</option>
                                                        @endif
                                                        <option value="">All Status</option>
                                                        @foreach ($status as $status)
                                                        <option value="{{$status->id}}">{{$status->nama_status}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group-row" style="margin-inline: 5px;">
                                                    <select name="company" id="company-dropdown">
                                                        @if ($selectedCompany)
                                                        <option selected value="{{$selectedCompany->project_company_id}}">{{$selectedCompany->name}}</option>
                                                        @endif
                                                        <option value="">All Company</option>
                                                        @foreach ($company as $company)
                                                        <option value="{{$company->project_company_id}}">{{$company->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group-row" style="margin-inline: 5px;">
                                                    <select name="user" id="user">
                                                        @if ($selectedUser)
                                                        <option selected value="{{$selectedUser->id}}">{{$selectedUser->username}}</option>
                                                        @endif
                                                        <option value="">All User</option>
                                                        @foreach ($userList as $user)
                                                        <option value="{{$user->id}}">{{$user->username}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group-row" style="margin-inline: 5px;">
                                                    <button style="margin-left:5px; margin-right:5px;" type="submit" class="btn btn-sm btn-primary">Tampil</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="font-weight-bold text-dark" width="8%">Tanggal</th>
                                            <th class="font-weight-bold text-dark">Keterangan</th>
                                            <th class="font-weight-bold text-dark">User</th>
                                            <th class="font-weight-bold text-dark">Kas Keluar</th>
                                            <th class="font-weight-bold text-dark">COA</th>
                                            <th class="font-weight-bold text-dark">Pembebanan</th>
                                            <th class="font-weight-bold text-dark">Status</th>
                                            <th class="font-weight-bold text-dark">Tanggal Respon</th>
                                            <!-- <th>Aksi</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataKas as $row)
                                        <tr>
                                            <td class="font-weight-bold text-dark py-1">{{Carbon\Carbon::parse($row->tanggal)->format('d-m-Y')}}</td>
                                            <td class="font-weight-bold text-dark py-1">{{$row->deskripsi}}</td>
                                            <td class="font-weight-bold text-dark py-1">{{$row->User->username}}</td>
                                            <td class="font-weight-bold text-dark py-1">Rp. {{number_format($row->jumlah,2,",", ".")}}</td>
                                            <td class="font-weight-bold text-dark py-1">@if ($row->coa)
                                                {{$row->COA->code}} <br>
                                                {{$row->COA->name}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark py-1">
                                                @if ($row->pembebanan)
                                                {{$row->Pembebanan->name}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark py-1">{{$row->Status->nama_status}}</td>
                                            <td class="font-weight-bold text-dark py-1">
                                                @if ($row->tanggal_respon)
                                                {{Carbon\Carbon::parse($row->tanggal_respon)->format('d-m-Y')}}
                                                @endif
                                            </td>
                                        </tr>
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
    <script>
        function set_modal_id(id) {
            document.getElementById("modal_id").value = id;
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

</body>

</html>