@extends( 'layout.default' )

@section( 'title' , 'Source URL Search' )

@section( 'angular-controller' , 'ng-controller="SourceUrlSearchController as source"' )

@section( 'content' )
    <div class="panel mt2-theme-panel" ng-init="source.loadFeedList(); source.setClientList( {{ $clients }} ); source.setVerticalList( {{ $feedVerticals }} )">
        <div class="panel-heading">
            <div class="panel-title">Source URL Search</div>
        </div>
        <div class="panel-body">

            <div class="form-group">
                <label>Source URL</label>
                <input placeholder="Source URL" value="" class="form-control" ng-model="source.search.source_url" name="source_url" type="text">
            </div>

            <div class="form-group">
                <label><h4>Filter by Clients</h4></label>
                <lite-membership-widget height="200" recordlist="source.clientList" chosenrecordlist="source.selectedClients" availablerecordtitle="'Available Clients'" chosenrecordtitle="'Selected Clients'" updatecallback="source.updateCurrentClientList()"></lite-membership-widget>
                <div class="has-error">
                    <div class="help-block" ng-show="source.formErrors.clientIds">
                        <div ng-repeat="error in source.formErrors.clientIds">
                            <div ng-bind="error"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label><h4>Filter by Feeds</h4></label>
                <lite-membership-widget height="200" recordlist="source.feedList" chosenrecordlist="source.selectedFeeds" availablerecordtitle="'Available Feeds'" chosenrecordtitle="'Selected Feeds'" updatecallback="source.updateCurrentFeedList()"></lite-membership-widget>
                <div class="has-error">
                    <div class="help-block" ng-show="source.formErrors.feedIds">
                        <div ng-repeat="error in source.formErrors.feedIds">
                            <div ng-bind="error"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label><h4>Filter by Verticals</h4></label>
                <lite-membership-widget height="200" recordlist="source.verticalList" chosenrecordlist="source.selectedVerticals" availablerecordtitle="'Available Verticals'" chosenrecordtitle="'Selected Verticals'" updatecallback="source.updateCurrentVerticalList()"></lite-membership-widget>
                <div class="has-error">
                    <div class="help-block" ng-show="source.formErrors.verticalIds">
                        <div ng-repeat="error in source.formErrors.verticalIds">
                            <div ng-bind="error"></div>
                        </div>
                    </div>
                </div>
            </div>

            <label><h4>Filter by Date Range <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="When using the custom date range, be sure to enter oldest date as 'Start Date' and most recent date as 'End Date'.">help</md-icon></h4></label>
            <div class="form-group text-center">
                <label class="radio-inline">
                    <input type="radio" name="dateRange" ng-model="source.dateRange" value="15" ng-click="source.updateSearchDate(15)"> 15 Days
                </label>
                <label class="radio-inline">
                    <input type="radio" name="dateRange" ng-model="source.dateRange" value="30" ng-click="source.updateSearchDate(30)"> 30 Days
                </label>
                <label class="radio-inline">
                    <input type="radio" name="dateRange" ng-model="source.dateRange" value="60" ng-click="source.updateSearchDate(60)"> 60 Days
                </label>
                <label class="radio-inline">
                    <input type="radio" name="dateRange" ng-model="source.dateRange" value="custom" ng-click="source.updateSearchDate('custom')"> Custom
                </label>
            </div>

            <div class="form-group text-center">
                <md-input-container>
                    <label>Start Date</label>
                    <md-datepicker ng-disabled="source.dateRange != 'custom'" flex="50" name="dateField" ng-change="source.updateSearchDate('custom')" ng-model="source.rawStartDate" md-placeholder="MM/DD/YYYY"></md-datepicker>
                </md-input-container>
                <md-input-container>
                    <label>End Date</label>
                    <md-datepicker ng-disabled="source.dateRange != 'custom'" flex="50" name="dateField" ng-change="source.updateSearchDate('custom')" ng-model="source.rawEndDate" md-placeholder="MM/DD/YYYY"></md-datepicker>
                </md-input-container>

                <div class="help-block" ng-show="source.formErrors.startDate || source.formErrors.endDate">
                    <div ng-repeat="error in source.formErrors.startDate">
                        <span class="text-danger" ng-bind="error"></span>
                    </div>
                    <div ng-repeat="error in source.formErrors.endDate">
                        <span class="text-danger" ng-bind="error"></span>
                    </div>
                </div>
            </div>

            <div class="checkbox text-center">
                <label>
                    <input type="checkbox" ng-value="true" ng-model="source.search.exportFile">
                    Export to excel file?
                </label>
            </div>
        </div>
        <div class="panel-footer">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="source.searchSourceUrl()" ng-disabled="source.isSearching" type="submit" value="Search">
        </div>
    </div>


    <md-table-container>
        <table md-table md-progress="source.queryPromise">
            <thead md-head class="mt2-theme-thead">
                <tr md-row>
                    <th md-column class="md-table-header-override-whitetext">Client</th>
                    <th md-column class="md-table-header-override-whitetext">Feed Name</th>
                    <th md-column class="md-table-header-override-whitetext">Source URL</th>
                    <th md-column class="md-table-header-override-whitetext">Count</th>
                </tr>
            </thead>
            <tbody md-body>
                <tr md-row ng-repeat="record in source.recordCounts track by $index">
                    <td md-cell ng-bind="::record.clientName"></td>
                    <td md-cell ng-bind="::record.feedName"></td>
                    <td md-cell ng-bind="::record.sourceUrl"></td>
                    <td md-cell ng-bind="::record.count"></td>
                </tr>
            </tbody>
        </table>
    </md-table-container>

    <span id="tableLoaded"></span>

@stop

<?php Assets::add(
        ['resources/assets/js/pages/SourceUrlSearchController.js',
        'resources/assets/js/feed/FeedApiService.js'],'js','pageLevel') ?>
