<md-table-container>
    <table md-table md-progress="esp.queryPromise">
        <thead md-head md-order="esp.sort" md-on-reorder="esp.loadAccounts">
            <tr md-row>
                <th md-column md-numeric>
                </th>
                <th md-column md-order-by="id" class="md-table-header-override-whitetext" md-numeric>ID</th>
                <th md-column md-order-by="account_name" class="md-table-header-override-whitetext">ESP</th>
                <th md-column md-order-by="key_1" class="md-table-header-override-whitetext">Key 1</th>
                <th md-column md-order-by="key_2" class="md-table-header-override-whitetext">Key 2</th>
                <th md-column md-order-by="created_at" class="md-table-header-override-whitetext">Created</th>
                <th md-column md-order-by="updated_at" class="md-table-header-override-whitetext">Updated</th>
            </tr>
        </thead>

        <tbody md-body>
            <tr md-row ng-repeat="record in esp.accounts track by $index">
                <td md-cell><edit-button editurl="'/espapi/edit/'" recordid="record.id"></edit-button></td>
                <td md-cell>@{{ record.id }}</td>
                <td md-cell>@{{ record.account_name }}</td>
                <td md-cell>@{{ record.key_1 }}</td>
                <td md-cell>@{{ record.key_2 }}</td>
                <td md-cell>@{{ record.created_at }}</td>
                <td md-cell>@{{ record.updated_at }}</td>
            </tr>
        </tbody>
    </table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="esp.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="esp.currentPage" md-total="@{{esp.accountTotal}}" md-on-paginate="esp.loadAccounts" md-page-select></md-table-pagination>
</md-content>
