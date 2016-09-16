<md-table-container>
    <table md-table>
        <thead md-head>
            <tr md-row>
                <th md-column></th>
                <th md-column class="md-table-header-override-whitetext" md-numeric>ID</th>
                <th md-column class="md-table-header-override-whitetext">Slug</th>
                <th md-column class="md-table-header-override-whitetext">Name</th>
            </tr>
        </thead>

        <tbody md-body>
            <tr md-row ng-repeat="record in role.roles track by $index">
                <td md-cell class="mt2-table-cell-center">
                    <md-button class="md-raised" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-href="@{{ role.editUrl + record.id }}" target="_self">
                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Edit</span>
                    </md-button>
                </td>
                <td md-cell>@{{ record.id }}</td>
                <td md-cell>@{{ record.slug }}</td>
                <td md-cell>@{{ record.name }}</td>
        </tbody>
    </table>
</md-table-container>