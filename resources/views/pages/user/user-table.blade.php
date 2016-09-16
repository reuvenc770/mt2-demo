<md-table-container>
    <table md-table>
        <thead md-head>
            <tr md-row>
                <th md-column></th>
                <th md-column class="md-table-header-override-whitetext" md-numeric>ID</th>
                <th md-column class="md-table-header-override-whitetext">Email</th>
                <th md-column class="md-table-header-override-whitetext">Username</th>
                <th md-column class="md-table-header-override-whitetext">First Name</th>
                <th md-column class="md-table-header-override-whitetext">Last Name</th>
                <th md-column class="md-table-header-override-whitetext">Roles</th>
                <th md-column class="md-table-header-override-whitetext">Status</th>
                <th md-column class="md-table-header-override-whitetext">Last Login</th>
            </tr>
        </thead>

        <tbody md-body>
            <tr md-row ng-repeat="record in user.accounts track by $index">
                <td md-cell>
                    <md-button class="md-raised" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-href="@{{ user.editUrl + record.id }}" target="_self">
                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Edit</span>
                    </md-button>
                </td>
                <td md-cell>@{{ record.id }}</td>
                <td md-cell>@{{ record.email }}</td>
                <td md-cell>@{{ record.username }}</td>
                <td md-cell>@{{ record.first_name }}</td>
                <td md-cell>@{{ record.last_name }}</td>
                <td md-cell>@{{ record.roles }}</td>
                <td md-cell>@{{ record.status }}</td>
                <td md-cell>@{{ record.last_login }}</td>
            </tr>
        </tbody>
    </table>
</md-table-container>