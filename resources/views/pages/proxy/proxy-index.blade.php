@extends( 'layout.default' )

@section( 'title' , 'MT2 Proxy List' )

@section ( 'angular-controller' , 'ng-controller="ProxyController as proxy"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('proxy.add'))
        <md-button ng-click="proxy.viewAdd()" aria-label="Add Proxy">
            <md-icon ng-show="app.isMobile()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-hide="app.isMobile()">Add Proxy</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="proxy.loadAccounts()">
        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
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
                            <th md-column class="md-table-header-override-whitetext">Esp Accounts</th>
                            <th md-column class="md-table-header-override-whitetext">Isps</th>
                            <th md-column class="md-table-header-override-whitetext">Notes</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="record in proxy.accounts track by $index">
                            <td md-cell>
                                <div layout="row" layout-align="center center">
                                    <md-button class="md-raised" ng-class="{'md-icon-button mt2-icon-button-xs' : app.isMobile() , 'mt2-button-xs' : !app.isMobile() }" ng-href="@{{ '/proxy/edit/' + record.id }}" target="_self">
                                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-hide="app.isMobile()"> Edit</span>
                                    </md-button>
                                    <md-button ng-if="record.status == 1" class="md-raised md-accent mt2-button-xs" ng-click="proxy.toggle( record.id , 0 )">Deactivate</md-button>
                                    <md-button ng-if="record.status == 0" class="md-raised md-accent mt2-button-xs" ng-click="proxy.toggle( record.id , 1 )">Activate</md-button>
                                </div>
                            </td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.status == 1 , 'mt2-bg-danger' : record.status == 0 }">
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
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/proxy.js"></script>
@stop
