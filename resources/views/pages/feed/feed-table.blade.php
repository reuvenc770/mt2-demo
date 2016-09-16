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
                <th md-column class="md-table-header-override-whitetext">List Owner</th>
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
                    <md-button class="md-raised" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-href="@{{'/feed/edit/' + record.client_id}}" target="_self">
                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Edit</span>
                    </md-button>
                </td>
                <td md-cell>@{{ record.username }}</td>
                <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.status == 'A' , 'mt2-bg-warn' : record.status == 'P' , 'mt2-bg-danger' : record.status == 'D' }">
                    <strong>@{{ record.status == 'A' ? 'Active' : record.status == 'P' ? 'Paused' : 'Inactive'  }}</strong>
                </td>
                <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.check_global_suppression == 'Y' , 'mt2-bg-danger' : record.check_global_suppression != 'Y' }">
                    <strong>@{{ record.check_global_suppression == 'Y' ? 'ON' : 'OFF'  }}</strong>
                </td>
                <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.check_previous_oc == '1' , 'mt2-bg-danger' : record.check_previous_oc != '1'}">
                    <strong>@{{ record.check_previous_oc == '1' ? 'ON' : 'OFF' }}</strong>
                </td>
                <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.feed_has_client_restrictions == '1' , 'mt2-bg-danger' : record.feed_has_client_restrictions != '1'}">
                    <strong>@{{ record.feed_has_client_restrictions == '1' ? 'ON' : 'OFF' }}</strong>
                </td>
                <td md-cell>@{{ record.list_owner }}</td>
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