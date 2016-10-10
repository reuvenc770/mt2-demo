<md-sidenav md-component-id="mainNav" class="md-sidenav-left mt2-sidenav-dark md-hue-3" md-is-locked-open="true" ng-cloak ng-init="app.setSidenavCookie()" ng-class="{ 'mt2-sidenav-minimized' : app.sideNavMinimized }">
    <div class="mt2-nav-toggle" ng-click="app.toggleNavSize( $event )">
        <i class="material-icons">menu</i>
    </div>

    <!-- Normal Menu -->
    <div class="mt2-logo-container" layout="row" ng-hide="app.sideNavMinimized">
        <img src="/img/mt2_icon.png" class="mt2-logo-img"><h2 class="mt2-logo-text">MT2</h2>
    </div>

    <md-list layout-align="center start" ng-hide="app.sideNavMinimized">
    @foreach ( $menuItems as $section )
        <md-list-item class="mt2-nav-main-item" ng-click="app.openSideNavMenu( '{{ $section[ 'name' ] }}' )" ng-class="{ 'mt2-nav-icon-active' : app.activeSection[ '{{$section[ 'name' ]}}' ] }" layout-align="start center" aria-label="{{ $section[ 'name' ] }}">
            @if ( $section[ 'icon' ] != '' )
            <md-icon class="mt2-nav-icon" md-font-set="material-icons">{{$section[ 'icon' ]}}</md-icon>
            @endif

            <span class="mt2-nav-main-text">{{ $section[ 'name' ] }}</span>

            <span flex></span>

            <md-icon ng-hide="app.sidenavSectionOpenStatus[ '{{ $section[ 'name' ] }}' ]" md-font-set="material-icons">chevron_right</md-icon>

            <md-icon ng-show="app.sidenavSectionOpenStatus[ '{{ $section[ 'name' ] }}' ]" md-font-set="material-icons">expand_more</md-icon>
        </md-list-item>

        <div class="mt2-nav-sub-item" ng-init="app.initSideNavMenu( '{{$section[ 'name' ]}}' )">
            @foreach ( $section[ 'children' ] as $currentChild )
            <a href="{{ '/' . $currentChild[ 'uri' ] }}" ng-class="{ 'mt2-nav-active' : '{{$currentChild[ 'uri' ]}}' == app.currentPath }" ng-init="app.openActiveSideNavMenu( '{{$section[ 'name' ]}}' , '{{$currentChild[ 'uri' ]}}' )" target="_self">
                <md-list-item ng-show="app.sidenavSectionOpenStatus[ '{{ $section[ 'name' ] }}' ]" aria-label="{{ $currentChild[ 'name' ] }}">
                    <span flex="20"></span>
                    <span class="childMenu"><em>{{ $currentChild[ 'name' ] }}</em></span>
                    <span flex></span>
                </md-list-item>
            </a>
            @endforeach
        </div>
    @endforeach
    </md-list>

    <!-- Mini Menu -->
    <div class="mt2-mini-logo-container" layout="row" ng-show="app.sideNavMinimized" ng-mouseover="app.closeHoverMenu()">
        <img src="/img/mt2_icon.png">
    </div>

    <md-list ng-show="app.sideNavMinimized">
    @foreach ( $menuItems as $section )
        <md-list-item id="{{ $section[ 'name' ] }}Parent" class="mt2-nav-main-icon-item" ng-class="{ 'mt2-nav-icon-active' : app.activeSection[ '{{$section[ 'name' ]}}' ] }" ng-mouseover="app.openHoverMenu( '{{$section[ 'name' ]}}' , $event )" aria-label="{{ $section[ 'name' ] }}">
            @if ( $section[ 'icon' ] != '' )
            <md-icon class="mt2-nav-icon" md-font-set="material-icons">{{$section[ 'icon' ]}}</md-icon>
            @endif
        </md-list-item>
    @endforeach
    </md-list>

    @foreach ( $menuItems as $section )
    <div class="hoverChildMenu" ng-show="app.sidenavMouseOverOpenStatus[ '{{ $section[ 'name' ] }}' ]" ng-style="app.sidenavMouseOverCss[ '{{ $section[ 'name' ] }}' ]" ng-mouseleave="app.closeHoverMenu()">
        <md-list>
            @foreach ( $section[ 'children' ] as $currentChild )
            <a ng-class="{ 'mt2-nav-active' : '{{$currentChild[ 'uri' ]}}' == app.currentPath }" href="{{ '/' . $currentChild[ 'uri' ] }}" target="_self">
                <md-list-item>
                    <span flex="10"></span>
                    <span><em>{{ $currentChild[ 'name' ] }}</em></span>
                    <span flex></span>
                </md-list-item>
            </a>
            @endforeach
        </md-list>
    </div>
    @endforeach

    <div ng-mouseover="app.closeHoverMenu()" style="height: 20px;"></div>
</md-sidenav>
