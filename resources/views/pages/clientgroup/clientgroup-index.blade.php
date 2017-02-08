@extends( 'layout.default' )

@section( 'title' , 'Feed Group' )

@section( 'angular-controller' , 'ng-controller="ClientGroupController as clientGroup"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('clientgroup.add'))
        <md-button ng-click="clientGroup.viewAdd()" aria-label="Add Feed Group">
            <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-show="app.isMobile()">add_circle_outline</md-icon>
            <span ng-hide="app.isMobile()">Add Feed Group</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="clientGroup.loadClientGroups()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <md-card flex-gt-md="70" flex="100">
            <md-table-container>
                <table md-table md-progress="clientGroup.queryPromise">
                    <thead md-head>
                        <tr md-row>
                            <th md-column></th>
                            <th md-column class="md-table-header-override-whitetext">ID</th>
                            <th md-column class="md-table-header-override-whitetext">Name</th>
                        </tr>
                    </thead>

                    <tbody md-body>
                        <tr md-row ng-repeat-start="record in clientGroup.clientGroups track by $index">
                            <td md-cell>
                                <md-button class="md-icon-button" aria-label="View Feeds"
                                            ng-click="clientGroup.clientFeedMap[record.id]=!clientGroup.clientFeedMap[record.id]">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-hide="clientGroup.clientFeedMap[record.id]">expand_more</md-icon>
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-show="clientGroup.clientFeedMap[record.id]">expand_less</md-icon>
                                </md-button>
                                <a ng-href="@{{ '/clientgroup/edit/' + record.id }}" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon></a>

                                <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-click="clientGroup.copyClientGroup( record.id )" aria-label="Copy" data-toggle="tooltip" data-placement="bottom" title="Copy">content_copy</md-icon>

                                <md-icon ng-click="ctrl.deletegroup( { groupID : record.id } )" aria-label="Delete" md-font-set="material-icons" class="mt2-icon-black"
                                     data-toggle="tooltip" data-placement="bottom" title="Delete">delete</md-icon>
                            </td>
                            <td md-cell>@{{ record.id }}</td>

                            <td md-cell>
                                <a ng-click="clientGroup.loadClients(record.id)">
                                    @{{ record.name }}
                                </a>
                            </td>

                        </tr>
                        <tr md-row ng-repeat-end ng-show="clientGroup.clientFeedMap[record.id]">
                            <td md-cell colspan="4" class="mt2-table-cell-center">
                                <md-card>
                                    <md-table-container>
                                        <table md-table class="mt2-table-cell-center">
                                            <thead md-head>
                                                <tr md-row>
                                                    <td md-column>Feed</td>
                                                    <td md-column>Status</td>
                                                </tr>
                                            </thead>

                                            <tbody md-body>
                                                <tr md-row ng-repeat="feed in clientGroup.clientMap[record.id] track by $index">
                                                    <td md-cell>@{{ feed.name }}</td>
                                                    <td md-cell ng-class="{ 'mt2-bg-success' : feed.status == 'A' , 'mt2-bg-warn' : feed.status == 'P' , 'mt2-bg-danger' : feed.status == 'D' }"><strong>@{{ feed.status == 'A' ? 'Active' : feed.status == 'P' ? 'Paused' : 'Inactive'  }}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </md-table-container>

                                </md-card>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </md-table-container>

            <md-content class="md-mt2-zeta-theme md-hue-2">
                <md-table-pagination md-limit="clientGroup.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="clientGroup.currentPage" md-total="@{{clientGroup.clientGroupTotal}}" md-on-paginate="clientGroup.loadClientGroups" md-page-select></md-table-pagination>
            </md-content>
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/clientgroup.js"></script>
@stop
