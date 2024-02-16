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
    @livewireStyles
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
                <div class="container-fluid">
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header pb-2" style="align-text:left; background-color:white;">
                            <a href="/print_bkk/{{$bkkHeader->id}}" style="float: right;" class="btn btn-success btn-sm" target="_blank">Print <i class="fas fa-print fa-sm"></i></a>
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="font-weight-bold text-dark">
                                            <tbody>
                                                <tr>
                                                    <td>Barcode</td>
                                                    <td>:</td>
                                                    <td>{{$bkkHeader->id}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Bank</td>
                                                    <td>:</td>
                                                    <td>{{$bkkHeader->bank->name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Rekening</td>
                                                    <td>:</td>
                                                    <td>{{$bkkHeader->bank->rekening}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="font-weight-bold text-dark">
                                            <tbody>
                                                <tr>
                                                    <td>Tanggal</td>
                                                    <td>:</td>
                                                    <td>{{\Carbon\Carbon::parse($bkkHeader->created_at)->format('d-m-Y H:i:s')}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Company</td>
                                                    <td>:</td>
                                                    <td>{{$bkkHeader->project->company->name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Project</td>
                                                    <td>:</td>
                                                    <td>{{$bkkHeader->project->name}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="font-weight-bold text-dark">Detail BKK</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="font-weight-bold text-dark">Barcode</th>
                                            <th class="font-weight-bold text-dark">Pekerjaan</th>
                                            <th class="font-weight-bold text-dark">COA</th>
                                            <th class="font-weight-bold text-dark">Unit</th>
                                            <th class="font-weight-bold text-dark">DPP</th>
                                            <th class="font-weight-bold text-dark">PPN</th>
                                            <th class="font-weight-bold text-dark">PPH</th>
                                            <th class="font-weight-bold text-dark">Payment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($bkkDetail as $row)
                                        <tr>
                                            <td class="font-weight-bold text-dark">{{$row->id}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->pekerjaan}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->coa->code}} {{$row->coa->name}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->unit->name}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->dpp}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->ppn}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->pph}}</td>
                                            <td class="font-weight-bold text-dark">{{$row->payment}}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5"></td>
                                        </tr>
                                        @endforelse
                                        <tr class="font-weight-bold text-dark">
                                            <td colspan="4">Total</td>
                                            <td>{{$totalDpp}}</td>
                                            <td>{{$totalPpn}}</td>
                                            <td>{{$totalPph}}</td>
                                            <td>{{$totalPayment}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Begin Page Content -->

                <!-- End of Main Content -->
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
            var company = @json($selectedCompany);
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
            })

            $("#myTable tbody").on('click', '.cb-child', function() {
                if ($(this).prop('checked') != true) {
                    $("#head-cb").prop('checked', false)
                }

                let semua_checkbox = $("#myTable tbody .cb-child:checked")
                let button_bkk = (semua_checkbox.length > 0)
                $("#button-set-bkk").prop('disabled', !button_bkk)
            })
        </script>
        @livewireScripts
</body>

</html>