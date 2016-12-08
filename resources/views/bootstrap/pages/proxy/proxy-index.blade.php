@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Proxy List' )

@section ( 'angular-controller' , 'ng-controller="ProxyController as proxy"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('proxy.add'))
        <li> <a ng-href="/proxy/create" target="_self" aria-label="Add Proxy">Add Proxy</a>
        </li>
    @endif
@stop

@section( 'content' )
    <div ng-init="proxy.loadAccounts()">
                <md-table-container>
                    <table md-table md-progress="proxy.queryPromise">
                        <thead md-head md-order="proxy.sort" md-on-reorder="proxy.loadAccounts" class="mt2-theme-thead">
                        <tr md-row>
                            <th md-column class="mt2-table-btn-column"></th>
                            <th md-column md-order-by="status" class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                            <th md-column md-order-by="name" class="md-table-header-override-whitetext mt2-cell-left-padding">Proxy Name</th>
                            <th md-column md-order-by="provider_name" class="md-table-header-override-whitetext">Provider Name</th>
                            <th md-column class="md-table-header-override-whitetext">IPs</th>
                            <th md-column class="md-table-header-override-whitetext">ESP Accounts</th>
                            <th md-column class="md-table-header-override-whitetext">ISPs</th>
                            <th md-column class="md-table-header-override-whitetext">DBA</th>
                            <th md-column class="md-table-header-override-whitetext">Notes</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="record in proxy.accounts track by $index">
                            <td md-cell class="mt2-table-btn-column">
                                <div layout="row" layout-align="center center">
                                    @if (Sentinel::hasAccess('api.proxy.destroy'))
                                        <md-icon  ng-click="proxy.delete( record.id )" aria-label="Delete Record"
                                                  md-font-set="material-icons" class="mt2-icon-black"
                                                  data-toggle="tooltip" data-placement="bottom" title="Delete Record">delete</md-icon>
                                    @endif
                                    <a aria-label="Edit" ng-href="@{{ '/proxy/edit/' + record.id }}" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                    </a>
                                    <md-icon ng-if="record.status == 1" ng-click="proxy.toggle( record.id , 0 )"
                                            aria-label="Deactivate" data-toggle="tooltip" data-placement="bottom" title="Deactivate"
                                            md-font-set="material-icons" class="mt2-icon-black">pause</md-icon>
                                    <md-icon ng-if="record.status == 0" ng-click="proxy.toggle( record.id , 1 )"
                                            aria-label="Activate" data-toggle="tooltip" data-placement="bottom" title="Activate"
                                            md-font-set="material-icons" class="mt2-icon-black">play_arrow</md-icon>
                                </div>
                            </td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'bg-success' : record.status == 1 , 'bg-danger' : record.status == 0 }">
                                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
                            </td>
                            <td md-cell class="mt2-cell-left-padding" nowrap>@{{ record.name }}</td>
                            <td md-cell>@{{ record.provider_name }}</td>
                            <td md-cell nowrap><p ng-repeat="value in record.ip_addresses.split(',')" nowrap>@{{ value }}</p></td>
                            <td md-cell nowrap>@{{ record.esp_account_names }}</td>
                            <td md-cell nowrap>@{{ record.isp_names }}</td>
                            <td md-cell nowrap>@{{ record.dba_name }}</td>
                            <td md-cell nowrap>@{{ record.notes }}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="9">
                                    <md-content class="md-mt2-zeta-theme md-hue-2">
                                        <md-table-pagination md-limit="proxy.paginationCount" md-limit-options="proxy.paginationOptions" md-page="proxy.currentPage" md-total="@{{proxy.accountTotal}}" md-on-paginate="proxy.loadAccounts" md-page-select></md-table-pagination>
                                    </md-content>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </md-table-container>

    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/proxy/ProxyController.js',
                'resources/assets/js/bootstrap/proxy/ProxyApiService.js'],'js','pageLevel') ?>
