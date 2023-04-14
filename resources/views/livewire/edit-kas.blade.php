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
        <select wire:model="selectedCoa" name="coa" id="coa" class="form-control">
            <option value="">--</option>
            @foreach ($coaList as $Coa)
            <option value="{{$Coa->coa_id}}">{{$Coa->code}} {{$Coa->name}}</option>
            @endforeach
        </select>
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
    <button wire:click="updateKas" class="btn btn-primary">Update</button>
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