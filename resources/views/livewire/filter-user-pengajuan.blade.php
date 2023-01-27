<div>
    <!-- {{-- Success is as dangerous as failure. --}} -->
    <div>
    <!-- Dropdown User -->
    <div wire:model="user" class="dropdown" style="float:right;">
        <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Pilih User
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="/home_admin">All</a>
            @foreach ($userList as $list)
            <a class="dropdown-item" value="{{$list->id}}">{{$list->username}}</a>
            @endforeach
        </div>
    </div>
    <!-- End Dropdown User -->
</div>
</div>
