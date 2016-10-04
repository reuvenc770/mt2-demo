
@extends( 'layout.default' )

@section( 'title' , 'Domain List' )

@section( 'angular-controller' , 'ng-controller="domainController as domain"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('domain.add'))
        <md-button ng-click="domain.viewAdd()" aria-label="Add Domain">
            <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-show="app.isMobile()">add_circle_outline</md-icon>
            <span ng-hide="app.isMobile()">Add Domain</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="domain.loadAccounts()">
        <md-content layout="row" layout-align="center" class="md-mt2-zeta-theme md-hue-1">
            <md-card flex-gt-md="70" flex="100">
                <md-table-container>
                    <table md-table md-progress="domain.queryPromise">
                        <thead md-head>
                        <tr md-row>
                            <th md-column></th>
                            <th md-column class="md-table-header-override-whitetext">ESP</th>
                            <th md-column class="md-table-header-override-whitetext">ESP Account</th>
                            <th md-column class="md-table-header-override-whitetext">Number of Domains</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="record in domain.accounts track by $index">
                            <td md-cell>
                                <div layout="row" layout-align="center center">
                                    <md-button target="_self" class="md-icon-button" aria-label="View"
                                                ng-href="@{{ '/domain/create/?name=' + record.esp_name + '&espId=' + record.esp_account_id + '&espAccountName=' + record.account_name }}">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">view_list</md-icon>
                                     <md-tooltip md-direction="bottom">View</md-tooltip>
                                    </md-button>
                                </div>
                            </td>
                            <td md-cell>@{{ record.esp_name }}</td>
                            <td md-cell>@{{ record.account_name }}</td>
                            <td md-cell>@{{ record.domain_numbers }}</td>
                        </tr>
                        </tbody>
                    </table>
                </md-table-container>

                <md-content class="md-mt2-zeta-theme md-hue-2">
                    <md-table-pagination md-limit="domain.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="domain.currentPage" md-total="@{{domain.accountTotal}}" md-on-paginate="domain.loadAccounts" md-page-select></md-table-pagination>
                </md-content>
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/domain.js"></script>
@stop
