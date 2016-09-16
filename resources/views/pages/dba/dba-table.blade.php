<md-table-container>
    <table md-table md-progress="dba.queryPromise">
        <thead md-head md-order="dba.sort" md-on-reorder="dba.loadAccounts">
        <tr md-row>
            <th md-column></th>
            <th md-column md-order-by="dba_name" class="md-table-header-override-whitetext">DBA Name</th>
            <th md-column md-order-by="registrant_name" class="md-table-header-override-whitetext">Registrant Name</th>
            <th md-column md-order-by="address" class="md-table-header-override-whitetext">Address</th>
            <th md-column md-order-by="dba_email" class="md-table-header-override-whitetext">Email</th>
            <th md-column md-order-by="phone" class="md-table-header-override-whitetext">Phone</th>
            <th md-column md-order-by="po_boxes" class="md-table-header-override-whitetext">PO Boxes</th>
            <th md-column md-order-by="entity_name" class="md-table-header-override-whitetext">Entity Name</th>
            <th md-column md-order-by="status" class="md-table-header-override-whitetext">Status</th>
        </tr>
        </thead>

        <tbody md-body>
        <tr md-row ng-repeat="record in dba.accounts track by $index" ng-class="{ 'mt2-row-disable' : record.status == 0 }" >
            <td md-cell>
                <md-button class="md-raised"
                            ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }"
                            ng-href="@{{ '/dba/edit/' + record.id }}" target="_self">
                    <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Edit</span>
                </md-button>
            </td>
            <td md-cell>@{{ record.dba_name }}</td>
            <td md-cell>@{{ record.registrant_name }}</td>
            <td md-cell>@{{ record.address }} @{{ record.city }} @{{ record.state }} @{{ record.zip }}</td>
            <td md-cell>@{{ record.dba_email }}</td>
            <td md-cell>@{{ record.phone }}</td>
            <td md-cell>@{{ dba.formatBox(record.po_boxes) }}</td>
            <td md-cell>@{{ record.entity_name }}</td>
            <td md-cell>
                <md-button ng-if="record.status == 1" class="md-raised md-warn md-hue-2 mt2-button-xs" ng-click="dba.toggle( record.id , 0 )">Deactivate</md-button>
                <md-button ng-if="record.status == 0" class="md-raised mt2-button-success mt2-button-xs" ng-click="dba.toggle( record.id , 1 )">Activate</md-button>
            </td>
        </tr>
        </tbody>
    </table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="dba.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="dba.currentPage" md-total="@{{dba.accountTotal}}" md-on-paginate="dba.loadAccounts" md-page-select></md-table-pagination>
</md-content>