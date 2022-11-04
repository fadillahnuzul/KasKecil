
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('style/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('style/css/sb-admin-2.min.css')}}" rel="stylesheet">

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
                                        <h1 class="h4 text-gray-900 mb-4">Kas Kecil</h1>
                                    </div>
                                    <form method="POST" action="/login">
                                        @csrf
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Username" id="username" name="username" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" placeholder="Password" id="password" name="password" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary btn-user btn-block" type="submit">Login</button>
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

</body>

</html>