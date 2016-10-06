<md-sidenav md-component-id="mainNav" class="md-sidenav-left mt2-sidenav-dark md-hue-3" md-is-locked-open="true" ng-cloak ng-init="app.setSidenavCookie()" ng-class="{ 'mt2-sidenav-minimized' : app.sideNavMinimized }">
    <div class="mt2-nav-toggle" ng-click="app.toggleNavSize()">
        <i class="material-icons" style="color: #adb0b2;">menu</i>
    </div>

    <div layout="row">
        <img src="/img/mt2_icon.png" style="margin: 10px;"><h2 ng-hide="app.sideNavMinimized" style="margin-top: 17px; color:#fff;margin-left: 10px;">MT2</h2>
    </div>

    <md-list layout-align="center start" ng-hide="app.sideNavMinimized">
    @foreach ( $menuItems as $section )
        <md-list-item class="mt2-nav-main-item" ng-click="app.openSideNavMenu( '{{ $section[ 'name' ] }}' )" layout-align="start center" aria-label="{{ $section[ 'name' ] }}">
            @if ( $section[ 'icon' ] != '' )
            <md-icon class="mt2-nav-icon" md-font-set="material-icons" style="color: #adb0b2;">{{$section[ 'icon' ]}}</md-icon>
            @endif
            <span class="mt2-nav-main-item">{{ $section[ 'name' ] }}</span>
            <span flex></span>

            <md-icon ng-hide="app.sidenavSectionOpenStatus[ '{{ $section[ 'name' ] }}' ]" md-font-set="material-icons" style="color: #adb0b2">chevron_right</md-icon>
            <md-icon ng-show="app.sidenavSectionOpenStatus[ '{{ $section[ 'name' ] }}' ]" md-font-set="material-icons" style="color: #adb0b2">expand_more</md-icon>
        </md-list-item>

        <div ng-init="app.initSideNavMenu( '{{$section[ 'name' ]}}' )" style="background-color: #21262d;">
            @foreach ( $section[ 'children' ] as $currentChild )
            <a href="{{ '/' . $currentChild[ 'uri' ] }}" target="_self">
                <md-list-item class="mt2-nav-sub-item" ng-show="app.sidenavSectionOpenStatus[ '{{ $section[ 'name' ] }}' ]" aria-label="{{ $currentChild[ 'name' ] }}">
                    <span flex="20"></span>
                    <span class="childMenu"><em>{{ $currentChild[ 'name' ] }}</em></span>
                    <span flex></span>
                </md-list-item>
            </a>
            @endforeach
        </div>
    @endforeach
    </md-list>

    @foreach ( $menuItems as $section )
    <md-menu layout-align="center start" ng-show="app.sideNavMinimized">
        <md-button class="mt2-nav-main-item" ng-click="$mdOpenMenu($event)" layout-align="start center" aria-label="{{ $section[ 'name' ] }}">
            @if ( $section[ 'icon' ] != '' )
            <md-icon class="mt2-nav-icon" md-font-set="material-icons" style="color: #adb0b2;">{{$section[ 'icon' ]}}</md-icon>
            @endif
        </md-button>

        <md-menu-content>
            @foreach ( $section[ 'children' ] as $currentChild )
            <md-menu-item>
                <a href="{{ '/' . $currentChild[ 'uri' ] }}" target="_self">
                        <span flex="20"></span>
                        <span class="childMenu"><em>{{ $currentChild[ 'name' ] }}</em></span>
                        <span flex></span>
                </a>
            </md-menu-item>
            @endforeach
        </div>
    </md-menu>
    @endforeach
</md-sidenav>
