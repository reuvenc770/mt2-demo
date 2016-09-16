<md-table-container>
    <table md-table md-progress="mailing.queryPromise">
        <thead md-head md-order="mailing.sort" md-on-reorder="mailing.loadAccounts">
            <tr md-row>
                <th md-column ></th>
                <th md-column md-order-by="id" class="md-table-header-override-whitetext" md-numeric>ID</th>
                <th md-column md-order-by="template_name" class="md-table-header-override-whitetext">Template Name</th>
                <th md-column md-order-by="template_type" class="md-table-header-override-whitetext">Template Type</th>
            </tr>
        </thead>

        <tbody md-body>
            <tr md-row ng-repeat="record in mailing.templates track by $index">
                <td md-cell class="mt2-table-cell-center">
                    <md-button class="md-raised" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-href="@{{ '/mailingtemplate/edit/' + record.id }}" target="_self">
                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Edit</span>
                    </md-button>
                </td>
                <td md-cell>@{{ record.id }}</td>
                <td md-cell>@{{ record.template_name }}</td>
                <td md-cell>@{{ mailing.templateTypeMap[record.template_type] }}</td>
            </tr>
        </tbody>
    </table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="mailing.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="mailing.currentPage" md-total="@{{mailing.templateTotal}}" md-on-paginate="mailing.loadAccounts" md-page-select></md-table-pagination>
</md-content>