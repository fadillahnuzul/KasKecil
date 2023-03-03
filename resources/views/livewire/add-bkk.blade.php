<div>
    <!-- {{-- Nothing in the world is as soft and yielding as water. --}} -->
    <div class="row">
        <div class="col-sm">
            <select wire:model="Coa" name="coa" id="coa">
                <option value="">--</option>
                @foreach ($Coa as $Coa)
                <option value="{{$Coa->coa_id}}">{{$Coa->code}} {{$Coa->name}}</option>
                @endforeach
            </select>
        </div>
        <label for="date" class="col-form-label">Mulai</label>
        <div class="col-sm">
            <input wire:model="startDate" type="date" class="form-control input-sm" id="startDate" value={{$startDate}} name="startDate">
        </div>
        <label for="date" class="col-form-label">Selesai</label>
        <div class="col-sm">
            <input wire:model="endDate" type="date" class="form-control input-sm" id="endDate" value={{$endDate}} name="endDate">
        </div>
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