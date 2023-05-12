<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{$title}}</title>
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

    <!-- Ajax
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}"> -->

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

                    <!-- Saldo, Tunai, Bank -->
                    <!-- Card Saldo -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            @if ($laporan == FALSE)
                                            <div class="text-s font-weight-bold text-success text-uppercase mb-1">
                                                Saldo</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($Saldo ,2, ",", ".")}}</div>
                                            @endif
                                            @if ($laporan == TRUE)
                                            <div class="text-s font-weight-bold text-success text-uppercase mb-1">
                                                Total Pengajuan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($total_pengajuan ,2, ",", ".")}}</div>
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

                        <!-- Card Total Pengajuan -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            @if ($laporan == FALSE)
                                            <div class="text-s font-weight-bold text-warning text-uppercase mb-1">
                                                Total Pengajuan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($total_pengajuan ,2, ",", ".")}}</div>
                                            @elseif ($laporan == TRUE)
                                            <div class="text-s font-weight-bold text-warning text-uppercase mb-1">
                                                Total Belum Diklaim</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($total_pengeluaran ,2, ",", ".")}}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Card Total Pengajuan -->
                        <!-- Card Belum Diklaim -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            @if ($laporan == FALSE)
                                            <div class="text-s font-weight-bold text-info text-uppercase mb-1">
                                                Kas Keluar</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($total_pengeluaran ,2, ",", ".")}}</div>
                                            @elseif ($laporan == TRUE)
                                            <div class="text-s font-weight-bold text-info text-uppercase mb-1">
                                                Total Diklaim</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($total_diklaim ,2, ",", ".")}}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Card Belum Diklaim -->
                        <!-- Card Sisa Saldo -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-danger text-uppercase mb-1">
                                                Sisa Saldo</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($total_diklaim ,2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Card Sisa Saldo -->
                    </div>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header pb-0">
                            <!-- Dropdown Divisi -->
                            <div class="dropdown" style="float:right;">
                                <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Pilih Unit
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @if ($laporan==TRUE)
                                    <a class="dropdown-item" href="/admin_laporan">All</a>
                                    @foreach ($divisi as $divisi)
                                    <!-- 1 = filter unit dashboard, 2 = filter unit laporan -->
                                    <a class="dropdown-item" href="/kas_divisi/2/{{$divisi->id}}">{{$divisi->name}}</a>
                                    @endforeach
                                    @elseif ($laporan==FALSE)
                                    <a class="dropdown-item" href="/home_admin">All</a>
                                    @foreach ($divisi as $divisi)
                                    <!-- 1 = filter unit dashboard, 2 = filter unit laporan -->
                                    <a class="dropdown-item" href="/kas_divisi/1/{{$divisi->id}}">{{$divisi->name}}</a>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                            <!-- End Dropdown Divisi -->
                            <!-- Dropdown Company -->
                            <div class="dropdown" style="float:right; margin-right:10px;">
                                <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Pilih Company
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="/index_filter_keluar">All</a>
                                    @foreach($companyList as $list)
                                    <a class="dropdown-item" href="/index_filter_keluar/2/{{$list->project_company_id}}">{{$list->name}}</a>
                                    @endforeach
                                </div>
                            </div>
                            <!-- End Dropdown Company -->
                            <!-- Dropdown User -->
                            <div class="dropdown" style="float:right; margin-right:10px;">
                                <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Pilih User
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="/index_filter_keluar">All</a>
                                    @foreach ($userList as $list)
                                    <a class="dropdown-item" href="/index_filter_keluar/1/{{$list->id}}">{{$list->username}}</a>
                                    @endforeach
                                </div>
                            </div>
                            <!-- End Dropdown User -->
                            @if ($laporan == TRUE)
                            <a href="/pengajuan.export" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" style="float:right; margin-right:5px"><i class="fas fa-download fa-sm text-white-50"></i> Cetak</a>
                            @endif
                            <div class="container">
                                <div class="row">
                                    <form action="" method="POST">
                                        @csrf
                                        <div class="container-fluid">
                                            <div class="form-group row">
                                                <label for="date" class="col-form-label">Mulai</label>
                                                <div class="col-sm">
                                                    <input type="date" class="form-control input-sm" id="startDate" class="date-input" value={{$startDate}} name="startDate">
                                                </div>
                                                <label for="date" class="col-form-label">Selesai</label>
                                                <div class="col-sm">
                                                    <input type="date" class="form-control input-sm" id="endDate" class="date-input" value={{$endDate}} name="endDate">
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
                                <table id="myTable" class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            @if ($filter_keluar == FALSE)
                                            <th class="font-weight-bold text-dark">Kode</th>
                                            <th class="font-weight-bold text-dark">Tanggal</th>
                                            <th class="font-weight-bold text-dark">User</th>
                                            <th class="font-weight-bold text-dark">Divisi</th>
                                            <th class="font-weight-bold text-dark">Keterangan</th>
                                            <th class="font-weight-bold text-dark">Pengajuan</th>
                                            <th class="font-weight-bold text-dark">Status</th>
                                            @elseif ($filter_keluar == TRUE)
                                            <th class="font-weight-bold text-dark">Tanggal</th>
                                            <th class="font-weight-bold text-dark">Keterangan</th>
                                            <th class="font-weight-bold text-dark">User</th>
                                            <th class="font-weight-bold text-dark">Kas Keluar</th>
                                            <th class="font-weight-bold text-dark">COA</th>
                                            <th class="font-weight-bold text-dark">Pembebanan</th>
                                            <th class="font-weight-bold text-dark">Status</th>
                                            <th class="font-weight-bold text-dark">Tanggal Respon</th>
                                            <th class="font-weight-bold text-dark">Tanggal BKK</th>
                                            @endif
                                            <th class="font-weight-bold text-dark">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        @foreach ($dataKas as $row)
                                        <tr>
                                            @if ($filter_keluar == FALSE)
                                            <td class="font-weight-bold text-dark">{{$row->kode}}</td>
                                            <td class="font-weight-bold text-dark">{{Carbon\Carbon::parse($row->tanggal)->format('d-m-Y')}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->User->username}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->Divisi->name}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->deskripsi}}</td>
                                            <td class="font-weight-bold text-dark">{{number_format($row->jumlah ,2, ",", ".")}}</td>
                                            <!-- <td class="font-weight-bold text-dark">@if ($row->sumber == NULL) 
                                                -
                                                @endif
                                                @if ($row->sumber != NULL)
                                                    {{$row->Sumber->sumber_dana}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">Rp. {{number_format($row->total_belanja ,2, ",", ".")}}</td>
                                            <td class="font-weight-bold text-dark">Rp. {{number_format($row->diklaim,2, ",", ".")}}</td> -->
                                            <td class="font-weight-bold text-dark">{{$row->Status->nama_status}}</td>
                                            <!-- filter kas keluar di dashboard admin -->
                                            @elseif ($filter_keluar == TRUE)
                                            <td class="font-weight-bold text-dark">{{Carbon\Carbon::parse($row->tanggal)->format('d-m-Y')}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->deskripsi}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->User->username}}</td>
                                            <td class="font-weight-bold text-dark">Rp. {{number_format($row->jumlah,2,",", ".")}}</td>
                                            <td class="font-weight-bold text-dark">@if ($row->coa)
                                                {{$row->COA->code}} <br>
                                                {{$row->COA->name}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">@if ($row->pembebanan)
                                                {{$row->Pembebanan->name}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">{{$row->Status->nama_status}}</td>
                                            <td class="font-weight-bold text-dark">
                                                @if ($row->tanggal_respon)
                                                {{Carbon\Carbon::parse($row->tanggal_respon)->format('d-m-Y')}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">
                                                @if ($row->tanggal_set_bkk)
                                                {{Carbon\Carbon::parse($row->tanggal_set_bkk)->format('d-m-Y')}}
                                                @endif
                                            </td>
                                            @endif
                                            <!-- filter kas keluar di dashboard admin -->
                                            <td class="font-weight-bold text-dark">
                                                <a href="export_pengajuan/{{$row->id}}" class="btn btn-success btn-sm">Print</a>
                                                @if ($row->Status->id==4 OR $row->Status->id==2)
                                                <a onclick="return confirm ('Apakah yakin untuk menghapus?')" href="/hapus_admin/1/{{$row->id}}" class="btn btn-danger btn-sm">Hapus</a>
                                                <a href="/edit_admin/{{$row->id}}" class="btn btn-info btn-sm">Edit</a>
                                                <a onclick="return confirm ('Apakah yakin untuk menyelesaikan pengajuan ini?')" href="/done_pengajuan/{{$row->id}}" class="btn btn-primary btn-sm">Set Done</a>
                                                @if ($row->Status->id == 1 AND $row->User->kk_access == 2)
                                                <a href="/acc/{{$row->id}}" class="btn btn-success btn-sm">
                                                    Approve</a>
                                                <a onclick="return confirm ('Apakah yakin untuk menolak?')" href="/tolak/{{$row->id}}" class="btn btn-warning btn-sm">
                                                    Decline</a>
                                                @elseif ($filter_keluar==FALSE && ($row->Status->id == 2 OR $row->Status->id == 4 OR $row->Status->id == 5))
                                                <!-- <a href="/detail_divisi/{{$row->id}}" class="btn btn-primary btn-sm">Detail</a>  -->
                                                @endif
                                                @elseif ($row->Status->id==9)
                                                <a onclick="return confirm ('Apakah yakin pengajuan ini sudah dikembalikan?')" href="/konfirm_kembali/{{$row->id}}" class="btn btn-success btn-sm">Konfirmasi Pengembalian</a>
                                                @endif
                                            </td>
                                        </tr>
                                        <?php $no++; ?>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- <button class="btn btn-primary" id="save-btn">Klaim</button>
                                <h5 style="margin-top:15px" id="totalDiklaim"></h5> -->
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

    <!-- Datatable -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    @if ($laporan == TRUE)
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excel',
                    text: 'Cetak',
                    filename: 'Laporan_Pengajuan_Kas_Kecil',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 6, 5],
                        format: {
                            body: function(data, row, column, node) {
                                // Strip $ from salary column to make it numeric
                                return column == 6 ?
                                    parseFloat(data.replace(/[^\d\,]/g, '')) :
                                    data;
                            }
                        },
                    }
                }],
                stateSave: true,
            });
        });
    </script>
    @elseif ($laporan == FALSE)
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                stateSave: true,
            });
        });
    </script>
    @endif
</body>

</html>