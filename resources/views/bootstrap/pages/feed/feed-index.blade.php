@extends( 'bootstrap.layout.default' )

@section( 'container' , 'container-fluid' )

@section( 'title' , 'Feeds' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('feed.add'))
        <li><a ng-href="/feed/create" target="_self">Add Feed</a></li>
    @endif
    @if (Sentinel::hasAccess('api.feed.exportlist'))
        <li><a ng-click="feed.exportList()" target="_self">Export Feeds</a></li>
    @endif
@stop

@section( 'content' )
<div ng-init="feed.loadFeeds()">
    <md-table-container>
        <table md-table class="mt2-table-large" md-progress="feed.queryPromise">
            <thead md-head md-order="feed.sort" md-on-reorder="feed.loadFeeds" class="mt2-theme-thead">
                <tr md-row>
                    <th md-column class="mt2-table-btn-column"></th>
                    <th md-column md-order-by="id" class="md-table-header-override-whitetext">ID</th>
                    <th md-column md-order-by="clientName" class="md-table-header-override-whitetext">Client</th>
                    <th md-column md-order-by="name" class="md-table-header-override-whitetext">Name</th>
                    <th md-column md-order-by="short_name" class="md-table-header-override-whitetext">Short Name</th>
                    @if ( Sentinel::inRole( 'fleet-admiral' ) )
                    <th md-column class="md-table-header-override-whitetext">Password</th>
                    @endif
                    <th md-column md-order-by="status" class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                    <th md-column md-order-by="feedVertical" class="md-table-header-override-whitetext mt2-cell-left-padding">Feed Vertical</th>
                    <th md-column md-order-by="country" class="md-table-header-override-whitetext">Country</th>
                    <th md-column md-order-by="feedType" class="md-table-header-override-whitetext">Feed Type</th>
                    <th md-column md-order-by="party" class="md-table-header-override-whitetext">Party</th>
                    <th md-column class="md-table-header-override-whitetext">Source URL</th>
                    <th md-column md-order-by="created_at" class="md-table-header-override-whitetext">Created</th>
                    <th md-column md-order-by="updated_at" class="md-table-header-override-whitetext">Updated</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="record in feed.feeds track by $index">
                    <td md-cell class="mt2-table-btn-column">
                        <div layout="row" layout-align="center center">
                            <a ng-href="@{{'/feed/edit/' + record.id}}" target="_self" aria-label="Edit" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                            </a>

                            <a ng-href="/feed/file/fieldorder/@{{record.id}}" target="_self">
                                <md-icon aria-label="Edit Field Order" data-toggle="tooltip" data-placement="bottom" title="Edit Field Order" md-font-set="material-icons" class="mt2-icon-black">reorder</md-icon>
                            </a>
                        </div>
                    </td>
                    <td md-cell ng-bind="record.id"></td>
                    <td md-cell ng-bind="record.clientName" nowrap></td>
                    <td md-cell ng-bind="record.name" nowrap></td>
                    <td md-cell ng-bind="record.short_name"></td>
                    @if ( Sentinel::inRole( 'fleet-admiral' ) )
                    <td md-cell ng-bind="record.password"></td>
                    @endif
                    <td md-cell class="mt2-table-cell-center" ng-class="{ 'bg-success' : record.status == 'Active' , 'bg-warning' : record.status == 'Paused' , 'bg-danger' : record.status == 'Inactive' }" ng-bind="record.status">
                    </td>
                    <td md-cell class="mt2-cell-left-padding" ng-bind="record.feedVertical" nowrap></td>
                    <td md-cell ng-bind="record.country"></td>
                    <td md-cell ng-bind="record.feedType" nowrap></td>
                    <td md-cell ng-bind="record.party"></td>
                    <td md-cell ng-bind="record.source_url"></td>
                    <td md-cell nowrap ng-bind="::app.formatDate( record.created_at )"></td>
                    <td md-cell nowrap ng-bind="::app.formatDate( record.updated_at )"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="14">
                        <md-content class="md-mt2-zeta-theme md-hue-2">
                            <md-table-pagination md-limit="feed.paginationCount" md-limit-options="feed.paginationOptions" md-page="feed.currentPage" md-total="@{{feed.feedTotal}}" md-on-paginate="feed.loadFeeds" md-page-select></md-table-pagination>
                        </md-content>
                    </td>
                </tr>
            </tfoot>
        </table>
    </md-table-container>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/feed/FeedController.js',
        'resources/assets/js/bootstrap/feed/FeedApiService.js',
        'resources/assets/js/bootstrap/feed/FeedUrlModalDirective.js'],'js','pageLevel') ?>

