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

    <!-- Ajax -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

                    <!-- Saldo, Tunai, Bank -->
                    <div class="row">
                        <!-- Card Saldo -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-success text-uppercase mb-1">
                                                Total Pengajuan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($total_pengajuan ,2, ",", ".")}}</div>
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
                            <div class="card border-left-warning shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-warning text-uppercase mb-1">
                                                Total Belum Diklaim</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($total_pengeluaran ,2, ",", ".")}}</div>
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
                            <div class="card border-left-info shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-info text-uppercase mb-1">
                                                Total Diklaim</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($diklaim ,2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-success text-uppercase mb-1">
                                                Sisa Saldo</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($sisa_saldo ,2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Card Bank -->
                    </div>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <!-- Dropdown Divisi -->
                            <div class="dropdown" style="float:right;">
                                <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Pilih Unit
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="/home_admin">All</a>
                                    @foreach ($divisi as $divisi)
                                    <a class="dropdown-item" href="/bank_kas_divisi/{{$divisi->id}}">{{$divisi->name}}</a>
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
                                    <form action="" method="POST">
                                        @csrf
                                        <div class="container-fluid">
                                            <div class="form-group row">
                                                <label for="date" class="col-form-label col-sm">Mulai</label>
                                                <div class="col-sm">
                                                    <input type="date" class="form-control input-sm" id="startDate" value={{$startDate}} name="startDate">
                                                </div>
                                                <label for="date" class="col-form-label col-sm">Selesai</label>
                                                <div class="col-sm">
                                                    <input type="date" class="form-control input-sm" id="endDate" value={{$endDate}} name="endDate">
                                                </div>
                                                <div class="form-group-row" style="margin-inline: 5px;">
                                                    <select name="company" id="company-dropdown">
                                                        <option value="">All Company</option>
                                                        @foreach ($companyList as $item)
                                                        <option value="{{$item->project_company_id}}" @if($item->project_company_id == $selectedCompany) selected @endif>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
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
                                            <!-- <th></th> -->
                                            <th class="font-weight-bold text-dark">Kode</th>
                                            <th class="font-weight-bold text-dark">Tanggal</th>
                                            <th class="font-weight-bold text-dark">User</th>
                                            <th class="font-weight-bold text-dark">Company</th>
                                            <th class="font-weight-bold text-dark">Keterangan</th>
                                            <th class="font-weight-bold text-dark">Pengajuan</th>
                                            <th class="font-weight-bold text-dark">Status</th>
                                            @if($laporan == FALSE)
                                            <th class="font-weight-bold text-dark">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        @foreach ($dataKas as $row)
                                        <tr>
                                            <!-- <td>{{$row->id}}</td> -->
                                            <td class="font-weight-bold text-dark py-1">{{$row->kode}}</td>
                                            <td class="font-weight-bold text-dark py-1">{{Carbon\Carbon::parse($row->tanggal)->format('d-m-Y')}}</td>
                                            <td class="font-weight-bold text-dark py-1">{{$row->User->username}}</td>
                                            <td class="font-weight-bold text-dark py-1">
                                                @if($row->Company)
                                                {{$row->Company->name}}
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark py-1">{{$row->deskripsi}}</td>
                                            <td class="font-weight-bold text-dark py-1">Rp. {{number_format($row->jumlah,2,",", ".")}}</td>
                                            <td class="font-weight-bold text-dark py-1">{{$row->Status->nama_status}}</td>
                                            @if ($laporan == FALSE)
                                            <td class="font-weight-bold text-dark py-1">
                                                @if ($row->Status->id != 6)
                                                <a onclick="return confirm ('Apakah yakin untuk menghapus?')" href="/hapus_bank/{{$row->id}}" class="btn btn-danger btn-sm">Hapus</a>
                                                <a href="/edit_bank/{{$row->id}}" class="btn btn-info btn-sm">Edit</a>
                                                @if ($row->Status->id == 1)
                                                <a href="/acc_bank/{{$row->id}}" class="btn btn-success btn-sm">
                                                    Approve</a>
                                                <a onclick="return confirm ('Apakah yakin untuk menolak?')" href="/tolak_bank/{{$row->id}}" class="btn btn-warning btn-sm">
                                                    Decline</a>
                                                @elseif ($row->Status->id == 2 OR $row->Status->id == 4 OR $row->Status->id == 5)
                                                <a href="/detail_bank/{{$row->id}}" class="btn btn-primary btn-sm">Detail</a>
                                                @endif
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                        <?php $no++; ?>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- <button class="btn btn-primary" id="save-btn">Klaim</button> -->
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
    <!-- Checkbox -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
    <!-- <script type="text/javascript">
        var table;
        $(document).ready(function () {
            table = $('#dataTable').DataTable({
                'columnDefs' :[{
                    'targets':0,
                    'checkboxes':{
                        'selectRow':true
                    }
                }]
            });
        });

        $('#save-btn').on('click',function(){
            var selected_rows = table.column(0).checkboxes.selected();

            const rowIds = [];
            $.each(selected_rows, function(key, pengajuanId){
                rowIds.push(pengajuanId);
            });
            console.table(rowIds);
            $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/klaim',
                    type: "POST",  
                    datatype: "json",
                    data: {pengajuanId: rowIds},
                    success: function (data) {
                        console.log(data);
                    },
                    error: function (data, textStatus, errorThrown) {
                        console.log(data);
 
                    },
                });
        });
        </script> -->
</body>

</html>