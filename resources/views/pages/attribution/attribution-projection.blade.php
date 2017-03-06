@extends( 'layout.default' )

@section( 'title' , 'Attribution Model Projection' )
@section( 'container' , 'container-fluid' )
@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'content' )
    <div style="width:600px">
        <div class="panel mt2-theme-panel center-block">
            <div class="panel-heading">
                <h3 class="panel-title">Projection Report Options</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Models</span>

                                <select name="esp_account_search" id="esp_account_search" class="form-control" ng-model="attr.proj.modelId" ng-disabled="false">
                                    {{$first = true}}
                                    @foreach ( $models as $model )
                                    <option value="{{ $model['id'] }}"
                                        @if( $first )
                                            ng-init='attr.proj.modelId = "{{$model[ 'id' ]}}"'
                                        @endif
                                    >{{ $model['name'] }}</option>
                                    {{$first = false}}
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <md-datepicker flex="50" name="dateField" ng-change="attr.updateProjectionDateRange()" ng-model="attr.startDateState"
                                       md-placeholder="Start Date"></md-datepicker>
                        <md-datepicker flex="50" name="dateField" ng-change="attr.updateProjectionDateRange()" ng-model="attr.endDateState"
                                       md-placeholder="End date"></md-datepicker>
                    </div>
                </div>

                <div class="pull-right">
                    <button class="btn mt2-theme-btn-primary btn-sm" ng-click="attr.loadProjectionRecords()">Apply</button>
                </div>
            </div>

        </div>
    </div>

    <div  ng-init="attr.loadProjectionRecords()">
        <table md-table>
            <table md-table>
                <thead md-head>
                <tr md-row>
                    <th class="md-table-header-override-whitetext" md-column>Client</th>
                    <th class="md-table-header-override-whitetext" md-column>Feed</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Uniques</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Live Level</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Model Level</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Live CPA Revenue</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Model CPA Revenue</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Live CPA Revshare</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Model CPA Revshare</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Live CPC Revenue</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Model CPC Revenue</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Live CPC Revshare</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Model CPC Revshare</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Live CPM Revenue</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Model CPM Revenue</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Live CPM Revshare</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Model CPM Revshare</th>
                </tr>
                </thead>
                <tbody md-body>
                <tr md-row ng-repeat="record in attr.projectionRecords track by $index" layout-align="center center">
                    <td md-cell class="text-center">@{{ ( record.clientName || 'Unknown Client' ) + ( record.clientId > 0 ? ( ' (' + record.clientId + ')' ) : '' ) }}</td>
                    <td md-cell class="text-center">@{{ ( record.feedName || 'Unknown Feed' ) + ( record.feedId > 0 ?  ( ' (' + record.feedId + ')' ) : '' ) }}</td>
                    <td md-cell class="text-center" ng-bind="record.uniques"></td>
                    <td md-cell class="text-center" ng-bind="record.liveLevel"></td>
                    <td md-cell class="text-center" ng-bind="record.modelLevel"></td>
                    <td md-cell class="text-center" ng-bind="record.liveCpaRevenue | currency:'$':2"></td>
                    <td md-cell class="text-center" ng-bind="record.modelCpaRevenue | currency:'$':2"></td>
                    <td md-cell class="text-center" ng-bind="record.liveCpaRevshare | currency:'$':2"</td>
                    <td md-cell class="text-center" ng-bind="record.modelCpaRevshare | currency:'$':2"</td>
                    <td md-cell class="text-center" ng-bind="record.liveCpcRevenue | currency:'$':2"</td>
                    <td md-cell class="text-center" ng-bind="record.modelCpcRevenue | currency:'$':2"</td>
                    <td md-cell class="text-center" ng-bind="record.liveCpcRevshare | currency:'$':2"</td>
                    <td md-cell class="text-center" ng-bind="record.modelCpcRevshare | currency:'$':2"</td>
                    <td md-cell class="text-center" ng-bind="record.liveCpmRevenue | currency:'$':2"</td>
                    <td md-cell class="text-center" ng-bind="record.modelCpmRevenue | currency:'$':2"</td>
                    <td md-cell class="text-center" ng-bind="record.liveCpmRevshare | currency:'$':2"</td>
                    <td md-cell class="text-center" ng-bind="record.modelCpmRevshare | currency:'$':2"</td>
                </tr>
                </tbody>
            </table>
        </table>
    </div>
@stop


<?php Assets::add(
        [
                'resources/assets/js/attribution/AttributionController.js',
                'resources/assets/js/attribution/AttributionApiService.js',
                'resources/assets/js/feed/FeedApiService.js'
        ],
        'js','pageLevel')
?>

