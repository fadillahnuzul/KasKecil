<div>
    <!-- {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}} -->
    <!-- Dropdown Company -->
    <div class="dropdown" style="float:right; margin-top:5px;">
        <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Pilih Company
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="/home_admin">All</a>
            @foreach ($companyList as $list)
            <a class="dropdown-item" value="{{$list->project_company_id}}">{{$list->name}}</a>
            @endforeach
        </div>
    </div>
    <!-- End Dropdown Company -->
</div>
