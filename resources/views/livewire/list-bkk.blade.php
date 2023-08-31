<div>
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header pb-0">
                <div class="container">
                    <div class="row">
                        <div class="container-fluid">
                            <div class="form-group row">
                                <label for="date" class="col-form-label">Mulai</label>
                                <div class="col-md-2" x-data="datepicker()">
                                    <input wire:model="startDate" class="datepicker form-control form-control-sm" type="text" x-ref="startDatepicker" required>
                                </div>
                                <label for="date" class="col-form-label">Selesai</label>
                                <div class="col-md-2" x-data="datepicker2()">
                                    <input wire:model="endDate" id="endDate" class="datepicker form-control form-control-sm" type="text" x-ref="endDatepicker" required>
                                </div>
                                <div class="form-group-row" style="margin-inline: 5px;">
                                    <select name="company" id="company-dropdown">
                                        @if ($selectedCompany)
                                        <option selected value="{{$selectedCompany->project_company_id}}">{{$selectedCompany->name}}</option>
                                        @endif
                                        <option value="">All Company</option>
                                        @foreach ($companyList as $company)
                                        <option value="{{$company->project_company_id}}">{{$company->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- <div class="form-group-row" style="margin-inline: 5px;">
                                                    <select name="project" id="project-dropdown">
                                                        <option value="">All Project</option>
                                                    </select>
                                                </div> -->
                                <div class="form-group-row" style="margin-inline: 5px;">
                                    <button style="margin-left:5px; margin-right:5px;" type="submit" class="btn btn-sm btn-primary">Tampil</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            @if ($selectedCompany)
                            <tr class="font-weight-bold text-dark">{{$selectedCompany->name}}</tr>
                            @endif
                            <tr>
                                <th><input type="checkbox" id="head-cb"></th>
                                <th class="font-weight-bold text-dark">Barcode</th>
                                <th class="font-weight-bold text-dark">Company</th>
                                <th class="font-weight-bold text-dark">Project</th>
                                <th class="font-weight-bold text-dark">Tanggal</th>
                                <th></th>
                                <!-- <th class="font-weight-bold text-dark">Aksi</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataBkk as $row)
                            <tr>
                                <td><input type="checkbox" class="cb-child" value="{{$row->id}}"></td>
                                <td class="font-weight-bold text-dark">{{$row->id}}</td>
                                <td class="font-weight-bold text-dark">{{$row->project->company->name}}</td>
                                <td class="font-weight-bold text-dark">{{$row->project->name}}</td>
                                <td class="font-weight-bold text-dark">{{Carbon\Carbon::parse($row->created_at)->format('d-m-Y')}}</td>
                                <td class="font-weight-bold text-dark">
                                    <button wire:click="$emitTo('detail-bkk', 'showBkkDetail')" class="btn btn-primary btn-sm">Detail</button>
                                    <button class="btn btn-success btn-sm"><i class="fas fa-print fa-sm"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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