<md-toolbar class="md-table-toolbar md-mt2-zeta-theme md-hue-2">
    <div class="md-toolbar-tools">
        <span>Client Report</span>

        <span flex></span>

        <md-datepicker class="md-mt2-zeta-theme transparent-background" ng-model="attr.query.filters.date.start" md-placeholder="Start Date"></md-datepicker>
        <md-datepicker class="md-mt2-zeta-theme transparent-background" ng-model="attr.query.filters.date.end" md-placeholder="End Date"></md-datepicker>
        <md-button class="md-raised" ng-click="attr.getRecords()">Filter</md-button>
    </div>
</md-toolbar>

<md-table-container>
    <table md-table md-progress="attr.queryPromise">
        <thead md-head md-order="attr.query.order" md-on-reorder="attr.getRecords">
            <tr md-row>
                <th md-column md-order-by="date" class="md-table-header-override-whitetext">Date</th>
                <th md-column md-order-by="client_id" md-numeric class="md-table-header-override-whitetext">Client</th>
                <th md-column md-order-by="delivered" md-numeric class="md-table-header-override-whitetext">Delivered</th>
                <th md-column md-order-by="opened" md-numeric class="md-table-header-override-whitetext">Opened</th>
                <th md-column md-order-by="clicked" md-numeric class="md-table-header-override-whitetext">Clicked</th>
                <th md-column md-order-by="converted" md-numeric class="md-table-header-override-whitetext">Converted</th>
                <th md-column md-order-by="bounced" md-numeric class="md-table-header-override-whitetext">Bounced</th>
                <th md-column md-order-by="unsubbed" md-numeric class="md-table-header-override-whitetext">Unsubbed</th>
                <th md-column md-order-by="revenue" md-numeric class="md-table-header-override-whitetext">Revenue</th>
                <th md-column md-order-by="cost" md-numeric class="md-table-header-override-whitetext">Cost</th>
            </tr>
        </thead>
        <tbody md-body>
            <tr md-row ng-repeat="record in { true : attr.records , false : [] }[ attr.query.type === 'Client' ]">
                <td md-cell>@{{record.date}}</td>
                <td md-cell>@{{record.client_id}}</td>
                <td md-cell>@{{record.delivered}}</td>
                <td md-cell>@{{record.opened}}</td>
                <td md-cell>@{{record.clicked}}</td>
                <td md-cell>@{{record.converted}}</td>
                <td md-cell>@{{record.bounced}}</td>
                <td md-cell>@{{record.unsubbed}}</td>
                <td md-cell>$@{{record.revenue}}</td>
                <td md-cell>$@{{record.cost}}</td>
            </tr>
            <tr md-row>
                <td md-cell><strong>Totals</strong></td>
                <td md-cell></td>
                <td md-cell><strong>@{{ attr.meta.recordTotals.delivered }}</strong></td>
                <td md-cell><strong>@{{ attr.meta.recordTotals.opened }}</strong></td>
                <td md-cell><strong>@{{ attr.meta.recordTotals.clicked }}</strong></td>
                <td md-cell><strong>@{{ attr.meta.recordTotals.converted }}</strong></td>
                <td md-cell><strong>@{{ attr.meta.recordTotals.bounced }}</strong></td>
                <td md-cell><strong>@{{ attr.meta.recordTotals.unsubbed }}</strong></td>
                <td md-cell><strong>$@{{ attr.meta.recordTotals.revenue }}</strong></td>
                <td md-cell><strong>$@{{ attr.meta.recordTotals.cost }}</strong></td>
            </tr>
        </tbody>
    </table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="attr.query.limit" md-limit-options="[50, 100, 250]" md-page="attr.query.page" md-total="@{{attr.meta.recordCount}}" md-on-paginate="attr.getRecords" md-page-select></md-table-pagination>
</md-content>
