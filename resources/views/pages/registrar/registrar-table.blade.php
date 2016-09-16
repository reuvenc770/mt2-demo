<md-table-container>
    <table md-table md-progress="registrar.queryPromise">
        <thead md-head md-order="registrar.sort" md-on-reorder="registrar.loadAccounts">
        <tr md-row>
            <th md-column></th>
            <th md-column md-order-by="name" class="md-table-header-override-whitetext">Registrar Name</th>
            <th md-column md-order-by="username" class="md-table-header-override-whitetext">Username</th>
            <th md-column md-order-by="contact_name" class="md-table-header-override-whitetext">Contact Name</th>
            <th md-column md-order-by="contact_email" class="md-table-header-override-whitetext">Contact Email</th>
            <th md-column md-order-by="phone_number" class="md-table-header-override-whitetext">Phone</th>
            <th md-column md-order-by="entity_name" class="md-table-header-override-whitetext">Entity Name</th>
            <th md-column md-order-by="status" class="md-table-header-override-whitetext">Status</th>
        </tr>
        </thead>

        <tbody md-body>
        <tr md-row ng-repeat="record in registrar.accounts track by $index" ng-class="{ 'mt2-row-disable' : record.status == 0 }">
            <td md-cell>
                <md-button class="md-raised" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-href="@{{ '/registrar/edit/' + record.id }}" target="_self">
                    <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Edit</span>
                </md-button>
            </td>
            <td md-cell>@{{ record.name }}</td>
            <td md-cell>@{{ record.username }}</td>
            <td md-cell>@{{ record.contact_name }}</td>
            <td md-cell>@{{ record.contact_email }}</td>
            <td md-cell>@{{ record.phone_number }}</td>
            <td md-cell>@{{ record.entity_name }}</td>
            <td md-cell>
                <md-button ng-if="record.status == 1" class="md-raised md-warn md-hue-2 mt2-button-xs" ng-click="registrar.toggle( record.id , 0 )">Deactivate</md-button>
                <md-button ng-if="record.status == 0" class="md-raised mt2-button-success mt2-button-xs" ng-click="registrar.toggle(record.id, 1 )">Activate</span>
            </td>
        </tr>
        </tbody>
    </table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="registrar.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="registrar.currentPage" md-total="@{{registrar.accountTotal}}" md-on-paginate="registrar.loadAccounts" md-page-select></md-table-pagination>
</md-content>