@extends( 'bootstrap.layout.default' )

@section( 'title' , 'MT2 User List' )

@section( 'angular-controller', 'ng-controller="userController as user"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('user.add'))
        <li><a ng-click="user.viewAdd()">Add User Account</a></li>
    @endif
@stop

@section( 'content' )
    <div ng-init="user.loadAccounts()">
            <md-card>
                <md-table-container>
                    <table md-table>
                        <thead md-head>
                            <tr md-row>
                                <th md-column></th>
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
                                <td md-cell>
                                    <div layout="row" layout-align="center center">
                                        <md-button class="md-icon-button" ng-href="@{{ user.editUrl + record.id }}" target="_self" aria-label="Edit">
                                            <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon>
                                            <md-tooltip md-direction="bottom">Edit</md-tooltip>
                                        </md-button>
                                    </div>
                                </td>
                                <td md-cell>@{{ record.id }}</td>
                                <td md-cell>@{{ record.email }}</td>
                                <td md-cell>@{{ record.username }}</td>
                                <td md-cell>@{{ record.first_name }}</td>
                                <td md-cell>@{{ record.last_name }}</td>
                                <td md-cell>
                                    @{{ record.roles.join(', ') }}
                                </td>
                                <td md-cell ng-bind="record.activations.length > 0 ? 'Active' : 'Inactive'"></td>
                                <td md-cell>@{{ record.last_login }}</td>
                            </tr>
                        </tbody>
                    </table>
                </md-table-container>
            </md-card>
    </div>

@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/user/UserController.js',
                'resources/assets/js/bootstrap/user/UserApiService.js'],'js','pageLevel') ?>
