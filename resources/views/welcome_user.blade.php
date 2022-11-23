
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Pilih Company</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('style/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('style/css/sb-admin-2.min.css')}}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-lg-6">

            @if(Session::has('status'))
                <div class="alert alert-danger" role="alert">
                    {{Session::get('message')}}
                </div>
            @endif
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                       
                            <!-- <div class="col-lg-6 d-none d-lg-block">
                            <img src="{{asset('style/img/undraw_rocket.svg')}}" style="width:800; height:600;"> 
                            </div> -->
                            
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Pilih Company dan Project</h1>
                                    </div>
                                    <form method="POST" action="/getCompany">
                                        @csrf
                                        <div class="form-group">
                                            <label for="company">Company :</label>
                                            <select name="company" id="company" class="form-control" required>
                                            <option value="">--</option>
                                            @foreach ($Company as $Company)
                                                <option value="{{$Company->project_company_id}}">{{$Company->name}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
	                                        <label for="title">Project :</label>
	                                        <select name="project" class="form-control"></select>
	                                    </div>
                                        <button class="btn btn-primary btn-user btn-block" type="submit">Submit</button>
                                    </form>
                                </div>
                            
                        
                    </div>
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
    <!-- Dependent dropdown project -->
    <script type="text/javascript">
    $(document).ready(function() {
        $('select[name="company"]').on('change', function() {
            var stateID = $(this).val();
            if(stateID) {
                $.ajax({
                    url: '/project/'+stateID,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {                      
                        $('select[name="project"]').empty();
                        $.each(data, function(key, value) {
                        $('select[name="project"]').append('<option value="'+ key +'">'+ value +'</option>');
                        });
                    }
                });
            }else{
                $('select[name="project"]').empty();
            }
        });
    });
</script>
</body>

</html>