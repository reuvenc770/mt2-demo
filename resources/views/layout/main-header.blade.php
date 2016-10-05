<md-toolbar>
    <div class="md-toolbar-tools">
        <span flex></span>

        @if(Sentinel::check())
            <md-button ng-href="{{route("myprofile")}}" target="_self">{{Sentinel::getUser()->first_name}} {{Sentinel::getUser()->last_name}}</md-button>
            <md-button ng-href="{{route("logout")}}" target="_self">Logout</md-button>

        @else
            <md-button ng-href="{{route("login")}}" target="_self">Login</md-button>
        @endif
    </div>
</md-toolbar>

<md-menu-bar>
    <div class="md-toolbar-tools mt2-sub-toolbar">
    {!! Breadcrumbs::renderIfExists() !!}

    <span flex></span>

    @yield( 'page-menu' )
    </div>

</md-menu-bar>
