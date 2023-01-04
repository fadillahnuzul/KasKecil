<div class="table-responsive">
    <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
        <thead>
            <tr>
                <!-- <th colspan="2" rowspan="2"></th> -->
                @if ($company)
                <th><strong>{{$company}}</strong></th>
                @else
                <th><strong>PT ABDAEL NUSA</strong></th>
                @endif
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>PIC : ___________</th>
            </tr>
            <tr>
                <th><strong>LAPORAN PENGELUARAN KAS KECIL</strong></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Tanggal : {{$dateNow}}</th>
            </tr>
            <tr>
                <th>PERIODE {{$startDate}} s/d {{$endDate}}</th>
            </tr>
            <tr>
            <th style="border: 2px solid;"><strong>Tanggal</strong></th>
            <th style="border: 2px solid;"><strong>Keterangan</strong></th>
            <th style="border: 2px solid;"><strong>Kode Pengajuan</strong></th>
            <th style="border: 2px solid;"><strong>No. COA</strong></th>
            <th style="border: 2px solid;"><strong>Nama COA</strong></th>
            <th style="border: 2px solid;"><strong>Pembebanan</strong></th>
            <th style="border: 2px solid;"><strong>Dibayarkan pada</strong></th>
            <th style="border: 2px solid;"><strong>Kas Keluar</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1;?>
            @foreach ($data_pengeluaran as $row)
                <tr style="border-right: 2px solid; border-left:2px solid;">
                <td>{{$row->tanggal}}</td>
                <td>{{$row->deskripsi}}</td>
                <td>{{$row->pengajuan->kode}}</td>
                <td>@if ($row->coa)
                    {{$row->COA->code}}
                    @endif
                </td>
                <td>@if ($row->coa)
                    {{$row->COA->name}}
                    @endif
                </td>
                <td>@if ($row->pembebanan)
                    {{$row->Pembebanan->name}}
                    @endif
                </td>
                <td>@if ($row->tujuan)
                    {{$row->tujuan}}
                    @endif
                </td>
                <td>{{$row->jumlah}}</td>
                </tr>
                <?php $no++ ;?>
            @endforeach 
            <tr style="border-top: 2px solid;">
                <th style="border-left: 2px solid;"></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="border: 2px solid;"><strong>Total Pengeluaran</strong></th>
                <th style="border: 2px solid;"><strong>{{$data_pengeluaran->total}}</strong></th>
            </tr>
            <tr>
                <th style="border-left: 2px solid;"></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="border: 2px solid;">Proses Pengajuan Klaim</th>
                <th style="border: 2px solid;">{{$data_pengeluaran->belum_diklaim}}</th>
            </tr>
            <tr>
                <th style="border-left: 2px solid;"></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="border: 2px solid;">Sisa pengajuan belum terpakai</th>
                <th style="border: 2px solid;">{{$data_pengeluaran->sisa}}</th>
            </tr>
            <tr>
                <th style="border-left: 2px solid;"></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="border: 2px solid;">Saldo Kas Kecil</th>
                <th style="border: 2px solid;">{{$data_pengeluaran->saldo}}</th>
            </tr>
            <tr>
                <th style="border-left: 2px solid; border-bottom: 2px solid;"></th>
                <th style="border-bottom: 2px solid;"></th>
                <th style="border-bottom: 2px solid;"></th>
                <th style="border-bottom: 2px solid;"></th>
                <th style="border-bottom: 2px solid;"></th>
                <th style="border-bottom: 2px solid;"></th>
                <th style="border: 2px solid;">Total</th>
                <th style="border: 2px solid;">{{$data_pengeluaran->total_all}}</th>
            </tr>
        </tbody>
    </table>
</div>