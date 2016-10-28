@extends( 'bootstrap.layout.default' )

@section( 'title' , 'MT2 Registrar List' )

@section ( 'angular-controller' , 'ng-controller="RegistrarController as registrar"' )

@section ( 'page-menu' )
    @if (Sentinel::hasAccess('registrar.add'))
        <li><a ng-click="registrar.viewAdd()">Add Registrar</a></li>
    @endif
@stop


@section( 'content' )
    <div ng-init="registrar.loadAccounts()">
                <md-table-container>
                    <table md-table md-progress="registrar.queryPromise">
                        <thead md-head md-order="registrar.sort" md-on-reorder="registrar.loadAccounts">
                        <tr md-row>
                            <th md-column></th>
                            <th md-column md-order-by="status" class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                            <th md-column md-order-by="name" class="md-table-header-override-whitetext mt2-cell-left-padding">Registrar Name</th>
                            <th md-column md-order-by="username" class="md-table-header-override-whitetext">Username</th>
                            <th md-column md-order-by="contact_name" class="md-table-header-override-whitetext">Contact Name</th>
                            <th md-column md-order-by="contact_email" class="md-table-header-override-whitetext">Contact Email</th>
                            <th md-column md-order-by="phone_number" class="md-table-header-override-whitetext">Phone</th>
                            <th md-column md-order-by="entity_name" class="md-table-header-override-whitetext">Entity Name</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="record in registrar.accounts track by $index">
                            <td md-cell>
                                <div layout="row" layout-align="center center">
                                    <md-button class="md-icon-button" ng-href="@{{ '/registrar/edit/' + record.id }}" target="_self" aria-label="Edit">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                        <md-tooltip md-direction="bottom">Edit</md-tooltip>
                                    </md-button>
                                    <md-button ng-if="record.status == 1" class="md-icon-button" ng-click="registrar.toggle( record.id , 0 )" aria-label="Deactivate">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">pause</md-icon>
                                        <md-tooltip md-direction="bottom">Deactivate</md-tooltip>
                                    </md-button>
                                    <md-button ng-if="record.status == 0" class="md-icon-button" ng-click="registrar.toggle(record.id, 1 )" aria-label="Activate">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">play_arrow</md-icon>
                                        <md-tooltip md-direction="bottom">Activate</md-tooltip>
                                    </span>
                                </md-button>
                            </td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.status == 1 , 'mt2-bg-danger' : record.status == 0 }">
                                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
                            </td>
                            <td md-cell class="mt2-cell-left-padding">@{{ record.name }}</td>
                            <td md-cell>@{{ record.username }}</td>
                            <td md-cell>@{{ record.contact_name }}</td>
                            <td md-cell>@{{ record.contact_email }}</td>
                            <td md-cell>@{{ record.phone_number }}</td>
                            <td md-cell>@{{ record.entity_name }}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8">
                                <md-content class="md-mt2-zeta-theme md-hue-2">
                                    <md-table-pagination md-limit="registrar.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="registrar.currentPage" md-total="@{{registrar.accountTotal}}" md-on-paginate="registrar.loadAccounts" md-page-select></md-table-pagination>
                                </md-content>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </md-table-container>

    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/registrar/RegistrarController.js',
                'resources/assets/js/bootstrap/registrar/RegistrarApiService.js'],'js','pageLevel') ?>
