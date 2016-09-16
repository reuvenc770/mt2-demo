<md-table-container>
    <table md-table md-progress="cleanse.queryPromise">
        <thead md-head >
            <tr md-row>
                <th md-column class="md-table-header-override-whitetext">File Name</th>
                <th md-column class="md-table-header-override-whitetext">Updated</th>
                <th md-column class="md-table-header-override-whitetext" md-numeric>Records</th>
            </tr>
        </thead>

        <tbody md-body>
            <tr md-row ng-repeat="record in cleanse.cleanses track by $index">
                <td md-cell>@{{ record.name }}</td>
                <td md-cell>@{{ record.lastUpdated }}</td>
                <td md-cell>@{{ record.count }}</td>
            </tr>
        </tbody>
    </table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="cleanse.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="cleanse.currentPage" md-total="@{{cleanse.cleanseTotal}}" md-on-paginate="cleanse.load" md-page-select></md-table-pagination>
</md-content>