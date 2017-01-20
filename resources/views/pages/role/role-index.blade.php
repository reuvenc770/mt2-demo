@extends( 'layout.default' )

@section( 'title' , 'Security Roles' )

@section( 'navRoleClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="roleController as role"')
@section( 'page-menu' )
    @if (Sentinel::hasAccess('role.add'))
        <li><a ng-href="/role/create" target="_self">Add New Role</a></li>
    @endif
@stop

@section( 'content' )
<div ng-init="role.loadRoles()">
        <md-table-container>
            <table md-table>
                <thead md-head class="mt2-theme-thead">
                    <tr md-row>
                        <th md-column class="mt2-table-btn-column"></th>
                        <th md-column class="md-table-header-override-whitetext">ID</th>
                        <th md-column class="md-table-header-override-whitetext">Slug</th>
                        <th md-column class="md-table-header-override-whitetext">Name</th>
                    </tr>
                </thead>

                <tbody md-body>
                    <tr md-row ng-repeat="record in role.roles track by $index">
                        <td md-cell class="mt2-table-btn-column">
                            <div layout="row" layout-align="center center">
                                <a ng-href="@{{ role.editUrl + record.id }}" target="_self" aria-label="Edit" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                </a>
                            </div>
                        </td>
                        <td md-cell>@{{ record.id }}</td>
                        <td md-cell>@{{ record.slug }}</td>
                        <td md-cell nowrap>@{{ record.name }}</td>
                </tbody>
            </table>
        </md-table-container>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/role/RoleController.js',
                'resources/assets/js/role/RoleApiService.js'],'js','pageLevel') ?>