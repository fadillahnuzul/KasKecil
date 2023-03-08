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
        <div class="col-sm">
            <input wire:model="startDate" class="form-control" type="date">
        </div>
        <label for="date" class="col-form-label">Selesai</label>
        <div class="col-sm">
            <input wire:model="endDate" class="form-control" type="date">
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
                <td class="font-weight-bold text-dark">{{$item->tanggal}}</td>
                <td class="font-weight-bold text-dark">{{$item->deskripsi}}</td>
                <td class="font-weight-bold text-dark">{{$item->COA->code}} {{$item->COA->name}}</td>
                <td class="font-weight-bold text-dark">Rp. {{number_format($item->jumlah,2,",", ".")}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{$kas->links()}}
    <button style="float:right;" class="btn-sm btn-primary" wire:click="getSelectedKas">Add Transaction</button>
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
            </tr>
        </thead>
        <tbody>
            @if ($selectedKas)
            @foreach ($selectedKas as $item)
            <tr>
                <!-- <td><button>Hapus</button></td> -->
                <td class="font-weight-bold text-dark">{{$item->tanggal}}</td>
                <td class="font-weight-bold text-dark">{{$item->deskripsi}}</td>
                <td class="font-weight-bold text-dark">{{$item->COA->code}} {{$item->COA->name}}</td>
                <td class="font-weight-bold text-dark">Rp. {{number_format($item->jumlah,2,",", ".")}}</td>
            </tr>
            @endforeach
            @endif
            <tr>
                <th></th>
                <th></th>
                <th class="font-weight-bold text-dark">Total Transaksi</th>
                <th class="font-weight-bold text-dark">Rp. {{number_format($totalKas,2,",", ".")}}</th>
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
            <input wire:model="tanggalBkk" type="date" id="tanggal" value={{$startDate}} name="tanggal">
        </div>
    </div>
    <div class="col-sm">
        <button style="float:right;" class="btn-sm btn-primary" wire:click="createBKK">Create BKK</button>
    </div>
    <!-- End Form Input -->
    @push('js')
    <script src="https://code.jquery.com/jquery-3.6.3.slim.js" integrity="sha256-DKU1CmJ8kBuEwumaLuh9Tl/6ZB6jzGOBV/5YpNE2BWc=" crossorigin="anonymous"></script>
    @endpush
</div>