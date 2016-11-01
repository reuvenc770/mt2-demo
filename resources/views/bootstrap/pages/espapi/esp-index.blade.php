
@extends( 'bootstrap.layout.default' )

@section( 'title' , 'ESP API Accounts' )

@section( 'navEspClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="espController as esp"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('espapi.add'))
        <li><a ng-href="/espapi/create" target="_self">Add ESP API Account</a></li>
    @endif
@stop

@section( 'content' )
<div ng-init="esp.loadAccounts()">
        <md-table-container>
            <table md-table md-progress="esp.queryPromise">
                <thead md-head md-order="esp.sort" md-on-reorder="esp.loadAccounts">
                    <tr md-row>
                        <th md-column class="mt2-table-btn-column"></th>
                        <th md-column md-order-by="id" class="md-table-header-override-whitetext">ID</th>
                        <th md-column md-order-by="account_name" class="md-table-header-override-whitetext">ESP</th>
                        <th md-column md-order-by="key_1" class="md-table-header-override-whitetext">Key 1</th>
                        <th md-column md-order-by="key_2" class="md-table-header-override-whitetext">Key 2</th>
                        <th md-column md-order-by="created_at" class="md-table-header-override-whitetext">Created</th>
                        <th md-column md-order-by="updated_at" class="md-table-header-override-whitetext">Updated</th>
                    </tr>
                </thead>

                <tbody md-body>
                    <tr md-row ng-repeat="record in esp.accounts track by $index">
                        <td md-cell class="mt2-table-btn-column">
                            <div layout="row" layout-align="center center">
                                <a ng-href="@{{ '/espapi/edit/' + record.id }}" aria-label="Edit" target="_self"data-toggle="tooltip" data-placement="bottom" title="Edit">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                </a>
                            </div>
                        </td>
                        <td md-cell>@{{ record.id }}</td>
                        <td md-cell>@{{ record.account_name }}</td>
                        <td md-cell>@{{ record.key_1 }}</td>
                        <td md-cell>@{{ record.key_2 }}</td>
                        <td md-cell nowrap ng-bind="::app.formatDate( record.created_at )"></td>
                        <td md-cell nowrap ng-bind="::app.formatDate( record.updated_at )"></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7">
                            <md-content class="md-mt2-zeta-theme md-hue-2">
                                <md-table-pagination md-limit="esp.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="esp.currentPage" md-total="@{{esp.accountTotal}}" md-on-paginate="esp.loadAccounts" md-page-select></md-table-pagination>
                            </md-content>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </md-table-container>


</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/espapi/EspController.js',
                'resources/assets/js/bootstrap/espapi/EspApiService.js'],'js','pageLevel') ?>
