@extends( 'layout.default' )

@section( 'title' , 'Attribution Model Projection' )

@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('api.attribution.projection.report'))
        <md-button ng-click="attr.refreshProjectionPage()" aria-label="Refresh Page">
            <span>Refresh</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<md-content class="md-mt2-zeta-theme" layout="row" layout-align="center center" ng-init="attr.initProjectionPage()">
    <div class="md-whiteframe-4dp" layout="column" layout-margin flex="95" ng-init="attr.initProjectionChart()">
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

            <md-table-container layout-fill ng-init="attr.loadProjectionRecords()">
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
                        <tr ng-repeat="record in attr.projectionRecords" ng-hide="record.live.level == 255" ng-class="{ 'mt2-total-row' : record.client_stats_grouping_id }" md-row>
                            <td md-cell>@{{ record.client_stats_grouping_id ? attr.clientNameMap[ record.client_stats_grouping_id ] + ' (' + record.client_stats_grouping_id + ')' : '' }}</td>
                            <td md-cell>@{{ record.feed_id ? attr.feedNameMap[ record.feed_id ] + ' (' + record.feed_id + ')' : '' }}</td>
                            <td md-cell ng-bind="record.live.level"></td>
                            <td ng-class="{ 'mt2-proj-increase-bg' : record.model.level > record.live.level , 'mt2-proj-decrease-bg' : record.model.level < record.live.level }" md-cell ng-bind="record.model.level"></td>
                            <td md-cell>$@{{ record.live.standard_revenue.toFixed( 2 ) }}</td>
                            <td ng-class="{ 'mt2-proj-increase-bg' : record.model.standard_revenue > record.live.standard_revenue , 'mt2-proj-decrease-bg' : record.model.standard_revenue < record.live.standard_revenue }" md-cell>$@{{ record.model.standard_revenue.toFixed( 2 ) }}</td>
                            <td md-cell>$@{{ record.live.standard_revshare.toFixed( 2 ) }}</td>
                            <td ng-class="{ 'mt2-proj-increase-bg' : record.model.standard_revshare > record.live.standard_revshare , 'mt2-proj-decrease-bg' : record.model.standard_revshare < record.live.standard_revshare }" md-cell>$@{{ record.model.standard_revshare.toFixed( 2 ) }}</td>
                            <td md-cell>$@{{ record.live.cpm_revenue.toFixed( 2 ) }}</td>
                            <td ng-class="{ 'mt2-proj-increase-bg' : record.model.cpm_revenue > record.live.cpm_revenue , 'mt2-proj-decrease-bg' : record.model.cpm_revenue < record.live.cpm_revenue }" md-cell>$@{{ record.model.cpm_revenue.toFixed( 2 ) }}</td>
                            <td md-cell>$@{{ record.live.cpm_revshare.toFixed( 2 ) }}</td>
                            <td ng-class="{ 'mt2-proj-increase-bg' : record.model.cpm_revshare > record.live.cpm_revshare , 'mt2-proj-decrease-bg' : record.model.cpm_revshare < record.live.cpm_revshare }" md-cell>$@{{ record.model.cpm_revshare.toFixed( 2 ) }}</td>
                        </tr>
                    </tbody>
                </table>
            </md-table>
    </div>
</md-content>
@stop

@section( 'pageIncludes' )
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="js/attribution.js"></script>
@stop
