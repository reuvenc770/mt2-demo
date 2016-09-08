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
            <th md-column></th>
        </tr>
        </thead>

        <tbody md-body>
        <tr md-row ng-repeat="record in registrar.accounts track by $index" ng-class="{ rowDisabled : registrar.status == 0 }">
            <td md-cell><edit-button editurl="'/registrar/edit/'" recordid="record.id"></edit-button></td>
            <td md-cell>@{{ record.name }}</td>
            <td md-cell>@{{ record.username }}</td>
            <td md-cell>@{{ record.contact_name }}</td>
            <td md-cell>@{{ record.contact_email }}</td>
            <td md-cell>@{{ record.phone_number }}</td>
            <td md-cell>@{{ record.entity_name }}</td>
            <td md-cell>
                <span ng-if="record.status == 1" class="btn btn-danger" ng-click="ctrl.toggle({recordId : record.id, direction : 0})">Deactivate</span>
                <span ng-if="record.status == 0" class="btn btn-success" ng-click="ctrl.toggle({recordId : record.id, direction : 1})">Activate</span>
            </td>
        </tr>
        </tbody>
    </table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="registrar.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="registrar.currentPage" md-total="@{{registrar.accountTotal}}" md-on-paginate="registrar.loadAccounts" md-page-select></md-table-pagination>
</md-content>