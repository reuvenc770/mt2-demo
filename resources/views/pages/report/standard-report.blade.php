<md-toolbar class="md-table-toolbar md-mt2-zeta-theme md-hue-2">
    <div class="md-toolbar-tools">
        <span>Deploy Report</span>

        <span flex></span>

        <md-datepicker class="md-mt2-zeta-theme transparent-background" ng-model="report.query.filters.date.start" md-placeholder="Start Date"></md-datepicker>
        <md-datepicker class="md-mt2-zeta-theme transparent-background" ng-model="report.query.filters.date.end" md-placeholder="End Date"></md-datepicker>
        <md-button class="md-raised" ng-click="report.getRecords()">Filter</md-button>
    </div>
</md-toolbar>

<md-table-container>
    <table md-table md-progress="report.queryPromise">
        <thead md-head md-order="report.query.order" md-on-reorder="report.getRecords">
            <tr md-row>
                <th md-column md-order-by="datetime" class="md-table-header-override-whitetext">Date</th>
                <th md-column md-order-by="external_deploy_id" class="md-table-header-override-whitetext">Deploy ID</th>
                <th md-column md-order-by="campaign_name" class="md-table-header-override-whitetext">Campaign</th>
                <th md-column md-order-by="esp_account_id" class="md-table-header-override-whitetext">ESP Account</th>
                <th md-column md-order-by="m_offer_id" class="md-table-header-override-whitetext">Offer</th>
                <th md-column md-order-by="m_creative_id" class="md-table-header-override-whitetext">Creative</th>
                <th md-column md-order-by="subject" class="md-table-header-override-whitetext">Subject</th>
                <th md-column md-order-by="from" class="md-table-header-override-whitetext">From</th>
                <th md-column md-order-by="from_email" class="md-table-header-override-whitetext">From Email</th>
                <th md-column md-order-by="m_sent" class="md-table-header-override-whitetext" md-numeric>Sent</th>
                <th md-column md-order-by="e_sent" class="md-table-header-override-whitetext" md-numeric>ESP Sent</th>
                <th md-column md-order-by="delivered" class="md-table-header-override-whitetext" md-numeric>Delivered</th>
                <th md-column md-order-by="bounced" class="md-table-header-override-whitetext" md-numeric>Bounced</th>
                <th md-column md-order-by="optouts" class="md-table-header-override-whitetext" md-numeric>Optouts</th>
                <th md-column md-order-by="m_opens" class="md-table-header-override-whitetext" md-numeric>Opens</th>
                <th md-column md-order-by="m_opens_unique" class="md-table-header-override-whitetext" md-numeric>Unique Opens</th>
                <th md-column md-order-by="e_opens" class="md-table-header-override-whitetext" md-numeric>ESP Opens</th>
                <th md-column md-order-by="e_opens_unique" class="md-table-header-override-whitetext" md-numeric>Unique ESP Opens</th>
                <th md-column md-order-by="t_opens" class="md-table-header-override-whitetext" md-numeric>CAKE Opens</th>
                <th md-column md-order-by="t_opens_unique" class="md-table-header-override-whitetext" md-numeric>Unique CAKE Opens</th>
                <th md-column md-order-by="m_clicks" class="md-table-header-override-whitetext" md-numeric>Clicks</th>
                <th md-column md-order-by="m_clicks_unique" class="md-table-header-override-whitetext" md-numeric>Unique Clicks</th>
                <th md-column md-order-by="e_clicks" class="md-table-header-override-whitetext" md-numeric>ESP Clicks</th>
                <th md-column md-order-by="e_clicks_unique" class="md-table-header-override-whitetext" md-numeric>Unique ESP Clicks</th>
                <th md-column md-order-by="t_clicks" class="md-table-header-override-whitetext" md-numeric>CAKE Clicks</th>
                <th md-column md-order-by="t_clicks_unique" class="md-table-header-override-whitetext" md-numeric>Unique CAKE Clicks</th>
                <th md-column md-order-by="conversions" class="md-table-header-override-whitetext" md-numeric>Conversions</th>
                <th md-column md-order-by="cost" class="md-table-header-override-whitetext" md-numeric>Cost</th>
                <th md-column md-order-by="revenue" class="md-table-header-override-whitetext" md-numeric>Revenue</th>
            </tr>
        </thead>
        <tbody md-body>
            <tr md-row ng-repeat="record in { true : report.records , false : [] }[ report.query.type === 'Deploy' ]">
                <td md-cell>@{{ record.datetime }}</td>
                <td md-cell>@{{ record.external_deploy_id }}</td>
                <td md-cell>@{{ record.campaign_name }}</td>
                <td md-cell>@{{ record.esp_account_id }}</td>
                <td md-cell>@{{ record.m_offer_id }}</td>
                <td md-cell>@{{ record.m_creative_id }}</td>
                <td md-cell>@{{ record.subject }}</td>
                <td md-cell>@{{ record.from }}</td>
                <td md-cell>@{{ record.from_email }}</td>
                <td md-cell>@{{ record.m_sent ? record.m_sent : 0 }}</td>
                <td md-cell>@{{ record.e_sent ? record.e_sent : 0 }}</td>
                <td md-cell>@{{ record.delivered ? record.delivered : 0 }}</td>
                <td md-cell>@{{ record.bounced ? record.bounced : 0 }}</td>
                <td md-cell>@{{ record.optouts ? record.optouts : 0 }}</td>
                <td md-cell>@{{ record.m_opens ? record.m_opens : 0 }}</td>
                <td md-cell>@{{ record.m_opens_unique ? record.m_opens_unique : 0 }}</td>
                <td md-cell>@{{ record.e_opens ? record.e_opens : 0 }}</td>
                <td md-cell>@{{ record.e_opens_unique ? record.e_opens_unique : 0 }}</td>
                <td md-cell>@{{ record.t_opens ? record.t_opens : 0 }}</td>
                <td md-cell>@{{ record.t_opens_unique ? record.t_opens_unique : 0 }}</td>
                <td md-cell>@{{ record.m_clicks ? record.m_clicks : 0 }}</td>
                <td md-cell>@{{ record.m_clicks_unique ? record.m_clicks_unique : 0 }}</td>
                <td md-cell>@{{ record.e_clicks ? record.e_clicks : 0 }}</td>
                <td md-cell>@{{ record.e_clicks_unique ? record.e_clicks_unique : 0 }}</td>
                <td md-cell>@{{ record.t_clicks ? record.t_clicks : 0 }}</td>
                <td md-cell>@{{ record.t_clicks_unique ? record.t_clicks_unique : 0 }}</td>
                <td md-cell>@{{ record.conversions ? record.conversions : 0 }}</td>
                <td md-cell>$@{{ record.cost ? ( record.cost ).toFixed( 2 ) : ( 0.0 ).toFixed( 2 ) }}</td>
                <td md-cell>$@{{ record.revenue ? record.revenue : ( 0.0 ).toFixed( 2 ) }}</td>
            </tr>
            <tr class="mt2-total-row" md-row>
                <td md-cell>Totals</td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell></td>
                <td md-cell>@{{ report.meta.recordTotals.m_sent }}</td>
                <td md-cell>@{{ report.meta.recordTotals.e_sent }}</td>
                <td md-cell>@{{ report.meta.recordTotals.delivered }}</td>
                <td md-cell>@{{ report.meta.recordTotals.bounced }}</td>
                <td md-cell>@{{ report.meta.recordTotals.optouts }}</td>
                <td md-cell>@{{ report.meta.recordTotals.m_opens }}</td>
                <td md-cell>@{{ report.meta.recordTotals.m_opens_unique }}</td>
                <td md-cell>@{{ report.meta.recordTotals.e_opens }}</td>
                <td md-cell>@{{ report.meta.recordTotals.e_opens_unique }}</td>
                <td md-cell>@{{ report.meta.recordTotals.t_opens }}</td>
                <td md-cell>@{{ report.meta.recordTotals.t_opens_unique }}</td>
                <td md-cell>@{{ report.meta.recordTotals.m_clicks }}</td>
                <td md-cell>@{{ report.meta.recordTotals.m_clicks_unique }}</td>
                <td md-cell>@{{ report.meta.recordTotals.e_clicks }}</td>
                <td md-cell>@{{ report.meta.recordTotals.e_clicks_unique }}</td>
                <td md-cell>@{{ report.meta.recordTotals.t_clicks }}</td>
                <td md-cell>@{{ report.meta.recordTotals.t_clicks_unique }}</td>
                <td md-cell>@{{ report.meta.recordTotals.conversions }}</td>
                <td md-cell>$@{{ report.meta.recordTotals.cost ? ( report.meta.recordTotals.cost ).toFixed( 2 ) : ( 0.0 ).toFixed( 2 ) }}</td>
                <td md-cell>$@{{ report.meta.recordTotals.revenue ? ( report.meta.recordTotals.revenue ).toFixed( 2 ) : ( 0.0 ).toFixed( 2 ) }}</td>
            </tr>
        </tbody>
    </table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="report.query.limit" md-limit-options="[50, 100, 250]" md-page="report.query.page" md-total="@{{report.meta.recordCount}}" md-on-paginate="report.getRecords" md-page-select></md-table-pagination>
</md-content>
