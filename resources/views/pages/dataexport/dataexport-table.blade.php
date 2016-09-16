<md-table-container>
    <table md-table class="mt2-table-large" md-row-select multiple="true" md-progress="dataExport.queryPromise" ng-model="dataExport.mdSelectedExports">
        <thead md-head>
            <tr md-row>
            <th md-column></th>
            <th md-column class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
            <th md-column class="md-table-header-override-whitetext">Filename</th>
            <th md-column class="md-table-header-override-whitetext">Client</th>
            <th md-column class="md-table-header-override-whitetext">Profile</th>
            <th md-column class="md-table-header-override-whitetext">FTP username</th>
            <th md-column class="md-table-header-override-whitetext">Frequency</th>
            <th md-column class="md-table-header-override-whitetext">Last Pulled</th>
            <th md-column class="md-table-header-override-whitetext" md-numeric>Records</th>
            </tr>
        </thead>

        <tbody md-body>
            <tr md-row
                md-auto-select="false"
                md-select="record"
                md-select-id="exportID"
                md-on-select="dataExport.mdToggleInclusion"
                md-on-deselect="dataExport.mdToggleInclusion"
                ng-repeat="record in dataExport.dataExports track by $index">
                <td md-cell layout="row" class="mt2-table-cell-center">
                    <md-button class="md-raised" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-href="@{{'/dataexport/edit/' + record.exportID}}" target="_self">
                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Edit</span>
                    </md-button>

                    <md-button class="md-raised md-warn" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-click="dataExport.changeDataExportStatus(record.exportID)">
                        <md-icon md-svg-icon="img/icons/ic_pause_white_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> @{{dataExport.massActionButtonText}}</span>
                    </md-button>

                    <md-button class="md-raised md-accent" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-click="dataExport.copyDataExport(record.exportID)">
                        <md-icon md-svg-icon="img/icons/ic_content_copy_white_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Copy</span>
                    </md-button>

                    <md-button class="md-raised md-warn md-hue-2" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-click="dataExport.deleteDataExport(record.exportID)">
                        <md-icon md-svg-icon="img/icons/ic_clear_white_24px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Delete</span></md-button>
                    </md-button>
                </td>
                <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.status == 'Active' , 'mt2-bg-warn' : record.status == 'Paused' , 'mt2-bg-danger' : record.status == 'Deleted' }"><strong>@{{ record.status == 'Active' ? 'Active' : record.status == 'Paused' ? 'Paused' : 'Deleted'  }}</strong></td>
                <td md-cell>@{{ record.fileName }}</td>
                <td md-cell>@{{ record.group_name }}</td>
                <td md-cell>@{{ record.profile_name }}</td>
                <td md-cell>@{{ record.ftpUser }}</td>
                <td md-cell>@{{ record.frequency }}</td>
                <td md-cell>@{{ record.lastUpdated }}</td>
                <td md-cell>@{{ record.recordCount }}</td>
            </tr>
        </tbody>
    </table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="dataExport.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="dataExport.currentPage" md-total="@{{dataExport.dataExportTotal}}" md-on-paginate="dataExport.mdLoadActiveDataExports" md-page-select></md-table-pagination>
</md-content>