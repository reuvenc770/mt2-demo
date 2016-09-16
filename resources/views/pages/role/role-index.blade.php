@extends( 'layout.default' )

@section( 'title' , 'MT2 Security Roles' )

@section( 'navRoleClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="roleController as role"')

@section( 'page-menu' )
    @if (Sentinel::hasAccess('role.add'))
        <md-button ng-click="role.viewAdd()" aria-label="Add a new Role">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add a new Role</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="role.loadRoles()">
    <md-content layout="row" class="md-mt2-zeta-theme md-hue-1" layout-align="center center">
        <md-card flex-gt-md="70" flex="100">
            @include( 'pages.role.role-table' )
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
    <script src="js/role.js"></script>
@stop
