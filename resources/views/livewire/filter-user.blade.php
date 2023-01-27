<div>
    <!-- {{-- If your happiness depends on money, you will never be happy with yourself. --}} -->
    <!-- Dropdown User -->
    <div class="dropdown" style="float:right;">
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
