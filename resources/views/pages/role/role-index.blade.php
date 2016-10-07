@extends( 'layout.default' )

@section( 'title' , 'MT2 Security Roles' )

@section( 'navRoleClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="roleController as role"')

@section( 'page-menu' )
    @if (Sentinel::hasAccess('role.add'))
        <md-button ng-click="role.viewAdd()" aria-label="Add a new Role">
            <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-show="app.isMobile()">add_circle_outline</md-icon>
            <span ng-hide="app.isMobile()">Add a new Role</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="role.loadRoles()">
    <md-content layout="row" class="md-mt2-zeta-theme md-hue-1" layout-align="center center">
        <md-card flex-gt-md="70" flex="100">
            <md-table-container>
                <table md-table>
                    <thead md-head>
                        <tr md-row>
                            <th md-column></th>
                            <th md-column class="md-table-header-override-whitetext">ID</th>
                            <th md-column class="md-table-header-override-whitetext">Slug</th>
                            <th md-column class="md-table-header-override-whitetext">Name</th>
                        </tr>
                    </thead>

                    <tbody md-body>
                        <tr md-row ng-repeat="record in role.roles track by $index">
                            <td md-cell>
                                <div layout="row" layout-align="center center">
                                    <md-button class="md-icon-button" ng-href="@{{ role.editUrl + record.id }}" target="_self" aria-label="Edit">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                        <md-tooltip md-direction="bottom">Edit</md-tooltip>
                                    </md-button>
                                </div>
                            </td>
                            <td md-cell>@{{ record.id }}</td>
                            <td md-cell>@{{ record.slug }}</td>
                            <td md-cell>@{{ record.name }}</td>
                    </tbody>
                </table>
            </md-table-container>
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
    <script src="js/role.js"></script>
@stop
