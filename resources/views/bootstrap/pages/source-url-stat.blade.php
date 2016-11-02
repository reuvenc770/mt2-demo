@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Source URL Stats' )

@section( 'content' )
    <div class="panel panel-primary" ng-controller="SourceUrlStatController as source" ng-init="source.loadFeedList(); source.setVerticalList( {{ $feedVerticals }} )">
        <div class="panel-heading">
            <div class="panel-title">Source URL Stats</div>
        </div>
        <div class="panel-body">

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

            <label><h4>Filter by Date Range</h4></label>
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
            </div>

            <div class="checkbox text-center">
                <label>
                    <input type="checkbox" ng-value="true" ng-model="source.search.exportFile">
                    Export to excel file?
                </label>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn btn-primary btn-block" ng-click="" ng-disabled="source.isSearching" type="submit" value="Search">
            </div>
        </div>
    </div>


    <md-table-container>
        <table md-table>
            <thead md-head>
                <tr md-row>
                    <th md-column class="md-table-header-override-whitetext">Client</th>
                    <th md-column class="md-table-header-override-whitetext">Feed Name</th>
                    <th md-column class="md-table-header-override-whitetext">Source URL</th>
                    <th md-column class="md-table-header-override-whitetext">Count</th>
                    <th md-column class="md-table-header-override-whitetext">Vertical</th>
                </tr>
            </thead>
            <tbody md-body>
                <tr md-row>
                    <td md-cell></td>
                    <td md-cell></td>
                    <td md-cell></td>
                    <td md-cell></td>
                    <td md-cell></td>
                </tr>
            </tbody>
        </table>
    </md-table-container>

@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/pages/SourceUrlStatController.js',
        'resources/assets/js/bootstrap/pages/SourceUrlStatApiService.js',
        'resources/assets/js/bootstrap/feed/FeedApiService.js'],'js','pageLevel') ?>