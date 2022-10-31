
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
<center><h2>Pengajuan Kas</h2></center>
<br>
<p style="float:right;">{{$data->today}}</p>
<p>Kode Pengajuan : {{$data->kode}}</p>
<br>
<p>Sehubungan dengan adanya surat ini, dibuatlah pengajuan dengan rincian sebagai berikut :</p>
    <div class="card-body">
        <div class="table-responsive" >
            <table class="table table-bordered"  cellspacing="0">
                <tbody>
                    <tr>
                        <td>Divisi</td>
                        <td></td>
                        <td> : {{$data->Divisi->name}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td></td>
                        <td> : {{$data->tanggal}}</td>
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
        <div class="table-responsive" >
            <table class="table table-bordered" width="100%" cellspacing="0" style="text-align:center;">
                <tbody>
                    <tr height="80px";>
                        <td>Tertanda
                            <br>
                            <br>
                            <br>
                            <br>
                            {{$data->pengaju}}
                        </td>
                        <td>Menyetujui
                            <br>
                            <br>
                            <br>
                            <br>
                            {{$data->penerima}}
                        </td>
                    </tr>
                
                    <tr height="80px";>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>