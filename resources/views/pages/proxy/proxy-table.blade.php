<md-table-container>
    <table md-table md-progress="proxy.queryPromise">
        <thead md-head md-order="proxy.sort" md-on-reorder="proxy.loadAccounts">
        <tr md-row>
            <th md-column></th>
            <th md-column md-order-by="name" class="md-table-header-override-whitetext">Proxy Name</th>
            <th md-column md-order-by="provider_name" class="md-table-header-override-whitetext">Provider Name</th>
            <th md-column class="md-table-header-override-whitetext">IPs</th>
            <th md-column class="md-table-header-override-whitetext">Esps</th>
            <th md-column class="md-table-header-override-whitetext">Isps</th>
            <th md-column class="md-table-header-override-whitetext">Notes</th>
            <th md-column md-order-by="status" class="md-table-header-override-whitetext">Status</th>
        </tr>
        </thead>

        <tbody md-body>
        <tr md-row ng-repeat="record in proxy.accounts track by $index" ng-class="{ 'mt2-row-disable' : record.status == 0 }">
            <td md-cell>
                <md-button class="md-raised" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-href="@{{ '/proxy/edit/' + record.id }}" target="_self">
                    <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Edit</span>
                </md-button>
            </td>
            <td md-cell>@{{ record.name }}</td>
            <td md-cell>@{{ record.provider_name }}</td>
            <td md-cell>@{{ record.ip_addresses }}</td>
            <td md-cell>@{{ record.esp_names }}</td>
            <td md-cell>@{{ record.isp_names }}</td>
            <td md-cell>@{{ record.notes }}</td>
            <td md-cell>
                <md-button ng-if="record.status == 1" class="md-raised md-warn md-hue-2 mt2-button-xs" ng-click="proxy.toggle( record.id , 0 )">Deactivate</md-button>
                <md-button ng-if="record.status == 0" class="md-raised mt2-button-success mt2-button-xs" ng-click="proxy.toggle( record.id , 1 )">Activate</md-button>
            </td>
        </tr>
        </tbody>
    </table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="proxy.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="proxy.currentPage" md-total="@{{proxy.accountTotal}}" md-on-paginate="proxy.loadAccounts" md-page-select></md-table-pagination>
</md-content>