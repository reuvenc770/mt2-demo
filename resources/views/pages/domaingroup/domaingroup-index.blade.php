@extends( 'layout.default' )

@section( 'title' , 'ISP Group List' )

@section ( 'angular-controller' , 'ng-controller="DomainGroupController as dg"' )
@section( 'cacheTag' , 'DomainGroup' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('ispgroup.add'))
        <li><a ng-href="/ispgroup/create" target="_self">Add ISP Group</a></li>
    @endif
@stop

@section( 'content' )
    <div ng-init="dg.loadAccounts()">
                <md-table-container>
                    <table md-table md-progress="dg.queryPromise">
                        <thead md-head md-order="dg.sort" md-on-reorder="dg.loadAccounts" class="mt2-theme-thead">
                        <tr md-row>
                            <th md-column class="mt2-table-btn-column"></th>
                            <th md-column class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                            <th md-column md-order-by="name" class="md-table-header-override-whitetext mt2-cell-left-padding">ISP Group Name</th>
                            <th md-column class="md-table-header-override-whitetext">Number of Domains</th>
                            <th md-column md-order-by="country" class="md-table-header-override-whitetext">Country</th>
                        </tr>
                        </thead>
                        <tbody md-body>
                        <tr md-row ng-repeat="record in dg.accounts track by $index">
                            <td md-cell class="mt2-table-btn-column">
                                <div layout="row" layout-align="center center">
                                    <a ng-href="@{{ '/ispgroup/edit/' + record.id }}" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                    </a>
                                    <md-icon ng-if="record.status == 'Active'" ng-click="dg.toggle( record.id , 'Paused' )" md-font-set="material-icons"
                                            class="mt2-icon-black no-margin" data-toggle="tooltip" data-placement="bottom" title="Deactivate" aria-label="Deactivate">pause</md-icon>
                                    <md-icon ng-if="record.status == 'Paused' || record.status == '' " ng-click="dg.toggle( record.id , 'Active' )" md-font-set="material-icons"
                                            class="mt2-icon-black no-margin" data-toggle="tooltip" data-placement="bottom" title="Activate" aria-label="Activate">play_arrow</md-icon>
                                </div>
                            </td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'bg-success' : record.status == 'Active' , 'bg-danger' : record.status == 'Paused' || record.status == '' }" >@{{ record.status == 'Active' ? 'Active' : 'Inactive' }}</td>
                            <td md-cell class="mt2-cell-left-padding">
                                @{{ record.name }}
                            </td>
                            <td md-cell>@{{ record.domainCount }}</td>
                            <td md-cell>@{{ record.country }}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <md-content class="md-mt2-zeta-theme">
                                        <md-table-pagination md-limit="dg.paginationCount" md-limit-options="dg.paginationOptions" md-page="dg.currentPage" md-total="@{{dg.accountTotal}}" md-on-paginate="dg.loadAccounts" md-page-select></md-table-pagination>
                                    </md-content>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </md-table-container>
    </div>
@stop


<?php Assets::add(
        ['resources/assets/js/domaingroup/DomainGroupController.js',
                'resources/assets/js/domaingroup/DomainGroupApiService.js'],'js','pageLevel') ?>