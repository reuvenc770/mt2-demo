@extends( 'layout.default' )

@section( 'title' , 'Feed' )

@section( 'navFeedClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('feed.add'))
        <md-button ng-click="feed.viewAdd()">
            <md-icon ng-show="app.isMobile()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-hide="app.isMobile()">Add Feed</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="feed.loadFeeds()">
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
            <md-table-container>
                <table md-table class="mt2-table-large" md-progress="feed.queryPromise">
                    <thead md-head>
                        <tr md-row>
                            <th md-column></th>
                            <th md-column class="md-table-header-override-whitetext">Name</th>
                            <th md-column class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                            <th md-column class="md-table-header-override-whitetext mt2-table-header-center mt2-table-header-wrap">Global Suppression</th>
                            <th md-column class="md-table-header-override-whitetext mt2-table-header-center mt2-table-header-wrap">OC Check</th>
                            <th md-column class="md-table-header-override-whitetext mt2-table-header-center mt2-table-header-wrap">Group Restriction</th>
                            <th md-column class="md-table-header-override-whitetext mt2-cell-left-padding">List Owner</th>
                            <th md-column class="md-table-header-override-whitetext mt2-table-header-wrap">CAKE Sub-Affiliate</th>
                            <th md-column class="md-table-header-override-whitetext">Feed Type</th>
                            <th md-column class="md-table-header-override-whitetext">Network</th>
                            <th md-column class="md-table-header-override-whitetext">Source URL</th>
                            <th md-column class="md-table-header-override-whitetext">Source IP</th>
                            <th md-column class="md-table-header-override-whitetext">FTP URL</th>
                            <th md-column class="md-table-header-override-whitetext">FTP User</th>
                            <th md-column class="md-table-header-override-whitetext">Contact</th>
                            <th md-column class="md-table-header-override-whitetext">Email</th>
                            <th md-column class="md-table-header-override-whitetext">Address</th>
                            <th md-column class="md-table-header-override-whitetext">Phone</th>
                        </tr>
                    </thead>

                    <tbody md-body>
                        <tr md-row ng-repeat="record in feed.feeds track by $index">
                            <td md-cell>
                                <div layout="row" layout-align="center center">
                                    <md-button class="md-raised" ng-class="{'md-icon-button mt2-icon-button-xs' : app.isMobile() , 'mt2-button-xs' : !app.isMobile() }" ng-href="@{{'/feed/edit/' + record.client_id}}" target="_self">
                                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-hide="app.isMobile()"> Edit</span>
                                    </md-button>
                                </div>
                            </td>
                            <td md-cell>@{{ record.username }}</td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.status == 'A' , 'mt2-bg-warn' : record.status == 'P' , 'mt2-bg-danger' : record.status == 'D' }">
                                @{{ record.status == 'A' ? 'Active' : record.status == 'P' ? 'Paused' : 'Inactive'  }}
                            </td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.check_global_suppression == 'Y' , 'mt2-bg-danger' : record.check_global_suppression != 'Y' }">
                                @{{ record.check_global_suppression == 'Y' ? 'ON' : 'OFF'  }}
                            </td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.check_previous_oc == '1' , 'mt2-bg-danger' : record.check_previous_oc != '1'}">
                                @{{ record.check_previous_oc == '1' ? 'ON' : 'OFF' }}
                            </td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.feed_has_client_restrictions == '1' , 'mt2-bg-danger' : record.feed_has_client_restrictions != '1'}">
                                @{{ record.feed_has_client_restrictions == '1' ? 'ON' : 'OFF' }}
                            </td>
                            <td md-cell class="mt2-cell-left-padding">@{{ record.list_owner }}</td>
                            <td md-cell>@{{ record.cake_sub_id }}</td>
                            <td md-cell>@{{ record.feed_type }}</td>
                            <td md-cell>@{{ record.network }}</td>
                            <td md-cell>@{{ record.feed_record_source_url }}</td>
                            <td md-cell>@{{ record.feed_record_ip }}</td>
                            <td md-cell>@{{ record.ftp_url }}</td>
                            <td md-cell>@{{ record.ftp_user }}</td>
                            <td md-cell>@{{ record.feed_main_name }}</td>
                            <td md-cell>@{{ record.email_addr }}</td>
                            <td md-cell>@{{ record.address  + ' ' + record.address2  + ' ' + record.city + ' ' + record.state + ' ' + record.zip }}</td>
                            <td md-cell>@{{ record.phone }}</td>
                        </tr>
                    </tbody>
                </table>
            </md-table-container>

            <md-content class="md-mt2-zeta-theme md-hue-2">
                <md-table-pagination md-limit="feed.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="feed.currentPage" md-total="@{{feed.feedTotal}}" md-on-paginate="feed.loadFeeds" md-page-select></md-table-pagination>
            </md-content>
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/feed.js"></script>
@stop
