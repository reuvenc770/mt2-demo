<md-table-container>
    <table md-table>
        <thead md-head>
        <tr md-row>
            <th md-column class="md-table-header-override-whitetext"></th>
            <th md-column class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
            <th md-column class="md-table-header-override-whitetext mt2-cell-left-padding">Domain</th>
            <th md-column class="md-table-header-override-whitetext">Proxy</th>
            <th md-column class="md-table-header-override-whitetext">Registrar</th>
            <th md-column class="md-table-header-override-whitetext">Mainsite</th>
            <th md-column class="md-table-header-override-whitetext">Created</th>
            <th md-column class="md-table-header-override-whitetext">Expires</th>
            <th md-column class="md-table-header-override-whitetext">DBA</th>
        </tr>
        </thead>

        <tbody md-body>
        <tr md-row ng-repeat="record in domain.domains track by $index">
            <td md-cell>
                <div layout="row" layout-align="center center">
                    <md-button ng-if="record.status == 1" class="md-icon-button" ng-click="domain.toggle( record.dom_id, 0 )">
                        <md-icon md-svg-icon="img/icons/ic_pause_black_18px.svg"></md-icon>
                        <md-tooltip md-direction="bottom">Deactivate</md-tooltip>
                    </md-button>
                    <md-button ng-if="record.status == 0" class="md-icon-button" ng-click="domain.toggle( record.dom_id, 1 )">
                        <md-icon md-svg-icon="img/icons/ic_play_arrow_18px.svg"></md-icon>
                        <md-tooltip md-direction="bottom">Activate</md-tooltip>
                    </md-button>
                </div>
            </td>
            <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.status == 1 , 'mt2-bg-danger' : record.status == 0 }">
                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
            </td>
            <td md-cell class="mt2-cell-left-padding">@{{ record.domain_name }}</td>
            <td md-cell>@{{ record.proxy_name }}</td>
            <td md-cell>@{{ record.registrar_name }}</td>
            <td md-cell>@{{ record.main_site }}</td>
            <td md-cell>@{{ record.created_at }}</td>
            <td md-cell>@{{ record.expires_at }}</td>
            <td md-cell>@{{ record.dba_name }}</td>
        </tr>
        </tbody>
    </table>
</md-table-container>
