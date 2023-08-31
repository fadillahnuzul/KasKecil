<div>
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="myTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="head-cb"></th>
                                <th class="font-weight-bold text-dark">Barcode</th>
                                <th class="font-weight-bold text-dark">Pekerjaan</th>
                                <th class="font-weight-bold text-dark">COA</th>
                                <th class="font-weight-bold text-dark">Payment</th>
                                <th class="font-weight-bold text-dark">DPP</th>
                                <th></th>
                                <!-- <th class="font-weight-bold text-dark">Aksi</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detailBkk as $row)
                            <tr>
                                <td><input type="checkbox" class="cb-child" value="{{$row->id}}"></td>
                                <td class="font-weight-bold text-dark">{{$row->id}}</td>
                                <td class="font-weight-bold text-dark">{{$row->pekerjaan}}</td>
                                <td class="font-weight-bold text-dark">{{$row->coa->code}} {{$row->coa->name}}</td>
                                <td class="font-weight-bold text-dark">{{$row->payment}}</td>
                                <td class="font-weight-bold text-dark">{{$row->dpp}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>