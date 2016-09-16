<md-table-container>
    <table md-table>
        <thead md-head>
        <tr md-row>
            <th md-column class="md-table-header-override-whitetext">Domain</th>
            <th md-column class="md-table-header-override-whitetext">Proxy</th>
            <th md-column class="md-table-header-override-whitetext">Registrar</th>
            <th md-column class="md-table-header-override-whitetext">Mainsite</th>
            <th md-column class="md-table-header-override-whitetext">Created</th>
            <th md-column class="md-table-header-override-whitetext">Expires</th>
            <th md-column class="md-table-header-override-whitetext">DBA</th>
            <th md-column class="md-table-header-override-whitetext">Actions</th>
        </tr>
        </thead>

        <tbody md-body>
        <tr md-row ng-repeat="record in domain.domains track by $index" ng-class="{ 'mt2-row-disable' : record.status == 0 }">
            <td md-cell>@{{ record.domain_name }}</td>
            <td md-cell>@{{ record.proxy_name }}</td>
            <td md-cell>@{{ record.registrar_name }}</td>
            <td md-cell>@{{ record.main_site }}</td>
            <td md-cell>@{{ record.created_at }}</td>
            <td md-cell>@{{ record.expires_at }}</td>
            <td md-cell>@{{ record.dba_name }}</td>
            <td md-cell>  <md-button ng-if="record.status == 1" class="md-raised md-warn md-hue-2 mt2-button-xs" ng-click="domain.toggle( record.dom_id, 0 )">Deactivate</md-button>
                <md-button ng-if="record.status == 0" class="md-raised mt2-button-success mt2-button-xs" ng-click="domain.toggle( record.dom_id, 1 )">Activate</md-button>
            </td>
        </tr>
        </tbody>
    </table>
</md-table-container>
