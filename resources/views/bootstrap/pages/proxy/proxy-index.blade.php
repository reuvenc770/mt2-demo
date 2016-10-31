@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Proxy List' )

@section ( 'angular-controller' , 'ng-controller="ProxyController as proxy"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('proxy.add'))
        <li> <a ng-click="proxy.viewAdd()" aria-label="Add Proxy">Add Proxy</a>
        </li>
    @endif
@stop

@section( 'content' )
    <div ng-init="proxy.loadAccounts()">
            <md-card>
                <md-table-container>
                    <table md-table md-progress="proxy.queryPromise">
                        <thead md-head md-order="proxy.sort" md-on-reorder="proxy.loadAccounts">
                        <tr md-row>
                            <th md-column></th>
                            <th md-column md-order-by="status" class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                            <th md-column md-order-by="name" class="md-table-header-override-whitetext mt2-cell-left-padding">Proxy Name</th>
                            <th md-column md-order-by="provider_name" class="md-table-header-override-whitetext">Provider Name</th>
                            <th md-column class="md-table-header-override-whitetext">IPs</th>
                            <th md-column class="md-table-header-override-whitetext">ESP Accounts</th>
                            <th md-column class="md-table-header-override-whitetext">ISPs</th>
                            <th md-column class="md-table-header-override-whitetext">Notes</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="record in proxy.accounts track by $index" ng-class="{ 'mt2-bg-success' : record.status == 1 , 'mt2-bg-danger' : record.status == 0 }">
                            <td md-cell>
                                <div layout="row" layout-align="center center">
                                    <md-button class="md-icon-button" aria-label="Edit" ng-href="@{{ '/proxy/edit/' + record.id }}" target="_self">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                        <md-tooltip md-direction="bottom">Edit</md-tooltip>
                                    </md-button>
                                    <md-button ng-if="record.status == 1" class="md-icon-button" ng-click="proxy.toggle( record.id , 0 )" aria-label="Deactivate">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">pause</md-icon>
                                        <md-tooltip md-direction="bottom">Deactivate</md-tooltip>
                                    </md-button>
                                    <md-button ng-if="record.status == 0" class="md-icon-button" ng-click="proxy.toggle( record.id , 1 )" aria-label="Activate">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">play_arrow</md-icon>
                                        <md-tooltip md-direction="bottom">Activate</md-tooltip>
                                    </md-button>
                                </div>
                            </td>
                            <td md-cell class="mt2-table-cell-center">
                                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
                            </td>
                            <td md-cell class="mt2-cell-left-padding">@{{ record.name }}</td>
                            <td md-cell>@{{ record.provider_name }}</td>
                            <td md-cell><p ng-repeat="value in record.ip_addresses.split(',')" >@{{ value }}</p></td>
                            <td md-cell>@{{ record.esp_account_names }}</td>
                            <td md-cell>@{{ record.isp_names }}</td>
                            <td md-cell>@{{ record.notes }}</td>
                        </tr>
                        </tbody>
                    </table>
                </md-table-container>

                <md-content class="md-mt2-zeta-theme md-hue-2">
                    <md-table-pagination md-limit="proxy.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="proxy.currentPage" md-total="@{{proxy.accountTotal}}" md-on-paginate="proxy.loadAccounts" md-page-select></md-table-pagination>
                </md-content>
            </md-card>
    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/proxy/ProxyController.js',
                'resources/assets/js/bootstrap/proxy/ProxyApiService.js'],'js','pageLevel') ?>
