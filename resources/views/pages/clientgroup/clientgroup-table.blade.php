<md-table-container>
    <table md-table md-progress="clientGroup.queryPromise">
        <thead md-head>
            <tr md-row>
                <th md-column class="mt2-table-header-center"></th>
                <th md-column class="md-table-header-override-whitetext" md-numeric>ID</th>
                <th md-column class="md-table-header-override-whitetext">Name</th>
                <th md-column></th>
            </tr>
        </thead>

        <tbody md-body>
            <tr md-row ng-repeat-start="record in clientGroup.clientGroups track by $index">
                <td md-cell class="mt2-table-cell-center">
                    <md-button class="md-icon-button" aria-label="View Feeds"
                                ng-click="clientGroup.clientFeedMap[record.id]=!clientGroup.clientFeedMap[record.id]">
                        <md-icon md-svg-icon="img/icons/ic_expand_more_black_18px.svg" ng-hide="clientGroup.clientFeedMap[record.id]"></md-icon>
                        <md-icon md-svg-icon="img/icons/ic_expand_less_black_18px.svg" ng-show="clientGroup.clientFeedMap[record.id]"></md-icon>
                    </md-button>
                </td>
                <td md-cell>@{{ record.id }}</td>

                <td md-cell>
                    <a ng-click="clientGroup.loadClients(record.id)">
                        @{{ record.name }}
                    </a>
                </td>

                <td md-cell class="=mt2-table-cell-center">
                        <md-button class="md-raised" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-href="@{{ '/clientgroup/edit/' + record.id }}" target="_self">
                            <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Edit</span>
                        </md-button>

                        <md-button class="md-raised md-accent" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-click="clientGroup.copyClientGroup( record.id )">
                            <md-icon md-svg-icon="img/icons/ic_content_copy_white_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Copy</span>
                        </md-button>

                        <md-button class="md-raised md-warn md-hue-2" ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }" ng-click="ctrl.deletegroup( { groupID : record.id } )">
                            <md-icon md-svg-icon="img/icons/ic_clear_white_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Delete</span>
                        </md-button>
                </td>
            </tr>
            <tr md-row ng-repeat-end ng-show="clientGroup.clientFeedMap[record.id]">
                <td md-cell colspan="4" class="mt2-table-cell-center">
                    <md-card>
                        @include( 'pages.clientgroup.clientgroup-children-table' )
                    </md-card>
                </td>
            </tr>
        </tbody>
    </table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="clientGroup.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="clientGroup.currentPage" md-total="@{{clientGroup.clientGroupTotal}}" md-on-paginate="clientGroup.loadClientGroups" md-page-select></md-table-pagination>
</md-content>