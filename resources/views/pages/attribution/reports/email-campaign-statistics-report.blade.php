<md-toolbar class="md-table-toolbar md-mt2-zeta-theme md-hue-2">
    <div class="md-toolbar-tools">
        <span>Mesa de Danny Report</span>

        <span flex></span>

        <md-button class="md-raised" ng-click="attr.getRecords()">Refresh</md-button>
    </div>
</md-toolbar>

<md-table-container>
    <table md-table md-progress="attr.queryPromise">
        <thead md-head md-order="attr.query.order" md-on-reorder="attr.getRecords">
            <tr md-row>
                <th md-column md-order-by="updated_at" class="md-table-header-override-whitetext" md-desc>Last Updated</th>
                <th md-column md-order-by="email_id" class="md-table-header-override-whitetext">EID</th>
                <th md-column md-order-by="deploy_id" class="md-table-header-override-whitetext">Deploy</th>
                <th md-column md-order-by="last_status" class="md-table-header-override-whitetext">Status</th>
                <th md-column md-order-by="mt_total_opens" class="md-table-header-override-whitetext" md-numeric>Opens</th>
                <th md-column md-order-by="esp_total_opens" class="md-table-header-override-whitetext" md-numeric>ESP Opens</th>
                <th md-column md-order-by="trk_total_opens" class="md-table-header-override-whitetext" md-numeric>CAKE Opens</th>
                <th md-column md-order-by="mt_first_open_datetime" class="md-table-header-override-whitetext">First Open</th>
                <th md-column md-order-by="esp_first_open_datetime" class="md-table-header-override-whitetext">ESP First Open</th>
                <th md-column md-order-by="trk_first_open_datetime" class="md-table-header-override-whitetext">CAKE First Open</th>
                <th md-column md-order-by="mt_last_open_datetime" class="md-table-header-override-whitetext">Last Open</th>
                <th md-column md-order-by="esp_last_open_datetime" class="md-table-header-override-whitetext">ESP Last Open</th>
                <th md-column md-order-by="trk_first_click_datetime" class="md-table-header-override-whitetext">CAKE Last Open</th>
                <th md-column md-order-by="mt_total_clicks" class="md-table-header-override-whitetext" md-numeric>Clicks</th>
                <th md-column md-order-by="esp_total_clicks" class="md-table-header-override-whitetext" md-numeric>ESP Clicks</th>
                <th md-column md-order-by="trk_total_clicks" class="md-table-header-override-whitetext" md-numeric>CAKE Clicks</th>
                <th md-column md-order-by="mt_first_click_datetime" class="md-table-header-override-whitetext">First Click</th>
                <th md-column md-order-by="esp_first_click_datetime" class="md-table-header-override-whitetext">ESP First Click</th>
                <th md-column md-order-by="trk_first_click_datetime" class="md-table-header-override-whitetext">CAKE First Click</th>
                <th md-column md-order-by="mt_last_click_datetime" class="md-table-header-override-whitetext">Last Click</th>
                <th md-column md-order-by="esp_last_click_datetime" class="md-table-header-override-whitetext">ESP Last Click</th>
                <th md-column md-order-by="trk_last_click_datetime" class="md-table-header-override-whitetext">CAKE Last Click</th>
                <th md-column md-order-by="unsubscribed" class="md-table-header-override-whitetext" md-numeric>Unsubs</th>
                <th md-column md-order-by="hard_bounce" class="md-table-header-override-whitetext" md-numeric>Bounces</th>
            </tr>
        </thead>
        <tbody md-body>
            <tr md-row ng-repeat="record in { true : attr.records , false : [] }[ attr.query.type === 'EmailCampaignStatistics' ]">
                <td md-cell>@{{ record.updated_at }}</td>
                <td md-cell>@{{ record.email_id }}</td>
                <td md-cell>@{{ record.deploy_id }}</td>
                <td md-cell>@{{ record.last_status }}</td>
                <td md-cell>@{{ record.mt_total_opens }}</td>
                <td md-cell>@{{ record.esp_total_opens }}</td>
                <td md-cell>@{{ record.trk_total_opens }}</td>
                <td md-cell>@{{ record.mt_first_open_datetime }}</td>
                <td md-cell>@{{ record.esp_first_open_datetime }}</td>
                <td md-cell>@{{ record.trk_first_open_datetime }}</td>
                <td md-cell>@{{ record.mt_last_open_datetime }}</td>
                <td md-cell>@{{ record.esp_last_open_datetime }}</td>
                <td md-cell>@{{ record.trk_first_click_datetime }}</td>
                <td md-cell>@{{ record.mt_total_clicks }}</td>
                <td md-cell>@{{ record.esp_total_clicks }}</td>
                <td md-cell>@{{ record.trk_total_clicks }}</td>
                <td md-cell>@{{ record.mt_first_click_datetime }}</td>
                <td md-cell>@{{ record.esp_first_click_datetime }}</td>
                <td md-cell>@{{ record.trk_first_click_datetime }}</td>
                <td md-cell>@{{ record.mt_last_click_datetime }}</td>
                <td md-cell>@{{ record.esp_last_click_datetime }}</td>
                <td md-cell>@{{ record.trk_last_click_datetime }}</td>
                <td md-cell>@{{ record.unsubscribed }}</td>
                <td md-cell>@{{ record.hard_bounce }}</td>
            </tr>
            <tr class="mt2-total-row" md-row>
                <td md-cell>Totals</td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell>@{{ attr.meta.recordTotals.mt_total_opens }}</td>
                <td md-cell>@{{ attr.meta.recordTotals.esp_total_opens }}</td>
                <td md-cell>@{{ attr.meta.recordTotals.trk_total_opens }}</td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell>@{{ attr.meta.recordTotals.mt_total_clicks }}</td>
                <td md-cell>@{{ attr.meta.recordTotals.esp_total_clicks }}</td>
                <td md-cell>@{{ attr.meta.recordTotals.trk_total_clicks }}</td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell>@{{ attr.meta.recordTotals.unsubscribed }}</td>
                <td md-cell>@{{ attr.meta.recordTotals.hard_bounce }}</td>
            </tr>
        </tbody>
    </table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="attr.query.limit" md-limit-options="[50, 100, 250]" md-page="attr.query.page" md-total="@{{attr.meta.recordCount}}" md-on-paginate="attr.getRecords" md-page-select></md-table-pagination>
</md-content>
