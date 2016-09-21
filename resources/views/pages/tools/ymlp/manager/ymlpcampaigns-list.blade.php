
@extends( 'layout.default' )

@section( 'title' , 'YMLP Campaigns' )

@section( 'angular-controller' , 'ng-controller="ymlpCampaignController as ymlp"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('ymlpcampaign.add'))
        <md-button ng-click="ymlp.viewAdd()" aria-label="Add YMLP Campaign">
            <md-icon ng-show="app.isMobile()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-hide="app.isMobile()">Add YMLP Campaign</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="ymlp.loadCampaigns()">
        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card>
                <md-table-container>
                    <table md-table md-progress="ymlp.queryPromise">
                        <thead md-head md-order="ymlp.sort" md-on-reorder="ymlp.loadCampaigns">
                            <tr md-row>
                                <th md-column md-numeric></th>
                                <th md-column md-order-by="id" class="md-table-header-override-whitetext" md-numeric>ID</th>
                                <th md-column md-order-by="sub_id" class="md-table-header-override-whitetext">Campaign Name</th>
                                <th md-column md-order-by="esp_account_id" class="md-table-header-override-whitetext" md-numeric>Esp Account ID</th>
                                <th md-column md-order-by="date" class="md-table-header-override-whitetext">Date</th>
                            </tr>
                        </thead>

                        <tbody md-body>
                            <tr md-row ng-repeat="record in ymlp.campaigns track by $index">
                                <td md-cell>
                                    <md-button class="md-raised"
                                                ng-class="{'md-icon-button mt2-icon-button-xs' : app.isMobile() , 'mt2-button-xs' : !app.isMobile() }"
                                                ng-href="@{{'/ymlp/ymlp-campaign/edit/' + record.id}}" target="_self">
                                       <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Edit</span>
                                    </md-button>
                                </td>
                                <td md-cell>@{{ record.id }}</td>
                                <td md-cell>@{{ record.sub_id }}</td>
                                <td md-cell>@{{ record.esp_account_id }}</td>
                                <td md-cell>@{{ record.date }}</td>
                            </tr>
                        </tbody>
                    </table>
                </md-table-container>

                <md-content class="md-mt2-zeta-theme md-hue-2">
                    <md-table-pagination md-limit="ymlp.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="ymlp.currentPage" md-total="@{{ymlp.campaignTotal}}" md-on-paginate="ymlp.loadAccounts" md-page-select></md-table-pagination>
                </md-content>
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/ymlpcampaign.js"></script>
@stop
