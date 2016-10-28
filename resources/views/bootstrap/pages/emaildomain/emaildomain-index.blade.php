@extends( 'bootstrap.layout.default' )

@section( 'title' , 'ISP Domain List' )

@section ( 'angular-controller' , 'ng-controller="EmailDomainController as emailDomain"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('isp.add'))
        <li ng-click="emailDomain.viewAdd()" ><a href="">Add ISP Domain</a></li>
    @endif
@stop

@section( 'content' )
    <div ng-init="emailDomain.loadAccounts()">
                <md-table-container>
                    <table md-table md-progress="emailDomain.queryPromise">
                        <thead md-head md-order="emailDomain.sort" md-on-reorder="emailDomain.loadAccounts">
                        <tr md-row>
                            <th md-column></th>
                            <th md-column md-order-by="domain_name" class="md-table-header-override-whitetext">ISP Domain Name</th>
                            <th md-column md-order-by="domain_group" class="md-table-header-override-whitetext">ISP Group</th>
                        </tr>
                        </thead>
                        <tbody md-body>
                        <tr md-row ng-repeat="record in emailDomain.accounts track by $index">
                            <td md-cell>
                                <div layout="row" layout-align="center center">
                                    <md-button class="md-icon-button" ng-href="@{{ '/isp/edit/' + record.id }}" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon>
                                        <md-tooltip md-direction="bottom">Edit</md-tooltip>
                                    </md-button>
                                </div>
                            </td>
                            <td md-cell>
                                @{{ record.domain_name }}
                            </td>
                            <td md-cell>@{{ record.domain_group }}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">
                                    <md-content class="md-mt2-zeta-theme md-hue-2">
                                        <md-table-pagination md-limit="emailDomain.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="emailDomain.currentPage" md-total="@{{emailDomain.accountTotal}}" md-on-paginate="emailDomain.loadAccounts" md-page-select></md-table-pagination>
                                    </md-content>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </md-table-container>
    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/emaildomain/EmailDomainController.js',
        'resources/assets/js/bootstrap/emaildomain/EmailDomainApiService.js'],'js','pageLevel') ?>