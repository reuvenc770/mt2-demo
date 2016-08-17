<md-toolbar class="md-table-toolbar md-mt2-zeta-theme md-hue-2">
    <div class="md-toolbar-tools">
        <span>3 Month Report</span>

        <span flex></span>

        <md-button class="md-raised" ng-click="attr.getRecords()">Refresh</md-button>
    </div>
</md-toolbar>

<md-table-container ng-init="attr.loadRecords()">
    <table md-table md-progress="attr.queryPromise">
        <thead md-head>
            <tr md-row>
                <th md-column colspan="2"></th>
                <th md-column colspan="6" class="mt2-two-months-ago-header mt2-month-header md-table-header-override-whitetext" ng-bind="attr.threeMonthSections.twoMonthsAgo"></th>
                <th md-column colspan="6" class="mt2-last-month-header mt2-month-header md-table-header-override-whitetext" ng-bind="attr.threeMonthSections.lastMonth"></th>
                <th md-column colspan="6" class="mt2-current-month-header mt2-month-header md-table-header-override-whitetext" ng-bind="attr.threeMonthSections.currentMonth"></th>
            </tr>
            <tr md-row>
                <th md-column class="md-table-header-override-whitetext">Client</th>
                <th md-column class="md-table-header-override-whitetext">Feed</th>

                <th md-column class="mt2-two-months-ago-header md-table-header-override-whitetext mt2-cell-left-padding" md-numeric>Revenue</th>
                <th md-column class="mt2-two-months-ago-header md-table-header-override-whitetext" md-numeric>Revshare</th>
                <th md-column class="mt2-two-months-ago-header md-table-header-override-whitetext" md-numeric>CPM Revenue</th>
                <th md-column class="mt2-two-months-ago-header md-table-header-override-whitetext" md-numeric>CPM Revshare</th>
                <th md-column class="mt2-two-months-ago-header md-table-header-override-whitetext" md-numeric>MT1 Uniques</th>
                <th md-column class="mt2-two-months-ago-header md-table-header-override-whitetext" md-numeric>MT2 Uniques</th>

                <th md-column class="mt2-last-month-header md-table-header-override-whitetext mt2-cell-left-padding" md-numeric>Revenue</th>
                <th md-column class="mt2-last-month-header md-table-header-override-whitetext" md-numeric>Revshare</th>
                <th md-column class="mt2-last-month-header md-table-header-override-whitetext" md-numeric>CPM Revenue</th>
                <th md-column class="mt2-last-month-header md-table-header-override-whitetext" md-numeric>CPM Revshare</th>
                <th md-column class="mt2-last-month-header md-table-header-override-whitetext" md-numeric>MT1 Uniques</th>
                <th md-column class="mt2-last-month-header md-table-header-override-whitetext" md-numeric>MT2 Uniques</th>

                <th md-column class="mt2-current-month-header md-table-header-override-whitetext mt2-cell-left-padding" md-numeric>Revenue</th>
                <th md-column class="mt2-current-month-header md-table-header-override-whitetext" md-numeric>Revshare</th>
                <th md-column class="mt2-current-month-header md-table-header-override-whitetext" md-numeric>CPM Revenue</th>
                <th md-column class="mt2-current-month-header md-table-header-override-whitetext" md-numeric>CPM Revshare</th>
                <th md-column class="mt2-current-month-header md-table-header-override-whitetext" md-numeric>MT1 Uniques</th>
                <th md-column class="mt2-current-month-header md-table-header-override-whitetext" md-numeric>MT2 Uniques</th>
            </tr>
        </thead>
        <tbody md-body>
            <tr ng-repeat="record in { true : attr.records , false : [] }[ attr.query.type === 'ThreeMonth' ]" ng-class="{ 'mt2-total-row' : record.client_stats_grouping_id }" md-row>
                <td md-cell>@{{ record.client_stats_grouping_id ? attr.listOwnerNameMap[ record.client_stats_grouping_id ] + ' (' + record.client_stats_grouping_id + ')' : '' }}</td>
                <td md-cell>@{{ record.client_id ? attr.clientNameMap[ record.client_id ] + ' (' + record.client_id + ')' : '' }}</td>

                <td class="mt2-two-months-ago-cell mt2-cell-left-padding" md-cell>$@{{ record.two_months_ago.standard_revenue }}</td>
                <td class="mt2-two-months-ago-cell" md-cell>$@{{ ( record.two_months_ago.standard_revenue * 0.15 ).toFixed( 3 ) }}</td>
                <td class="mt2-two-months-ago-cell" md-cell>$@{{ record.two_months_ago.cpm_revenue ? record.two_months_ago.cpm_revenue : (0.0).toFixed( 3 ) }}</td>
                <td class="mt2-two-months-ago-cell" md-cell>$@{{ record.two_months_ago.cpm_revenue ? ( record.two_months_ago.cpm_revenue * 0.15 ).toFixed( 3 ) : (0.0).toFixed( 3 ) }}</td>
                <td class="mt2-two-months-ago-cell" md-cell>@{{ record.two_months_ago.mt1_uniques }}</td>
                <td class="mt2-two-months-ago-cell" md-cell>@{{ record.two_months_ago.mt2_uniques }}</td>

                <td class="mt2-last-month-cell mt2-cell-left-padding" md-cell>$@{{ record.last_month.standard_revenue }}</td>
                <td class="mt2-last-month-cell" md-cell>$@{{ ( record.last_month.standard_revenue * 0.15 ).toFixed( 3 ) }}</td>
                <td class="mt2-last-month-cell" md-cell>$@{{ record.last_month.cpm_revenue ? record.last_month.cpm_revenue : (0.0).toFixed( 3 ) }}</td>
                <td class="mt2-last-month-cell" md-cell>$@{{ record.last_month.cpm_revenue ? ( record.last_month.cpm_revenue * 0.15 ).toFixed( 3 ) : (0.0).toFixed( 3 ) }}</td>
                <td class="mt2-last-month-cell" md-cell>@{{ record.last_month.mt1_uniques }}</td>
                <td class="mt2-last-month-cell" md-cell>@{{ record.last_month.mt2_uniques }}</td>

                <td class="mt2-current-month-cell mt2-cell-left-padding" md-cell>$@{{ record.current_month.standard_revenue }}</td>
                <td class="mt2-current-month-cell" md-cell>$@{{ ( record.current_month.standard_revenue * 0.15 ).toFixed( 3 ) }}</td>
                <td class="mt2-current-month-cell" md-cell>$@{{ record.current_month.cpm_revenue ? record.current_month.cpm_revenue : (0.0).toFixed( 3 ) }}</td>
                <td class="mt2-current-month-cell" md-cell>$@{{ record.current_month.cpm_revenue ? ( record.current_month.cpm_revenue * 0.15 ).toFixed( 3 ) : (0.0).toFixed( 3 ) }}</td>
                <td class="mt2-current-month-cell" md-cell>@{{ record.current_month.mt1_uniques }}</td>
                <td class="mt2-current-month-cell" md-cell>@{{ record.current_month.mt2_uniques }}</td>
            </tr>
            <tr class="mt2-total-row" md-row>
                <td md-cell>Totals</td>
                <td md-cell></td>

                <td class="mt2-two-months-ago-cell" md-cell>$@{{ ( attr.meta.recordTotals.two_months_ago.standard_revenue ).toFixed( 3 ) }}</td>
                <td class="mt2-two-months-ago-cell" md-cell>$@{{ ( attr.meta.recordTotals.two_months_ago.standard_revenue * 0.15 ).toFixed( 3 ) }}</td>
                <td class="mt2-two-months-ago-cell" md-cell>$@{{ attr.meta.recordTotals.two_months_ago.cpm_revenue ? attr.meta.recordTotals.two_months_ago.cpm_revenue : (0.0).toFixed( 3 ) }}</td>
                <td class="mt2-two-months-ago-cell" md-cell>$@{{ attr.meta.recordTotals.two_months_ago.cpm_revenue ? ( attr.meta.recordTotals.two_months_ago.cpm_revenue  * 0.15 ).toFixed( 3 ) : (0.0).toFixed( 3 ) }}</td>
                <td class="mt2-two-months-ago-cell" md-cell>@{{ attr.meta.recordTotals.two_months_ago.mt1_uniques }}</td>
                <td class="mt2-two-months-ago-cell" md-cell>@{{ attr.meta.recordTotals.two_months_ago.mt2_uniques }}</td>

                <td class="mt2-last-month-cell" md-cell>$@{{ ( attr.meta.recordTotals.last_month.standard_revenue ).toFixed( 3 ) }}</td>
                <td class="mt2-last-month-cell" md-cell>$@{{ ( attr.meta.recordTotals.last_month.standard_revenue * 0.15 ).toFixed( 3 ) }}</td>
                <td class="mt2-last-month-cell" md-cell>$@{{ attr.meta.recordTotals.last_month.cpm_revenue ? attr.meta.recordTotals.last_month.cpm_revenue : (0.0).toFixed( 3 ) }}</td>
                <td class="mt2-last-month-cell" md-cell>$@{{ attr.meta.recordTotals.last_month.cpm_revenue ? ( attr.meta.recordTotals.last_month.cpm_revenue  * 0.15 ).toFixed( 3 ) : (0.0).toFixed( 3 ) }}</td>
                <td class="mt2-last-month-cell" md-cell>@{{ attr.meta.recordTotals.last_month.mt1_uniques }}</td>
                <td class="mt2-last-month-cell" md-cell>@{{ attr.meta.recordTotals.last_month.mt2_uniques }}</td>

                <td class="mt2-current-month-cell" md-cell>$@{{ ( attr.meta.recordTotals.current_month.standard_revenue ).toFixed( 3 ) }}</td>
                <td class="mt2-current-month-cell" md-cell>$@{{ ( attr.meta.recordTotals.current_month.standard_revenue * 0.15 ).toFixed( 3 ) }}</td>
                <td class="mt2-current-month-cell" md-cell>$@{{ attr.meta.recordTotals.current_month.cpm_revenue ? attr.meta.recordTotals.current_month.cpm_revenue : (0.0).toFixed( 3 ) }}</td>
                <td class="mt2-current-month-cell" md-cell>$@{{ attr.meta.recordTotals.current_month.cpm_revenue ? ( attr.meta.recordTotals.current_month.cpm_revenue  * 0.15 ).toFixed( 3 ) : (0.0).toFixed( 3 ) }}</td>
                <td class="mt2-current-month-cell" md-cell>@{{ attr.meta.recordTotals.current_month.mt1_uniques }}</td>
                <td class="mt2-current-month-cell" md-cell>@{{ attr.meta.recordTotals.current_month.mt2_uniques }}</td>
            </tr>
        </tbody>
    </table>
</md-table-container>
