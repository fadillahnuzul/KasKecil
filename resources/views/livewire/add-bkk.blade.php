<div>
    <!-- {{-- Nothing in the world is as soft and yielding as water. --}} -->
    <!-- Filter COA & Tanggal -->
    <div class="row">
        <div class="col-sm">
            <select wire:model="selectedCoaId">
                <option value="">Ketik nama coa</option>
                @foreach ($coaList as $itemCoa)
                <option value="{{$itemCoa->coa_id}}">{{$itemCoa->name}} ({{$itemCoa->code}})</option>
                @endforeach
            </select>
        </div>
        <label for="date" class="col-form-label">Mulai</label>
        <div class="col-md-2" x-data="datepicker()">
            <input wire:model="startDate"  class="datepicker form-control form-control-sm" type="text" x-ref="startDatepicker">
        </div>
        <label for="date" class="col-form-label">Selesai</label>
        <div class="col-md-2" x-data="datepicker2()">
            <input wire:model="endDate" id="endDate" class="datepicker form-control form-control-sm" type="text" x-ref="endDatepicker">
        </div>
    </div>
    <!-- Filter COA & Tanggal -->
    <!-- Tabel List Transaksi -->
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <!-- <th scope="col"><input type="checkbox" wire:model="selectedAll"></th> -->
                <th scope="col" class="font-weight-bold text-dark">Tanggal</th>
                <th scope="col" class="font-weight-bold text-dark">Keterangan</th>
                <th scope="col" class="font-weight-bold text-dark">COA</th>
                <th scope="col" class="font-weight-bold text-dark">Kas Keluar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kas as $item)
            <tr>
                <td><input type="checkbox" value="{{$item->id}}" wire:model="selectedKasId" wire:model="selected"></td>
                <td class="font-weight-bold text-dark">{{\Carbon\Carbon::parse($item->tanggal)->format('d-m-Y')}}</td>
                <td class="font-weight-bold text-dark">{{$item->deskripsi}}</td>
                <td class="font-weight-bold text-dark">{{$item->COA->code}} {{$item->COA->name}}</td>
                <td class="font-weight-bold text-dark" style="text-align:right">Rp. {{number_format($item->jumlah,2,",", ".")}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{$kas->links()}}
    <button style="float:right;" class="btn-sm btn-primary" wire:click="getSelectedKas">Add Transaction</button>
    <div>
        @if (session()->has('message_coa'))
        <div class="alert alert-danger">
            {{ session('message_coa') }}
        </div>
        @endif
    </div>
    <!-- End Tabel List Transaksi -->

    <!-- Tabel Transaksi Terpilih -->
    <table class="table">
        <thead>
            <tr>
                <!-- <th></th> -->
                <th scope="col" class="font-weight-bold text-dark">Tanggal</th>
                <th scope="col" class="font-weight-bold text-dark">Keterangan</th>
                <th scope="col" class="font-weight-bold text-dark">COA</th>
                <th scope="col" class="font-weight-bold text-dark">Kas Keluar</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if ($selectedKas)
            {{$no=0;}}
            @foreach ($selectedKas as $item => $list_kas)
            @foreach ($list_kas as $row)
            <tr>
                <!-- <td><button>Hapus</button></td> -->
                <td class="font-weight-bold text-dark">{{\Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y')}}</td>
                <td class="font-weight-bold text-dark">{{$row['deskripsi']}}</td>
                <td class="font-weight-bold text-dark">{{App\Models\Coa::getCoa($row['coa'])->code}} {{App\Models\Coa::getCoa($row['coa'])->name}}</td>
                <td class="font-weight-bold text-dark" style="text-align:right">Rp. {{number_format($row['jumlah'],2,",", ".")}}</td>
                <td></td>
            </tr>
            @endforeach
            @foreach ($totalKasCoa as $total)
            <tr rowspan="{{$total['jumlah_data']}}">
                <td></td>
                <td></td>
                <td></td>
                @if($total['id'] == $list_kas[0]['id'])
                <td class="font-weight-bold text-dark"><strong>Subtotal</strong></td>
                <td class="font-weight-bold text-dark" style="text-align:right"><strong>Rp. {{number_format($total['total_kas'],2,",", ".")}}</strong></td>
                @else 
                <td></td>
                <td></td>
                @endif
            </tr>
            @endforeach
            @endforeach
            @endif
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th class="font-weight-bold text-dark"><strong>Total</strong></th>
                <th class="font-weight-bold text-dark" style="text-align:right"><strong>Rp. {{number_format($totalKas,2,",", ".")}}</strong></th>
            </tr>
        </tbody>
    </table>
    <!-- End Tabel Tansaksi Terpilih -->

    <!-- Form Input -->
    <div class="row">
        <div class="col-sm">
            <select wire:model="selectedCompany">
                <option value="">Input company</option>
                @foreach ($companyList as $item)
                <option value="{{$item->project_company_id}}">{{$item->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm">
            <select wire:model="selectedProject">
                <option value="">Input project</option>
                @foreach ($projectList as $item)
                <option value="{{$item->project_id}}">{{$item->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm">
            <select wire:model="selectedRekening">
                <option value="">Input rekening</option>
                @foreach ($rekeningList as $item)
                <option value="{{$item->bank_id}}">{{$item->name}} {{$item->rekening}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm">
            <select wire:model="selectedPartner">
                <option value="">Input partner</option>
                @foreach ($partnerList as $item)
                <option value="{{$item->name}}">{{$item->name}}</option>
                @endforeach
            </select>
        </div>
        <label for="date" class="col-form-label">Tanggal BKK</label>
        <div class="col-sm">
            <input wire:model="tanggalBkk" type="date" id="tanggal" name="tanggal">
        </div>
    </div>
    <div class="col-sm">
        <button style="float:right;" class="btn-sm btn-primary" wire:click="createBKK">Create BKK</button>
    </div>
    <div>
        @if (session()->has('message_save'))
        <div class="alert alert-success">
            {{ session('message_save') }}
        </div>
        @endif
    </div>
    <!-- End Form Input -->
</div>
@once
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr(".datepicker",{mode:"single"});
</script>
@endonce