@extends( 'layout.default' )

@section( 'title' , 'Attribution Model Projection' )

@section( 'angular-controller' , 'ng-controller="AttributionProjectionController as proj"' )

@section( 'page-menu' )
@stop

@section( 'content' )
<md-content class="md-mt2-zeta-theme" layout="row" layout-align="center center" ng-init="">
    <div class="md-whiteframe-4dp" layout="column" layout-margin flex="100" flex-gt-lg="70" ng-init="proj.initChart()">
            <md-toolbar layout="row" class="md-hue-3" layout-fill>
                <div class="md-toolbar-tools">
                    Projection Chart
                </div>
            </md-toolbar>

            <md-content flex="grow" layout-fill>
                <div id="projectionChart" flex="100" style="overflow: hidden;"></div>
            </md-content>

            <md-toolbar layout="row" class="md-hue-3" layout-fill>
                <div class="md-toolbar-tools">
                    Projection Report
                </div>
            </md-toolbar>

            <md-table-container layout-fill ng-init="proj.loadRecords()">
                <table md-table>
                    <thead md-head>
                        <tr md-row>
                            <th class="md-table-header-override-whitetext" md-column>Client</th>
                            <th class="md-table-header-override-whitetext" md-column>Feed</th>
                            <th class="md-table-header-override-whitetext" md-column md-numeric>Live Level</th>
                            <th class="md-table-header-override-whitetext" md-column md-numeric>Model Level</th>
                            <th class="md-table-header-override-whitetext" md-column md-numeric>Live Revenue</th>
                            <th class="md-table-header-override-whitetext" md-column md-numeric>Model Revenue</th>
                        </tr>
                    </thead>
                    <tbody md-body>
                        <tr ng-repeat="record in proj.records" ng-class="{ 'mt2-total-row' : record.client_stats_grouping_id }" md-row>
                            <td md-cell>@{{ record.client_stats_grouping_id ? proj.listOwnerNameMap[ record.client_stats_grouping_id ] + ' (' + record.client_stats_grouping_id + ')' : '' }}</td>
                            <td md-cell>@{{ record.client_id ? proj.clientNameMap[ record.client_id ] + ' (' + record.client_id + ')' : '' }}</td>
                            <td md-cell ng-bind="record.live.level"></td>
                            <td md-cell ng-bind="record.model.level"></td>
                            <td md-cell ng-bind="record.live.revenue"></td>
                            <td md-cell ng-bind="record.model.revenue"></td>
                        </tr>
                    </tbody>
                </table>
            </md-table>
    </div>
</md-content>
@stop

@section( 'pageIncludes' )
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="js/projectionAttribution.js"></script>
@stop
