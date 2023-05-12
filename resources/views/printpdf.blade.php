<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Cetak Pengajuan</title>

</head>

<body>
    <!-- <img style="float:right;" class="img-fluid w-50" src="/assets/img/logo.jpg" alt=""> -->
    <center><h2>Pengajuan Dana Kas Kecil</h2></center>
    <hr>
    <p style="float:right;">{{$data->today}}</p>
    <p>Kode Pengajuan : {{$data->kode}}</p>
    <br>
    <p>Sehubungan dengan adanya surat ini, dibuatlah pengajuan dengan rincian sebagai berikut :</p>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" cellspacing="0">
                <tbody>
                    <tr>
                        <td>PIC</td>
                        <td></td>
                        <td> : {{$data->User->username}}</td>
                    </tr>
                    <tr>
                        <td>Divisi</td>
                        <td></td>
                        <td> : {{$data->Divisi->name}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td></td>
                        <td> : {{Carbon\Carbon::parse($data->tanggal)->isoFormat('dddd, D MMMM Y')}}</td>
                    </tr>
                    <tr>
                        <td>Nominal Pengajuan</td>
                        <td></td>
                        <td> : Rp {{number_format($data->jumlah, 2, ",", ".")}}</td>
                    </tr>
                    <tr>
                        <td>Keterangan Pengajuan</td>
                        <td></td>
                        <td> : {{$data->deskripsi}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>Demikian surat pengajuan ini kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
        <br>
        <br>
    </div>
</body>

</html>