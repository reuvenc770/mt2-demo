@inject( 'menu' , 'App\Services\NavigationService' )

<nav id="adminNav" class="navmenu navmenu-default navmenu-fixed-left offcanvas" role="navigation">
    <ul class="nav navmenu-nav">
    @foreach ( $menu->getMenu() as $current )
        <li ng-class="{ active : {{ $current[ 'active' ] }} }"><a href="{{ $current[ 'uri' ] }}" target="_self">{{ $current[ 'name' ] }}</a></li>
    @endforeach
        @if(Sentinel::check())
            <li><a href="{{route("logout")}}" target="_self">Log Out</a></li>
        @else
            <li><a href="{{route("login")}}" target="_self">Log In</a></li>
        @endif
    </ul>
</nav>
