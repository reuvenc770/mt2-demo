
@extends( 'layout.default' )

@section( 'title' , 'ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="espController as esp"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('espapi.add'))
        <md-button ng-click="esp.viewAdd()" aria-label="Add ESP Account">
            <md-icon ng-show="app.isMobile()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-hide="app.isMobile()">Add ESP Account</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="esp.loadAccounts()">
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
            <md-table-container>
                <table md-table md-progress="esp.queryPromise">
                    <thead md-head md-order="esp.sort" md-on-reorder="esp.loadAccounts">
                        <tr md-row>
                            <th md-column>
                            </th>
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
                            <td md-cell>
                                <div layout="row" layout-align="center center">
                                    <md-button class="md-raised"
                                        ng-class="{'md-icon-button mt2-icon-button-xs' : app.isMobile() , 'mt2-button-xs' : !app.isMobile() }"
                                        ng-href="@{{ '/espapi/edit/' + record.id }}" target="_self">
                                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-show="!app.isMobile()"> Edit</span>
                                    </md-button>
                                </div>
                            </td>
                            <td md-cell>@{{ record.id }}</td>
                            <td md-cell>@{{ record.account_name }}</td>
                            <td md-cell>@{{ record.key_1 }}</td>
                            <td md-cell>@{{ record.key_2 }}</td>
                            <td md-cell>@{{ record.created_at }}</td>
                            <td md-cell>@{{ record.updated_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </md-table-container>

            <md-content class="md-mt2-zeta-theme md-hue-2">
                <md-table-pagination md-limit="esp.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="esp.currentPage" md-total="@{{esp.accountTotal}}" md-on-paginate="esp.loadAccounts" md-page-select></md-table-pagination>
            </md-content>

        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/espapi.js"></script>
@stop
