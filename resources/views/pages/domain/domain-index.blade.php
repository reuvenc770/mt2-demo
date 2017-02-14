
@extends( 'layout.default' )

@section( 'title' , 'Domain List' )

@section( 'angular-controller' , 'ng-controller="domainController as domain"' )
@section( 'cacheTag' , 'Domain' )
@section( 'page-menu' )
    @if (Sentinel::hasAccess('domain.add'))
        <li><a ng-href="/domain/create" target="_self" aria-label="Add Domain">Add Domain</a>
        </li>
    @endif
@stop

@section( 'content' )
    @include( 'pages.domain.domain-search' )
    <div ng-init="domain.loadAccounts()">
                <md-table-container>
                    <table md-table md-progress="domain.queryPromise">
                        <thead md-head class="mt2-theme-thead">
                        <tr md-row>
                            <th md-column class="mt2-table-btn-column"></th>
                            <th md-column class="md-table-header-override-whitetext">ESP</th>
                            <th md-column class="md-table-header-override-whitetext">ESP Account</th>
                            <th md-column class="md-table-header-override-whitetext">Number of Domains</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="record in domain.accounts track by $index">
                            <td md-cell class="mt2-table-btn-column">
                                <div layout="row" layout-align="center center">
                                    <a ng-href="@{{ '/domain/listview/?name=' + record.esp_name + '&espId=' + record.esp_account_id + '&espAccountName=' + record.account_name }}"
                                    target="_self" aria-label="Account View" data-toggle="tooltip" data-placement="bottom" title="Account View">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">view_list</md-icon>
                                    </a>
                                </div>
                            </td>
                            <td md-cell>@{{ record.esp_name }}</td>
                            <td md-cell>@{{ record.account_name }}</td>
                            <td md-cell>@{{ record.domain_numbers }}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <md-content class="md-mt2-zeta-theme">
                                        <md-table-pagination md-limit="domain.paginationCount" md-limit-options="domain.paginationOptions" md-page="domain.currentPage" md-total="@{{domain.accountTotal}}" md-on-paginate="domain.loadAccounts" md-page-select></md-table-pagination>
                                    </md-content>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </md-table-container>
    </div>
@stop


<?php Assets::add(
        ['resources/assets/js/domain/DomainController.js',
                'resources/assets/js/domain/DomainApiService.js'],'js','pageLevel') ?>