<div>
    <!-- {{-- Nothing in the world is as soft and yielding as water. --}} -->
    <!-- Filter COA & Tanggal -->
    <div class="row">
        <div class="form-group-row" style="margin-right: 5px; max-width:430px; color:black;">
            <input type="text" autocomplete="search-coa" wire:model="searchCoa" placeholder="Cari COA" class="form-control form-control-sm" style="color:black;">
            <select wire:model="selectedCoaId" id="selectedCoaFromInput" required onclick="getCoa()" class="form-control form-control-sm" style="color:black;">
                @foreach ($coaList as $itemCoa)
                <option value="{{$itemCoa->coa_id}}">{{$itemCoa->code}} {{$itemCoa->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group-row" style="margin-right: 5px; max-width:400px; ">
            <select wire:model="selectedCompany" required class="form-control form-control-sm" style="color:black;">
                <option value="">Input company</option>
                @foreach ($companyList as $item)
                <option value="{{$item->project_company_id}}">{{$item->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group-row" style="margin-right: 5px; max-width:400px;">
            <select wire:model="selectedProject" required class="form-control form-control-sm" style="color:black;">
                <option value="">Input project</option>
                @foreach ($projectList as $item)
                <option value="{{$item->project_id}}">{{$item->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group-row" style="margin-right: 5px; max-width:400px;">
            <select wire:model="selectedUnit" class="form-control form-control-sm" style="color:black;">
                <option value="">All Unit</option>
                @foreach ($unitList as $item)
                <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <label for="date" class="col-form-label">Mulai</label>
        <div class="col-md-2" x-data="datepicker()">
            <input wire:model="startDate" class="datepicker form-control form-control-sm" type="text" x-ref="startDatepicker" required>
        </div>
        <label for="date" class="col-form-label">Selesai</label>
        <div class="col-md-2" x-data="datepicker2()">
            <input wire:model="endDate" id="endDate" class="datepicker form-control form-control-sm" type="text" x-ref="endDatepicker" required>
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
                <th scope="col" class="font-weight-bold text-dark">Unit</th>
                <th scope="col" class="font-weight-bold text-dark">Pembebanan</th>
                <th scope="col" class="font-weight-bold text-dark">Project</th>
                <th scope="col" class="font-weight-bold text-dark">Barcode</th>
                <th scope="col" class="font-weight-bold text-dark">Kas Keluar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kas as $item)
            <tr>
                <td><input type="checkbox" value="{{$item->id}}" wire:model="selectedKasId"></td>
                <td class="font-weight-bold text-dark">{{\Carbon\Carbon::parse($item->tanggal)->format('d-m-Y')}}</td>
                <td class="font-weight-bold text-dark">{{$item->deskripsi}}
                    @if($item->in_budget==1)
                    <span class="badge bg-danger text-white">Overbudget</span>
                    @endif
                </td>
                <td class="font-weight-bold text-dark">{{$item->COA->code}} {{$item->COA->name}}</td>
                <td class="font-weight-bold text-dark">{{strtolower($item->unit->name)}}</td>
                <td class="font-weight-bold text-dark">{{$item->Pembebanan->name}}</td>
                <td class="font-weight-bold text-dark">
                    @if ($item->project_id)
                    {{$item->Project->name}}
                    @endif
                </td>
                <td class="font-weight-bold text-dark">{{$item->bkk_header_id}}</td>
                <td class="font-weight-bold text-dark" style="text-align:right">Rp. {{number_format($item->jumlah,2,",", ".")}}</td>
            </tr>
            @empty
            @endforelse
        </tbody>
    </table>
    @if(count($kas))
    {{$kas->links()}}
    @endif
    @if (session()->has('message_overbudget'))
    <div class="alert alert-danger">
        {{ session('message_overbudget') }}
    </div>
    @endif
    <button style="float:right;" class="btn-sm btn-primary" wire:click="getSelectedKas">Add Transaction</button>
    <div>
        @if (session()->has('message_coa'))
        <div class="alert alert-danger">
            {{ session('message_coa') }}
        </div>
        @endif
        @if (session()->has('message_budget'))
        <div class="alert alert-danger">
            {{ session('message_budget') }}
        </div>
        @endif
    </div>
    <!-- End Tabel List Transaksi -->

    <!-- Tabel Transaksi Terpilih -->
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th scope="col" class="font-weight-bold text-dark">Tanggal</th>
                <th scope="col" class="font-weight-bold text-dark">Keterangan</th>
                <th scope="col" class="font-weight-bold text-dark">COA</th>
                <th scope="col" class="font-weight-bold text-dark">Unit</th>
                <th scope="col" class="font-weight-bold text-dark">Kas Keluar</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if ($selectedKas)
            @foreach ($selectedKas as $item => $list_kas)
            @foreach ($list_kas as $row)
            @foreach ($row as $data)
            <tr>
                <td><i class="fas fa-trash" wire:click="deleteKas({{$data['id']}})"></i></td>
                <td class="font-weight-bold text-dark">{{\Carbon\Carbon::parse($data['tanggal'])->format('d-m-Y')}}</td>
                <td class="font-weight-bold text-dark">{{$data['deskripsi']}}
                    @if($data['in_budget']==1)
                    <span class="badge bg-danger text-white">Overbudget</span>
                    @endif
                </td>
                <td class="font-weight-bold text-dark">{{App\Models\Coa::getCoa($item)->code}} {{App\Models\Coa::getCoa($item)->name}}</td>
                <td class="font-weight-bold text-dark">{{$data['unit']['name']}}</td>
                <td class="font-weight-bold text-dark" style="text-align:right">Rp. {{number_format($data['jumlah'],2,",", ".")}}</td>
                <td></td>
            </tr>
            @endforeach
            @foreach ($row as $data)
            @foreach ($totalKasCoa as $total)
            @foreach ($total as $kasCoaUnit)
            @if($kasCoaUnit['id'] == $data['id'])
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="font-weight-bold text-dark"><strong>Subtotal</strong></td>
                <td class="font-weight-bold text-dark" style="text-align:right"><strong>Rp. {{number_format($kasCoaUnit['total_kas'],2,",", ".")}}</strong></td>
            </tr>
            @endif
            @endforeach
            @endforeach
            @endforeach
            <tr>
                <td colspan="6"></td>
            </tr>
            @endforeach
            @endforeach
            @endif
            <tr>
                <th></th>
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
        <div class="form-group-row" style="margin-right: 5px; max-width:400px;">
            <select wire:model="selectedRekening" required class="form-control form-control-sm" style="color:black;">
                <option value="">Input rekening</option>
                @foreach ($rekeningList as $item)
                <option value="{{$item->bank_id}}">{{$item->name}} {{$item->rekening}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group-row" style="margin-right: 5px; max-width:250px;">
            <select wire:model="selectedPartner" onchange="removeRequired()" id="selectedPartnerDropdownId" class="form-control form-control-sm" style="color:black;">
                <option value="">Input partner</option>
                @foreach ($partnerList as $item)
                <option value="{{$item->name}}">{{$item->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group-row" style="margin-right: 5px; width:250px;">
            <input type="text" wire:model="manualTypePartner" oninput="removeRequired()" id="manualTypePartnerTextId" class="form-control form-control-sm" placeholder="Ketik Partner, Pilih Atau Ketik" style="color:black;">
        </div>
        <div class="form-group-row" style="color:black">
            Tanggal BKK
        </div>
        <div class="col-sm form-group-row" style="margin-right: 5px">
            <input wire:model="tanggalBkk" class="form-select form-control-sm" type="date" id="tanggal" name="tanggal" required placeholder="Tanggal BKK">
        </div>
        <div class="col-sm form-group-row" style="margin-right: 5px">
            <button style="float:right;" class="btn-sm btn-primary" wire:click="createBKK" wire:loading.remove>Create BKK</button>
            <div wire:loading wire:target="createBKK">
                Processing BKK...
            </div>
        </div>
    </div>
    <div>
        @if (session()->has('message_save'))
        <div class="alert alert-success">
            {{ session('message_save') }}
        </div>
        @endif
        @if (session()->has('message_not_save'))
        <div class="alert alert-danger">
            {{ session('message_not_save') }}
        </div>
        @endif
    </div>
    <!-- End Form Input -->
</div>
@once
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr(".datepicker", {
        mode: "single"
    });
</script>
<script>
    function removeRequired() {
        if (document.getElementById("selectedPartnerDropdownId").value != "" || document.getElementById("manualTypePartnerTextId").value != "") {
            document.getElementById("selectedPartnerDropdownId").removeAttribute("required");
            document.getElementById("manualTypePartnerTextId").removeAttribute("required");
        } else {
            document.getElementById("selectedPartnerDropdownId").setAttribute("required", true);
            document.getElementById("manualTypePartnerTextId").setAttribute("required", true);
        }
    }

    function getCoa() {
        Livewire.emit('getSelectedCoa', document.getElementById("selectedCoaFromInput").value)
    }
</script>
@endonce