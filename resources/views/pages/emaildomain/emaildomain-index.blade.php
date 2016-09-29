@extends( 'layout.default' )

@section( 'title' , 'ISP Domain List' )

@section ( 'angular-controller' , 'ng-controller="EmailDomainController as emailDomain"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('isp.add'))
        <md-button ng-click="emailDomain.viewAdd()" aria-label="Add ISP Domain">
            <md-icon ng-show="app.isMobile()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-hide="app.isMobile()">Add ISP Domain</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="emailDomain.loadAccounts()">
        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card flex-gt-md="50" flex="50">
                <md-table-container>
                    <table md-table md-progress="emailDomain.queryPromise">
                        <thead md-head md-order="emailDomain.sort" md-on-reorder="emailDomain.loadAccounts">
                        <tr md-row>
                            <th md-column  class="mt2-cell-left-padding"></th>
                            <th md-column md-order-by="domain_name" class="md-table-header-override-whitetext mt2-cell-left-padding">ISP Domain Name</th>
                            <th md-column md-order-by="domain_group" class="md-table-header-override-whitetext mt2-cell-left-padding">ISP Group</th>
                        </tr>
                        </thead>
                        <tbody md-body>
                        <tr md-row ng-repeat="record in emailDomain.accounts track by $index">
                            <td md-cell>
                                <div layout="row" layout-align="left left">
                                    <md-button class="md-raised"
                                               ng-class="{'md-icon-button mt2-icon-button-xs' : app.isMobile() , 'mt2-button-xs' : !app.isMobile() }"
                                               ng-href="@{{ '/isp/edit/' + record.id }}" target="_self">
                                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-hide="app.isMobile()"> Edit</span>
                                    </md-button>
                                </div>
                            </td>
                            <td md-cell>
                                @{{ record.domain_name }}
                            </td>
                            <td md-cell>@{{ record.domain_group }}</td>
                        </tr>
                        </tbody>
                    </table>
                </md-table-container>

                <md-content class="md-mt2-zeta-theme md-hue-2">
                    <md-table-pagination md-limit="emailDomain.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="emailDomain.currentPage" md-total="@{{emailDomain.accountTotal}}" md-on-paginate="emailDomain.loadAccounts" md-page-select></md-table-pagination>
                </md-content>
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/emailDomain.js"></script>
@stop