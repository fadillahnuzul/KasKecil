<div>
    <!-- {{-- Stop trying to control. --}} -->
    <div class="form-group row" style="margin-inline: 5px;">
        <label for="date" class="col-form-label">Mulai</label>
        <div class="form-group-row">
            <input type="date" class="input-sm" id="startDate" value={{$startDate}} name="startDate">
        </div>
        <label for="date" class="col-form-label">Selesai</label>
        <div class="form-group-row" style="margin-inline: 5px;">
            <input type="date" class="input-sm" id="endDate" value={{$endDate}} name="endDate">
        </div>
        <div class="form-group-row" style="margin-inline: 5px;">
            <select name="status" id="status">
                <option value="">All Status</option>
            </select>
        </div>
        <div class="form-group-row" style="margin-inline: 5px;">
            <select name="company" id="company">
                <option value="">All Company</option>
            </select>
        </div>
        <div class="form-group-row" style="margin-inline: 5px;">
            <select wire:model="selectedProject" name="project" id="project">
                <option value="">All Project</option>
            </select>
        </div>
    </div>
    <div class="form-group-row" style="margin-inline: 5px;">
        <select name="user" id="user">
            <option value="">All User</option>
        </select>
    </div>
</div>