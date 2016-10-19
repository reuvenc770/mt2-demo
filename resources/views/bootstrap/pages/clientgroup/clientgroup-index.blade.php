@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Feed Groups' )

@section( 'angular-controller' , 'ng-controller="ClientGroupController as clientGroup"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('clientgroup.add'))
        <li><a ng-click="clientGroup.viewAdd()">Add Feed Group</a></li>
    @endif
@stop

@section( 'content' )
<div ng-init="clientGroup.loadClientGroups()">
    <md-card >
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
                            <div layout="row" layout-align="center center">
                                <md-button class="md-icon-button" aria-label="View Feeds"
                                            ng-click="clientGroup.clientFeedMap[record.id]=!clientGroup.clientFeedMap[record.id]">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-hide="clientGroup.clientFeedMap[record.id]">expand_more</md-icon>
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-show="clientGroup.clientFeedMap[record.id]">expand_less</md-icon>
                                </md-button>
                                <md-button class="md-icon-button" ng-href="@{{ '/clientgroup/edit/' + record.id }}" target="_self" aria-label="Edit">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                    <md-tooltip md-direction="bottom">Edit</md-tooltip>
                                </md-button>

                                <md-button class="md-icon-button" ng-click="clientGroup.copyClientGroup( record.id )" aria-label="Copy">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">content_copy</md-icon>
                                    <md-tooltip md-direction="bottom">Copy</md-tooltip>
                                </md-button>

                                <md-button class="md-icon-button" ng-click="ctrl.deletegroup( { groupID : record.id } )" aria-label="Delete">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">clear</md-icon>
                                    <md-tooltip md-direction="bottom">Delete</md-tooltip>
                                </md-button>
                            </div>
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
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/clientgroup/ClientGroupController.js',
        'resources/assets/js/bootstrap/clientgroup/ClientGroupApiService.js',
        'resources/assets/js/bootstrap/feed/FeedApiService.js'],'js','pageLevel') ?>
