@extends( 'layout.default' )

@section( 'title' , 'MT2 Security Roles' )

@section( 'navRoleClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="roleController as role"')

@section( 'page-menu' )
    @if (Sentinel::hasAccess('role.add'))
        <md-button ng-click="role.viewAdd()" aria-label="Add a new Role">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add a new Role</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="role.loadRoles()">
        <div class="row">
            <div class="col-xs-12">
                <div id="mtTableContainer" class="table-responsive">
                    <generic-table headers="role.headers" records="role.roles" editurl="role.editUrl"></generic-table>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/role.js"></script>
@stop
