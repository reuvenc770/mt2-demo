@extends( 'layout.default' )

@section( 'container' , 'container-fluid' )

@section( 'title' , 'Feeds' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )
@section( 'cacheTag' , 'Feed' )
@section( 'page-menu' )
    @if (Sentinel::hasAccess('feed.add'))
        <li><a ng-href="/feed/create" target="_self">Add Feed</a></li>
    @endif
    @if ( Sentinel::hasAccess('api.feed.exportlist') && Sentinel::inRole( 'fleet-admiral' ) )
        <li><a ng-click="feed.exportList()" target="_self">Export Feeds</a></li>
    @endif
@stop

@section( 'content' )
<div ng-init="feed.loadFeeds()">
    <div style="width:800px">
        <div class="panel mt2-theme-panel center-block">
            <div class="panel-heading">
                <h3 class="panel-title">Search Feeds
                    <md-icon md-font-set="material-icons" class="mt2-icon-white material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="Search fields with an asterisk [*] indicate that it will be a fuzzy search. The search phrase must match the beginning of the actual result or no results will be returned.">help</md-icon>
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    @if ( Sentinel::inRole( 'fleet-admiral' ) )
                    <div class="col-md-6">
                        <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">Client Name*</span>
                            <input type="text" id="search-client-name" class="form-control" value="" ng-model="feed.search.client_name"/>
                        </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">Feed Name*</span>
                            <input type="text" id="search-feed-name" class="form-control" value="" ng-model="feed.search.feed_name"/>
                        </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">Feed Short Name*</span>
                            <input type="text" id="search-feed-short-name" class="form-control" value="" ng-model="feed.search.feed_short_name"/>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Status</span>
                                <select name="search-status" id="search-status" class="form-control" ng-model="feed.search.status">
                                    <option value="">---</option>
                                    <option value="Active">Active</option>
                                    <option value="Paused">Paused</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Feed Vertical</span>
                                <select name="search-feed-vertical" id="search-feed-vertical" class="form-control" ng-model="feed.search.feed_vertical_id">
                                    <option value="">---</option>
                                    @foreach ( $clientTypes as $clientType )
                                        <option value="{{ $clientType['id'] }}">{{ $clientType['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Country</span>
                                <select name="search-country" id="search-country" class="form-control" ng-model="feed.search.country">
                                    <option value="">---</option>
                                    <option value="1">United States</option>
                                    <option value="2">United Kingdom</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Feed Type</span>
                                <select name="search-feed-type" id="search-feed-type" class="form-control" ng-model="feed.search.feed_type_id">
                                    <option value="">---</option>
                                    @foreach ( $feedTypes as $feedType )
                                        <option value="{{ $feedType['id'] }}">{{ $feedType['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Party</span>
                                <select name="search-party" id="search-party" class="form-control" ng-model="feed.search.party">
                                    <option value="">---</option>
                                    <option value="1">1st Party</option>
                                    <option value="2">2nd Party</option>
                                    <option value="3">3rd Party</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Source URL</span>
                                <input type="text" id="search-source-url" class="form-control" value="" ng-model="feed.search.source_url"/>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="pull-right">
                    <button class="btn btn-sm mt2-theme-btn-secondary" ng-click="feed.resetSearch()">Reset</button>
                    <button class="btn btn-sm mt2-theme-btn-primary" ng-click="feed.searchFeeds()">Search</button>
                </div>
            </div>
        </div>
    </div>

    <md-table-container>
        <table md-table class="mt2-table-large" md-progress="feed.queryPromise">
            <thead md-head md-order="feed.sort" md-on-reorder="feed.sortCurrentRecords" class="mt2-theme-thead">
                <tr md-row>
                    <th md-column class="mt2-table-btn-column"></th>
                    <th md-column md-order-by="id" class="md-table-header-override-whitetext">ID</th>
                    @if ( Sentinel::inRole( 'fleet-admiral' ) )
                    <th md-column md-order-by="clientName" class="md-table-header-override-whitetext">Client</th>
                    @endif
                    <th md-column md-order-by="name" class="md-table-header-override-whitetext">Feed Name</th>
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
                    @if ( Sentinel::inRole( 'fleet-admiral' ) )
                    <td md-cell ng-bind="record.clientName" nowrap></td>
                    @endif
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
        ['resources/assets/js/feed/FeedController.js',
        'resources/assets/js/feed/FeedApiService.js'],'js','pageLevel') ?>

