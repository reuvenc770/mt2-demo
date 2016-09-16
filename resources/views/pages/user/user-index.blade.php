@extends( 'layout.default' )

@section( 'title' , 'MT2 User List' )

@section( 'navEspClasses' , 'active' )

@section( 'angular-controller', 'ng-controller="userController as user"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('user.add'))
        <md-button ng-click="user.viewAdd()" aria-label="Add User Account">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add User Account</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="user.loadAccounts()">
        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card>
                @include( 'pages.user.user-table' )
            </md-card>
        </md-content>
    </div>

@stop

@section( 'pageIncludes' )
    <script src="js/user.js"></script>
@stop
