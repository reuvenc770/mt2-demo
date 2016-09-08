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
                <td md-cell><md-button ng-href="@{{'/ymlp/ymlp-campaign/edit/' + record.id}}" target="_self">Edit</md-button></td>
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