@inject( 'menu' , 'App\Services\NavigationService' )

<nav id="adminNav" class="navmenu navmenu-default navmenu-fixed-left offcanvas" role="navigation">
    <ul class="nav navmenu-nav">
    @foreach ( $menu->getMenu() as $current )
        <li ng-class="{ active : {{ $current[ 'active' ] }} }"><a href="{{ $current[ 'uri' ] }}" target="_self">{{ $current[ 'name' ] }}</a></li>
    @endforeach
    </ul>
</nav>
