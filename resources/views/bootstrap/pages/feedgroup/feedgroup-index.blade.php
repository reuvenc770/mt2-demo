@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Feed Groups' )

@section( 'angular-controller' , 'ng-controller="FeedGroupController as feedGroup"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('feedgroup.add'))
        <li><a ng-href="/feedgroup/create" target="_self">Add Feed Group</a></li>
    @endif
@stop

@section( 'content' )
<div ng-init="feedGroup.loadFeedGroups()">
    <md-card >
        <md-table-container>
            <table md-table md-progress="feedGroup.queryPromise">
                <thead md-head>
                    <tr md-row>
                        <th md-column></th>
                        <th md-column class="md-table-header-override-whitetext">Name</th>
                    </tr>
                </thead>
                <tbody md-body>
                    <tr md-row ng-repeat="record in feedGroup.feedGroups">
                        <td md-cell>
                                <md-button class="md-icon-button" ng-href="@{{ '/feedgroup/edit/' + record.id }}" target="_self" aria-label="Edit">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                    <md-tooltip md-direction="bottom">Edit</md-tooltip>
                                </md-button>
                        </td>
                        <td md-cell ng-bind="record.name">
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">
                            <md-content class="md-mt2-zeta-theme md-hue-2">
                                <md-table-pagination md-limit="feedGroup.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="feedGroup.currentPage" md-total="@{{feedGroup.feedGroupTotal}}" md-on-paginate="feedGroup.loadFeedGroups" md-page-select></md-table-pagination>
                            </md-content>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </md-table-container>

    </md-card>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/feedgroup/FeedGroupController.js',
        'resources/assets/js/bootstrap/feedgroup/FeedGroupApiService.js',
        'resources/assets/js/bootstrap/feed/FeedApiService.js'],'js','pageLevel') ?>
