    <div>
        <!-- {{-- Care about people's approval and you will be their prisoner. --}} -->
        <div class="form-group">
            <label for="tanggal">Tanggal Pengeluaran :</label>
            <input type="date" wire:model="selectedDate" class="datepicker form-control" placeholder="Tanggal Pengeluaran" id="tanggal" name="tanggal" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Keterangan :</label>
            <input type="text" wire:model="deskripsi" class="form-control" placeholder="Keterangan Pengeluaran" id="deskripsi" name="deskripsi" required>
        </div>
        <div class="form-group">
            <label for="kredit">Nominal :</label>
            <input type="text" wire:model="jumlah" class="form-control" placeholder="Nominal Pengeluaran" id="kredit" name="kredit" required>
        </div>
        <div class="form-group">
            <label for="coa">COA :</label>
            <select wire:model="selectedCoa" name="coa" id="coa" class="form-control" required>
                <option value="">--</option>
                @foreach ($coaList as $coa)
                <option value="{{$coa->coa_id}}">{{$coa->code}} {{$coa->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="company">Company :</label>
            <select wire:model="selectedCompany" class="form-control">
                <option value="">--</option>
                @foreach ($companyList as $Company)
                <option value="{{$Company->project_company_id}}">{{$Company->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="project">Project :</label>
            <select wire:model="selectedProject" class="form-control">
                <option value="">--</option>
                @foreach ($projectList as $project)
                <option value="{{$project->project_id}}">{{$project->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="tujuan">Dibayarkan kepada (Nota tujuan) :</label>
            <input wire:model="tujuan" type="text" class="form-control" placeholder="Dibayarkan Kepada" id="tujuan" name="tujuan" required>
        </div>
        <div class="form-group">
            <label for="pic">PIC :</label>
            <input wire:model="pic" type="text" class="form-control" placeholder="PIC" id="pic" name="pic">
        </div>
        <button wire:click="getCompanyProject" class="btn btn-primary" id="btnSubmit">Submit</button>
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