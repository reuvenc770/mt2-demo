@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Domain Group List' )

@section ( 'angular-controller' , 'ng-controller="DomainGroupController as dg"' )


@section( 'page-menu' )
    @if (Sentinel::hasAccess('ispgroup.add'))
        <li ng-click="dg.viewAdd()" ><a href="">Add Domain Group</a></li>
    @endif
@stop

@section( 'content' )
    <div ng-init="dg.loadAccounts()">
            <md-card>
                <md-table-container>
                    <table md-table md-progress="dg.queryPromise">
                        <thead md-head md-order="dg.sort" md-on-reorder="dg.loadAccounts">
                        <tr md-row>
                            <th md-column></th>
                            <th md-column md-order-by="name" class="md-table-header-override-whitetext mt2-table-header-center">ISP Group Name</th>
                            <th md-column  class="md-table-header-override-whitetext mt2-cell-left-padding">Number of Domains</th>
                            <th md-column md-order-by="country" class="md-table-header-override-whitetext">Country</th>
                            <th md-column md-order-by="status" class="md-table-header-override-whitetext">Status</th>
                        </tr>
                        </thead>
                        <tbody md-body>
                        <tr md-row ng-repeat="record in dg.accounts track by $index">
                            <td md-cell>
                                <div layout="row" layout-align="center center">
                                    <md-button class="md-raised"
                                               ng-class="{'md-icon-button mt2-icon-button-xs' : app.isMobile() , 'mt2-button-xs' : !app.isMobile() }"
                                               ng-href="@{{ '/ispgroup/edit/' + record.id }}" target="_self">
                                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-hide="app.isMobile()"> Edit</span>
                                    </md-button>
                                </div>
                            </td>
                            <td md-cell>
                                @{{ record.name }}
                            </td>
                            <td md-cell>@{{ record.domainCount }}</td>
                            <td md-cell>@{{ record.country }}</td>
                            <td md-cell>@{{ record.status }}</td>
                        </tr>
                        </tbody>
                    </table>
                </md-table-container>

                <md-content class="md-mt2-zeta-theme md-hue-2">
                    <md-table-pagination md-limit="dg.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="dg.currentPage" md-total="@{{dg.accountTotal}}" md-on-paginate="dg.loadAccounts" md-page-select></md-table-pagination>
                </md-content>
            </md-card>
    </div>
@stop


<?php Assets::add(
        ['resources/assets/js/bootstrap/domaingroup/DomainGroupController.js',
                'resources/assets/js/bootstrap/domaingroup/DomainGroupApiService.js'],'js','pageLevel') ?>