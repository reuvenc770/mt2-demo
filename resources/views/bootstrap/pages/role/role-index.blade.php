@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Security Roles' )

@section( 'navRoleClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="roleController as role"')

@section( 'page-menu' )
    @if (Sentinel::hasAccess('role.add'))
        <li><a ng-click="role.viewAdd()">Add New Role</a></li>
    @endif
@stop

@section( 'content' )
<div ng-init="role.loadRoles()">
    <md-card>
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
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/role/RoleController.js',
                'resources/assets/js/bootstrap/role/RoleApiService.js'],'js','pageLevel') ?>