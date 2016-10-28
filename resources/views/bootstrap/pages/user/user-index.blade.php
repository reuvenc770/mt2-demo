@extends( 'bootstrap.layout.default' )

@section( 'title' , 'MT2 User List' )

@section( 'angular-controller', 'ng-controller="userController as user"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('user.add'))
        <li><a ng-href="/user/create" target="_self">Add User Account</a></li>
    @endif
@stop

@section( 'content' )
    <div ng-init="user.loadAccounts()">
                <md-table-container>
                    <table md-table>
                        <thead md-head>
                            <tr md-row>
                                <th md-column class="mt2-table-btn-column"></th>
                                <th md-column class="md-table-header-override-whitetext" md-numeric>ID</th>
                                <th md-column class="md-table-header-override-whitetext">Email</th>
                                <th md-column class="md-table-header-override-whitetext">Username</th>
                                <th md-column class="md-table-header-override-whitetext">First Name</th>
                                <th md-column class="md-table-header-override-whitetext">Last Name</th>
                                <th md-column class="md-table-header-override-whitetext">Roles</th>
                                <th md-column class="md-table-header-override-whitetext">Status</th>
                                <th md-column class="md-table-header-override-whitetext">Last Login</th>
                            </tr>
                        </thead>

                        <tbody md-body>
                            <tr md-row ng-repeat="record in user.accounts track by $index">
                                <td md-cell class="mt2-table-btn-column">
                                    <div layout="row" layout-align="center center">
                                        <a ng-href="@{{ user.editUrl + record.id }}" target="_self" aria-label="Edit" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                            <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                        </a>
                                    </div>
                                </td>
                                <td md-cell>@{{ record.id }}</td>
                                <td md-cell>@{{ record.email }}</td>
                                <td md-cell>@{{ record.username }}</td>
                                <td md-cell>@{{ record.first_name }}</td>
                                <td md-cell>@{{ record.last_name }}</td>
                                <td md-cell nowrap>
                                    @{{ record.roles.join(', ') }}
                                </td>
                                <td md-cell ng-bind="record.activations.length > 0 ? 'Active' : 'Inactive'"></td>
                                <td md-cell nowrap>@{{ record.last_login ? app.formatDate( record.last_login ) : '' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </md-table-container>
    </div>

@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/user/UserController.js',
                'resources/assets/js/bootstrap/user/UserApiService.js'],'js','pageLevel') ?>
