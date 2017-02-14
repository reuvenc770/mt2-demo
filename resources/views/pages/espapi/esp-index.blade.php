
@extends( 'layout.default' )

@section( 'title' , 'ESP API Accounts' )

@section( 'navEspClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="espController as esp"' )
@section( 'cacheTag' , 'EspAccount' )
@section( 'page-menu' )
    @if (Sentinel::hasAccess('espapi.add'))
        <li><a ng-href="/espapi/create" target="_self">Add ESP API Account</a></li>
    @endif
@stop

@section( 'content' )
<div ng-init="esp.loadAccounts()">
        <md-table-container>
            <table md-table md-progress="esp.queryPromise">
                <thead md-head md-order="esp.sort" md-on-reorder="esp.loadAccounts" class="mt2-theme-thead">
                    <tr md-row>
                        <th md-column class="mt2-table-btn-column"></th>
                        <th md-column md-order-by="status" class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                        <th md-column md-order-by="account_name" class="md-table-header-override-whitetext mt2-cell-left-padding">ESP</th>
                        <th md-column class="md-table-header-override-whitetext">Custom ID</th>
                        <th md-column md-order-by="key_1" class="md-table-header-override-whitetext">Key 1</th>
                        <th md-column md-order-by="key_2" class="md-table-header-override-whitetext">Key 2</th>
                        <th md-column md-order-by="created_at" class="md-table-header-override-whitetext">Created</th>
                        <th md-column md-order-by="updated_at" class="md-table-header-override-whitetext">Updated</th>
                    </tr>
                </thead>

                <tbody md-body>
                    <tr md-row ng-repeat="record in esp.accounts track by $index" ng-class="{  'bg-danger' : record.enable_suppression == 0 }">
                        <td md-cell class="mt2-table-btn-column">
                            <div layout="row" layout-align="center center">
                                <a ng-href="@{{ '/espapi/edit/' + record.id }}" target="_self" aria-label="Edit" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                </a>
                                <md-icon ng-if="record.status == 1" ng-click="esp.toggle( record.id , 2 )" aria-label="Deactivate 30 Days from now"
                                         md-font-set="material-icons" class="mt2-icon-black"
                                         data-toggle="tooltip" data-placement="bottom" title="Deactivate 30 Days from now">stop</md-icon>
                                <md-icon ng-if="record.status == 0" ng-click="esp.toggle(record.id, 1 )" aria-label="Activate"
                                         md-font-set="material-icons" class="mt2-icon-black"
                                         data-toggle="tooltip" data-placement="bottom" title="Activate">play_arrow</md-icon>
                                <md-icon ng-if="record.enable_suppression" ng-click="esp.toggleSuppression(record.id, 0 )" aria-label="Disable Suppression"
                                         md-font-set="material-icons" class="mt2-icon-black"
                                         data-toggle="tooltip" data-placement="bottom" title="Activate">pause_circle_outline</md-icon>
                                <md-icon ng-if="!record.enable_suppression" ng-click="esp.toggleSuppression(record.id, 1 )" aria-label="Enable Suppression"
                                         md-font-set="material-icons" class="mt2-icon-black"
                                         data-toggle="tooltip" data-placement="bottom" title="Activate">play_circle_outline</md-icon>
                            </div>
                            </div>
                        </td>
                        <td md-cell ng-switch="record.status" class="mt2-table-cell-center" ng-class="{ 'bg-success' : record.status == 1 , 'bg-danger' : record.status == 0 }">

                                <span ng-switch-when="1">Active</span>
                                <ANY ng-switch-when="0">Inactive</ANY>
                                <ANY ng-switch-default>Pending Deactivation</ANY>

                        </td>
                        <td class="mt2-cell-left-padding" md-cell>@{{ record.account_name }}</td>
                        <td md-cell>@{{ record.custom_id }}</td>
                        <td md-cell>@{{ record.key_1 }}</td>
                        <td md-cell>@{{ record.key_2 }}</td>
                        <td md-cell nowrap ng-bind="::app.formatDate( record.created_at )"></td>
                        <td md-cell nowrap ng-bind="::app.formatDate( record.updated_at )"></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8">
                            <md-content class="md-mt2-zeta-theme">
                                <md-table-pagination md-limit="esp.paginationCount" md-limit-options="esp.paginationOptions" md-page="esp.currentPage" md-total="@{{esp.accountTotal}}" md-on-paginate="esp.loadAccounts" md-page-select></md-table-pagination>
                            </md-content>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </md-table-container>


</div>
@stop

<?php Assets::add(
        ['resources/assets/js/espapi/EspController.js',
                'resources/assets/js/espapi/EspApiService.js'],'js','pageLevel') ?>
