@extends( 'bootstrap.layout.default' )

@inject( 'projection' , 'App\Collections\Attribution\ProjectionReportCollection' )

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
<md-content class="md-mt2-zeta-theme" layout="row" layout-align="center center" ng-init="attr.initProjectionPage()" flex="none">
    <div class="md-whiteframe-4dp" layout="column" layout-margin flex="none" ng-init="attr.initProjectionChart()">
            <md-toolbar layout="row" class="md-mt2-zeta-theme md-hue-2" layout-fill>
                <div class="md-toolbar-tools">
                    Projection Chart
                </div>
            </md-toolbar>

            <md-content flex="grow" layout-fill>
                <div id="projectionChart" flex="100" style="overflow: hidden;"></div>
            </md-content>

            <md-toolbar layout="row" class="md-mt2-zeta-theme md-hue-2" layout-fill>
                <div class="md-toolbar-tools">
                    Projection Report
                </div>
            </md-toolbar>

            <md-table-container layout-fill>
                <md-table>
                <table md-table>
                    <thead md-head>
                        <tr md-row>
                            <th class="md-table-header-override-whitetext" md-column>Client</th>
                            <th class="md-table-header-override-whitetext" md-column>Feed</th>
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
                        {!! $projection->getReportRowsHtml( $modelId ) !!}
                    </tbody>
                </table>
            </md-table>
                </md-table-container>
    </div>
</md-content>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
@stop


<?php Assets::add(
        [
                'resources/assets/js/bootstrap/attribution/AttributionController.js',
                'resources/assets/js/bootstrap/attribution/AttributionApiService.js',
                'resources/assets/js/bootstrap/attribution/AttributionProjectionService.js',
                'resources/assets/js/bootstrap/attribution/AttributionModelTableDirective.js',
                'resources/assets/js/bootstrap/report/ThreeMonthReportService.js',
                'resources/assets/js/bootstrap/report/ReportApiService.js',
                'resources/assets/js/feed/FeedApiService.js', //REFACTOR WHEN FEEDS ARE REFACTORED
        ],
        'js','pageLevel')
?>

