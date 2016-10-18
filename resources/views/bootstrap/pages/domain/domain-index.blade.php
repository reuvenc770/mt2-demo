
@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Domain List' )

@section( 'angular-controller' , 'ng-controller="domainController as domain"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('domain.add'))
        <li><a ng-click="domain.viewAdd()" aria-label="Add Domain">Add Domain</a>
        </li>
    @endif
@stop

@section( 'content' )
    @include( 'bootstrap.pages.domain.domain-search' )
    <div ng-init="domain.loadAccounts()">
            <md-card>
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
                                    <md-button target="_self" class="md-raised md-accent"
                                                ng-class="{'md-icon-button mt2-icon-button-xs' : app.isMobile() , 'mt2-button-xs' : !app.isMobile() }"
                                                ng-href="@{{ '/domain/listview/?name=' + record.esp_name + '&espId=' + record.esp_account_id + '&espAccountName=' + record.account_name }}">
                                     <md-icon md-svg-icon="img/icons/ic_view_list_white_24px.svg"></md-icon><span ng-hide="app.isMobile()"> Account View</span>
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
    </div>
@stop


<?php Assets::add(
        ['resources/assets/js/bootstrap/domain/DomainController.js',
                'resources/assets/js/bootstrap/domain/DomainApiService.js'],'js','pageLevel') ?>