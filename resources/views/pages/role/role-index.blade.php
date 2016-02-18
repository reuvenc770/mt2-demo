@extends( 'layout.default' )

@section( 'title' , 'MT2 Security Roles' )

@section( 'navRoleClasses' , 'active' )

@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">Security Roles</h1></div>
    </div>

    <div ng-controller="roleController as role" ng-init="role.loadRoles()">
        @if (Sentinel::hasAccess('role.add'))
        <div class="row">
            <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="role.viewAdd()"><span class="glyphicon glyphicon-plus"></span>Add a new Role</button>
        </div>
        @endif
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
