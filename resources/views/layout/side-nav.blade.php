<nav id="mainSideNav" class="navmenu navmenu-inverse navmenu-fixed-left" ng-class="{ 'offcanvas' : app.isFixedNav() === false }" role="navigation">
    <a class="navmenu-brand center-block" href="/" target="_self">
        <h3><img src="/img/mt2_icon.png" /> CMP</h3>
    </a>

    <ul class="nav navmenu-nav">
        @foreach ( $menuItems as $section )
        <li class="dropdown custom-dropdown" id="{{{$section[ 'name' ]}}}Menu" ng-class="{ 'open' : app.activeSection[ '{{{$section[ 'name' ]}}}' ] }">
            <a href="" class="dropdown-toggle" ng-click="app.toggleDropdown( $event )">
                @if ( $section[ 'glyth' ] != '' )
                <span class="glyphicon {{$section[ 'glyth' ]}}"></span>
                @endif
                <span class="mt2-nav-main-text">{{ $section[ 'name' ] }}</span>
                <span class="glyphicon glyphicon-menu-right pull-right" aria-hidden="true" ng-hide="app.menuIsOpen( '{{{$section[ 'name' ]}}}Menu' )"></span>
                <span class="glyphicon glyphicon-menu-down pull-right" aria-hidden="true" ng-show="app.menuIsOpen( '{{{$section[ 'name' ]}}}Menu' )"></span>
            </a>
            <ul class="dropdown-menu navmenu-nav" role="menu">
                @if(isset($section[ 'children' ]))
                @foreach ( $section[ 'children' ] as $currentChild )
                <li class="nav-child-item" ng-class="{ 'active' : app.activeMenuLink[ '{{$currentChild[ 'name' ]}}' ] }" ng-init="app.setCurrentActiveSection( '{{$section[ 'name' ]}}' , '{{$currentChild[ 'name' ]}}' , '{{$currentChild[ 'uri' ]}}' )">
                    <a href="{{ '/' . $currentChild[ 'uri' ] }}" target="_self"><span class="nav-child-text">{{ $currentChild[ 'name' ] }}</span></a>
                </li>
                @endforeach
                @endif
            </ul>
        </li>
        @endforeach
    </ul>

</nav>