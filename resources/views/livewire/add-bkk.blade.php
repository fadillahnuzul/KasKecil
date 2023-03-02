<div>
    <!-- {{-- Nothing in the world is as soft and yielding as water. --}} -->
    <div class="col-sm">
        <select name="status" id="status">
            @if ($selectedStatus)
            <option selected value="{{$selectedStatus->id}}">{{$selectedStatus->nama_status}}</option>
            @endif
            @if ($laporan == TRUE)
            <option value="">All Status</option>
            @endif
            @foreach ($status as $status)
            <option value="{{$status->id}}">{{$status->nama_status}}</option>
            @endforeach
        </select>
    </div>
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="head-cb"></th>
                <th class="font-weight-bold text-dark">Tanggal</th>
                <th class="font-weight-bold text-dark">Keterangan</th>
                <th class="font-weight-bold text-dark">COA</th>
                <th class="font-weight-bold text-dark">Kas Keluar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kas as $kas)
            <tr>
                <td><input type="checkbox" id="child-cb"></td>
                <td class="font-weight-bold text-dark">{{$kas->tanggal}}</td>
                <td class="font-weight-bold text-dark">{{$kas->deskripsi}}</td>
                <td class="font-weight-bold text-dark">{{$kas->COA->code}} {{$kas->COA->name}}</td>
                <td class="font-weight-bold text-dark">{{$kas->jumlah}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>