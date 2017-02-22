@extends( 'layout.default' )

@section( 'title' , 'Attribution Model Projection' )
@section( 'container' , 'container-fluid' )
@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('api.attribution.projection.report'))
        <li><a ng-click="attr.refreshProjectionPage()" aria-label="Refresh Page">
            Refresh</a>
        </li>
    @endif
@stop

@section( 'content' )
    <div  ng-init="attr.initProjectionPage()">
        <table md-table>
            <table md-table>
                <thead md-head>
                <tr md-row>
                    <th class="md-table-header-override-whitetext" md-column>Client</th>
                    <th class="md-table-header-override-whitetext" md-column>Feed</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Uniques</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Live Level</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Model Level</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Live Revenue</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Model Revenue</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Live Revshare</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Model Revshare</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Live CPM Revenue</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Model CPM Revenue</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Live CPM Revshare</th>
                    <th class="md-table-header-override-whitetext" md-column md-numeric>Model CPM Revshare</th>
                </tr>
                </thead>
                <tbody md-body>
                <tr ng-repeat="record in attr.reportRecords track by $index">
                    <td ng-bind="record.clientName"></td>
                    <td ng-bind="record.feedName"></td>
                    <td ng-bind="record.uniques"></td>
                    <td ng-bind="record.liveLevel"></td>
                    <td ng-bind="record.modelLevel"></td>
                    <td ng-bind="record.liveRevenue"></td>
                    <td ng-bind="record.modelRevenue"></td>
                    <td ng-bind="record.liveRevshare"></td>
                    <td ng-bind="record.modelRevshare"></td>
                    <td ng-bind="record.liveCpmRevenue"></td>
                    <td ng-bind="record.modelCpmRevenue"></td>
                    <td ng-bind="record.liveCpmRevshare"></td>
                    <td ng-bind="record.modelCpmRevshare"></td>
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
                'resources/assets/js/attribution/AttributionProjectionService.js',
                'resources/assets/js/report/ThreeMonthReportService.js',
                'resources/assets/js/report/ReportApiService.js',
                'resources/assets/js/feed/FeedApiService.js', //REFACTOR WHEN FEEDS ARE REFACTORED
        ],
        'js','pageLevel')
?>

