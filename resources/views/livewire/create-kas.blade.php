    <div>
        <!-- {{-- Care about people's approval and you will be their prisoner. --}} -->
        <div class="form-group">
            <label for="tanggal">Tanggal Pengeluaran :</label>
            <input type="date" wire:model="selectedDate" class="datepicker form-control" placeholder="Tanggal Pengeluaran" id="tanggal" name="tanggal" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Keterangan :</label>
            <input type="text" autocomplete="on" wire:model="deskripsi" class="form-control" placeholder="Keterangan Pengeluaran" id="deskripsi" name="deskripsi" required>
        </div>
        <div class="form-group">
            <label for="kredit">Nominal :</label>
            <input type="text" autocomplete="on" wire:model="jumlah" class="form-control" placeholder="Nominal Pengeluaran" id="kredit" name="kredit" required>
        </div>
        <div class="form-group">
            <label for="coa">COA :</label>
            <div class="row">
                <div class="col-md-3">
                    <input wire:model="searchCoa" class="form-control" type="text" name="" id="" placeholder="Pilih COA">
                </div>
                <div class="col-md-9">
                    <select wire:model="selectedCoa" id="selectedCoaFromInput" onchange="getCoa()" name="coa" id="coa" class="form-control" required>
                        @foreach ($coaList as $coa)
                        <option value="{{$coa->coa_id}}">{{$coa->code}} {{$coa->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
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
            <input wire:model="tujuan" autocomplete="on" type="text" class="form-control" placeholder="Dibayarkan Kepada" id="tujuan" name="tujuan" required>
        </div>
        <div class="form-group">
            <label for="pic">PIC :</label>
            <input wire:model="pic" autocomplete="on" type="text" class="form-control" placeholder="PIC" id="pic" name="pic">
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
        <button wire:click="getCompanyProject" class="btn btn-primary" id="btnSubmit">Submit</button>
    </div>
    @once
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#coa").select2({
                placeholder: 'Masukkan kode atau nama COA',
            });
        })
    </script>
    @endonce
    @once
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr(".datepicker", {
            mode: "single"
        });

        function getCoa() {
            Livewire.emit('getSelectedCoaInput', document.getElementById("selectedCoaFromInput").value)
        }
    </script>
    @endonce