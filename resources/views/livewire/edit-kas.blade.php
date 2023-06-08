<div>
    <!-- {{-- The Master doesn't talk, he acts. --}} -->
    <div class="form-group">
        <label for="tanggal">Tanggal :</label>
        <input wire:model="selectedDate" type="date" class="datepicker form-control" placeholder="Tanggal Kas" id="tanggal" name="tanggal" required>
    </div>
    <div class="form-group">
        <label for="deskripsi">Keterangan :</label>
        <input wire:model="deskripsi" type="text" class="form-control" placeholder="Keterangan Kas" id="deskripsi" name="deskripsi" required>
    </div>
    <div class="form-group">
        <label for="jumlah">Kas Keluar :</label>
        <input wire:model="jumlah" type="text" class="form-control" placeholder="Nominal Kas Keluar" id="jumlah" name="jumlah">
    </div>
    <div class="form-group">
        <label for="coa">COA :</label>
        <div class="row">
            <div class="col-md-3">
                <input wire:model="searchCoa" class="form-control" type="text" name="" id="" placeholder="Cari COA">
            </div>
            <div class="col-md-9">
                <select wire:model="selectedCoa" name="coa" id="coa" class="form-control" required>
                    @foreach ($coaList as $coa)
                    <option value="{{$coa->coa_id}}">{{$coa->code}} {{$coa->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="company">Company :</label>
        <select wire:model="selectedCompany" name="company" id="company" class="form-control">
            <option value="">--</option>
            @foreach ($companyList as $Company)
            <option value="{{$Company->project_company_id}}">{{$Company->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="project">Project :</label>
        <select wire:model="selectedProject" name="project" id="project" class="form-control">
            <option value="">--</option>
            @foreach ($projectList as $project)
            <option value="{{$project->project_id}}">{{$project->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="tujuan">Dibayarkan kepada (Nota tujuan):</label>
        <input wire:model="tujuan" type="text" class="form-control" placeholder="Keterangan Pengeluaran" value="{{$kas->tujuan}}" id="tujuan" name="tujuan">
    </div>
    <div class="form-group">
        <label for="pic">PIC :</label>
        <input wire:model="pic" value="{{$kas->pic}}" type="text" class="form-control" placeholder="PIC" id="pic" name="pic">
    </div>
    @if (session()->has('message_kas'))
    <div class="alert alert-danger">
        {{ session('message_kas') }}
    </div>
    @endif
    @if (session()->has('tidak_ada_budget'))
    <div class="alert alert-danger">
        {{ session('tidak_ada_budget') }}
    </div>
    @endif
    @if (session()->has('budget_kurang'))
    <div class="alert alert-danger">
        {{ session('budget_kurang') }}
    </div>
    @endif
    <button wire:click="updateKas" class="btn btn-primary">Update</button>
    <div wire:loading wire:target="updateKas">
        Update Kas...
    </div>
</div>
@once
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr(".datepicker", {
        mode: "single"
    });
</script>
@endonce