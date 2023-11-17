<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                    <!-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-2 text-gray-800"></h1>
                        <a href="/export" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                            class="fas fa-download fa-sm text-white-50"></i> Cetak Laporan</a>
                    </div> -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-2">
                            <div class="card border-left-success shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-success text-uppercase mb-1">
                                                Total Pengajuan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($Saldo,2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Card Saldo -->
                        <div class="col-xl-3 col-md-6 mb-2">
                            <div class="card border-left-danger shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-danger text-uppercase mb-1">
                                                Total Terpakai</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($totalKeluar,2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Card Tunai -->
                        <div class="col-xl-3 col-md-6 mb-2">
                            <div class="card border-left-warning shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-warning text-uppercase mb-1">
                                                Total Klaim</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($totalKlaim,2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Tunai -->
                        <div class="col-xl-3 col-md-6 mb-2">
                            <div class="card border-left-info shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-info text-uppercase mb-1">
                                                Sisa Saldo</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($sisa,2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    @foreach ($userList as $user)
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-success text-uppercase mb-1">
                                                Total Pengajuan ({{$user->username}})</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($user->total_pengajuan,2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Card Saldo -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-danger text-uppercase mb-1">
                                                Belum Klaim ({{$user->username}})</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($user->total_pengeluaran,2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Card Tunai -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-warning text-uppercase mb-1">
                                                Sudah Klaim ({{$user->username}})</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($user->total_diklaim,2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Tunai -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-s font-weight-bold text-info text-uppercase mb-1">
                                                Sisa Saldo ({{$user->username}})</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp. {{number_format($user->sisa_saldo,2, ",", ".")}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header pb-0">
                            <!-- End Dropdown Divisi -->
                            <!-- @if ($laporan == TRUE) -->
                            <!-- <a href="/pengeluaran.export" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" style="float:right; margin-right:5px"><i
                                class="fas fa-download fa-sm text-white-50"></i> Cetak</a> -->
                            <!-- @endif -->
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
                                                <!-- <div class="form-group-row" style="margin-inline: 5px;">
                                                    <select name="project" id="project-dropdown">
                                                        <option value="">All Project</option>
                                                    </select>
                                                </div> -->
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
                                <table class="table table-bordered" id="myTable" width="100%" cellspacing="0">
                                    <thead>
                                        @if ($selectedCompany)
                                        <tr class="font-weight-bold text-dark">{{$selectedCompany->name}}</tr>
                                        @endif
                                        <tr>
                                            <th><input type="checkbox" id="head-cb"></th>
                                            <th class="font-weight-bold text-dark">Tanggal</th>
                                            <th class="font-weight-bold text-dark">Keterangan</th>
                                            <th class="font-weight-bold text-dark">User</th>
                                            <th class="font-weight-bold text-dark">Kas Keluar</th>
                                            <th class="font-weight-bold text-dark">COA</th>
                                            <th class="font-weight-bold text-dark">Pembebanan</th>
                                            <th class="font-weight-bold text-dark">Project</th>
                                            <th class="font-weight-bold text-dark">Nota Tujuan</th>
                                            <th class="font-weight-bold text-dark">PIC</th>
                                            <th class="font-weight-bold text-dark">Status</th>
                                            <th class="font-weight-bold text-dark">Tanggal Respon</th>
                                            <th class="font-weight-bold text-dark">Tanggal BKK</th>
                                            <th class="font-weight-bold text-dark">Tanggal Kembali</th>
                                            <th class="font-weight-bold text-dark">Barcode</th>
                                            <!-- <th class="font-weight-bold text-dark">Aksi</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataKas as $row)
                                        <tr>
                                            <td><input type="checkbox" class="cb-child" value="{{$row->id}}"></td>
                                            <td class="font-weight-bold text-dark">{{Carbon\Carbon::parse($row->tanggal)->format('d-m-Y')}}</td>
                                            <td class="font-weight-bold text-dark">
                                                {{$row->deskripsi}}
                                                @if($row->in_budget==1)
                                                <span class="badge bg-danger text-white">Overbudget</span>
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">{{$row->User->username}}</td>
                                            <td class="font-weight-bold text-dark">{{number_format($row->jumlah ,2, ",", ".")}}</td>
                                            <td class="font-weight-bold text-dark">@if ($row->coa)
                                                {{$row->COA->code}} {{$row->COA->name}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">@if ($row->pembebanan)
                                                {{$row->Pembebanan->name}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">@if ($row->project_id)
                                                {{$row->Project->name}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">{{$row->tujuan}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->pic}}</td>
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
                                            <td class="font-weight-bold text-dark">
                                                @if ($row->tanggal_uang_kembali)
                                                {{Carbon\Carbon::parse($row->tanggal_uang_kembali)->format('d-m-Y')}}
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">{{$row->bkk_header_id}}</td>
                                            <!-- <td class="font-weight-bold text-dark">
                                        @if($row->status != 8)
                                            <a href="/set_bkk/{{$row->id}}" class="btn btn-warning btn-sm">Set BKK</a>
                                        @endif
                                        </td> -->
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                @if ($laporan==FALSE)
                                <button id="button-set-bkk" type="button" disabled onclick="klaim()" class="btn btn-sm btn-success">Klaim</button>
                                @elseif ($laporan==TRUE)
                                <button id="button-set-bkk" type="button" disabled onclick="setBKK()" class="btn btn-sm btn-success">Set BKK</button>
                                <button id="button-set-kembali" type="button" disabled onclick="setUangKembali()" class="btn btn-sm btn-warning">Set Uang Kembali</button>
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
    <!-- <script src="{{asset('style/vendor/datatables/jquery.dataTables.min.js')}}"></script> -->
    <!-- <script src="{{asset('style/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script> -->

    <!-- Page level custom scripts -->
    <script src="{{asset('style/js/demo/datatables-demo.js')}}"></script>
    
    <!-- table js -->
    <script src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script>
        var company = {!! json_encode($selectedCompany) !!};
        if (company == null) {
            company_name = 'PT ABDAEL NUSA'
        } else {
            company_name = company.name
        }
        $(document).ready(function() {
            $('#myTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Cetak',
                    messageTop: company_name,
                    title: 'Laporan Kas Keluar',
                    filename: 'Laporan_Kas_Kecil',
                    exportOptions: {
                        columns: [1, 2, 5, 7, 8, 10, 11, 4],
                        format: {
                            body: function(data, row, column, node) {
                                // Strip $ from salary column to make it numeric
                                return column == 7 ?
                                    parseFloat(data.replace(/[^\d\,]/g, '')) :
                                    data;
                            }
                        },
                    }
                }],
                columnDefs: [{
                        targets: [0],
                        ordering: false,
                    },
                    {
                        targets: [1],
                        type: "date",
                    },
                    // {
                    //     targets: [4],
                    //     render: (function (data, type, row) {
                    //     return type === 'export' ?
                    //         data.replace( /[$,.]/g, '' ) :
                    //         data;
                    //     }) 
                    // },
                ],
                stateSave: true,
                order: [
                    [9, 'asc']
                ],
            });
        });

        $("#head-cb").on('click', function() {
            var isChecked = $("#head-cb").prop('checked')
            $(".cb-child").prop('checked', isChecked)
            $("#button-set-bkk").prop('disabled', !isChecked)
            $("#button-set-kembali").prop('disabled', !isChecked)
        })

        $("#myTable tbody").on('click', '.cb-child', function() {
            if ($(this).prop('checked') != true) {
                $("#head-cb").prop('checked', false)
            }

            let semua_checkbox = $("#myTable tbody .cb-child:checked")
            let button_bkk = (semua_checkbox.length > 0)
            $("#button-set-bkk").prop('disabled', !button_bkk)
            $("#button-set-kembali").prop('disabled', !button_bkk)
        })

        

        function setBKK() {
            let checkbox_terpilih = $("#myTable tbody .cb-child:checked")
            let semua_id = []
            $.each(checkbox_terpilih, function(index, elm) {
                semua_id.push(checkbox_terpilih[index].value)
            })
            $.ajax({
                url: "{{url('')}}/set_bkk_checkbox",
                method: 'post',
                data: {
                    ids: semua_id
                },
                success: function(res) {
                    alert(res.message)
                    table.ajax.reload(null, false)
                }
            })
            location.reload()
        }

        function setUangKembali() {
            let checkbox_terpilih = $("#myTable tbody .cb-child:checked")
            let semua_id = []
            $.each(checkbox_terpilih, function(index, elm) {
                semua_id.push(checkbox_terpilih[index].value)
            })
            $.ajax({
                url: "{{url('')}}/set_uang_kembali",
                method: 'post',
                data: {
                    ids: semua_id, _token: '{{csrf_token()}}' 
                },
                success: function(res) {
                    alert(res.message)
                    table.ajax.reload(null, false)
                }
            })
            location.reload()
        }

        function klaim() {
            let checkbox_terpilih = $("#myTable tbody .cb-child:checked")
            let semua_id = []
            $.each(checkbox_terpilih, function(index, elm) {
                semua_id.push(checkbox_terpilih[index].value)
            })
            $.ajax({
                url: "{{url('')}}/done",
                method: 'post',
                data: {
                    ids: semua_id
                },
                success: function(res) {
                    alert(res.message);
                    table.ajax.reload(null, false)
                }
            })
            location.reload()
        }
    </script>
</body>

</html>