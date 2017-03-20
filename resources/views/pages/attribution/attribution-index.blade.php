@extends( 'layout.default' )

@section( 'title' , 'Attribution Model' )
@section( 'container' , 'container-fluid' )
@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
        @if (Sentinel::hasAccess('attributionModel.add'))
        <li><a ng-href="{{ route( 'attributionModel.add' ) }}" target="_self" >Add Model</a></li>
        @endif
        @if (Sentinel::hasAccess('attributionProjection.show'))
        <li><a ng-href="{{ 'attribution/projection' }}" target="_self" >Projections</a></li>
        @endif
@stop

@section( 'content' )

<div  ng-init="attr.initIndexPage()">
    <div class="alert alert-info" role="alert"> <strong>Heads up!</strong> Highlighted row is currently live. Attribution is automated to run once a day. To manually update record attribution for <em>live model</em> click 'Run Attribution' button <md-icon md-font-set="material-icons" class="mt2-icon-black icon-xs">monetization_on</md-icon> on the live model row. To manually update record attribution for <em>inactive model</em> select the inactive model and click 'Run Attribution' button <md-icon md-font-set="material-icons" class="mt2-icon-black icon-xs">monetization_on</md-icon> for the inactive model you want to run. After manually running live attribution, reports will update but you will need to go to the <a ng-href="{{ route( 'report.list' ) }}" target="_self">reports page</a> to view the reports. If attribution is running for a model you will not be able to edit that model until the run is complete.</div>

<div class="navbar navbar-topper navbar-primary" role="navigation">
    <div class="container-fluid">
        <a class="navbar-brand pull-right">
            <md-icon md-font-set="material-icons" class="mt2-icon-white material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="left" data-content="After editing a model, attribution does not run again. You will need to wait until the next automated run or manually click 'Run Attribution'.">help</md-icon>
        </a>
        <a class="navbar-brand pull-left md-table-header-override-whitetext">Models</a>

        <ul class="nav navbar-nav navbar-right" ng-show="attr.showModelActions">
                <li><h2 class="md-toolbar-tools" ng-show="attr.selectedModel[ 0 ].processing"><a>This model is running. Please check back later for projections.</a></h2></li>
        </ul>
    </div>
</div>
    <md-table-container>
        <table md-table md-progress="attr.modelQueryPromise" md-row-select ng-model="attr.selectedModel">
            <thead md-head md-order="" md-on-reorder="" class="mt2-theme-thead">
            <tr md-row>
                <th md-column class="mt2-table-btn-column"></th>
                <th md-column md-order-by="" class="md-table-header-override-whitetext">Model Name</th>
                <th md-column md-order-by="" class="md-table-header-override-whitetext">Processing</th>
                <th md-column md-order-by="" class="md-table-header-override-whitetext">Created</th>
                <th md-column md-order-by="" class="md-table-header-override-whitetext">Updated</th>
            </tr>
            </thead>

            <tbody md-body>
            <tr
                    md-row
                    ng-class="{ 'mt2-live-row' : model.live == 1 }"
                    ng-repeat="model in attr.models track by $index">
                <td md-cell class="mt2-table-btn-column" nowrap>
                    <a ng-hide="model.processing" ng-href="@{{ 'attribution/edit/' + model.id }}" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon></a>

                    @if (Sentinel::hasAccess('api.attribution.run'))
                    <a ng-hide="model.processing" ng-click="attr.runAttribution( model.live === 1 ? '' : model.id )" aria-label="Run Attribution" data-toggle="tooltip" data-placement="bottom" title="Run Attribution">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">monetization_on</md-icon>
                    </a>
                    @endif

                    @if (Sentinel::hasAccess('api.attribution.model.setlive'))
                    <a ng-hide="model.live === 1 || model.processing" ng-click="attr.setModelLive( model.id )" aria-label="Set Live" data-toggle="tooltip" data-placement="bottom" title="Set Live">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">play_circle_outline</md-icon>
                    </a>
                    @endif
                </td>
                <td md-cell ng-bind="model.name" nowrap></td>
                <td md-cell ng-bind="model.processing ? 'Running' : 'Completed'"></td>
                <td md-cell ng-bind="::app.formatDate( model.created_at )" nowrap></td>
                <td md-cell ng-bind="::app.formatDate( model.updated_at )" nowrap></td>
            </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">
                        <md-table-pagination md-limit="attr.paginationCount" md-limit-options="attr.paginationOptions" md-page="attr.currentPage" md-total="@{{attr.modelTotal}}" md-on-paginate="attr.loadModels" md-page-select></md-table-pagination>
                    </td>
                </tr>
            </tfoot>
        </table>
    </md-table-container>
</div>
@stop

<?php Assets::add(
        [
                'resources/assets/js/attribution/AttributionController.js',
                'resources/assets/js/attribution/AttributionApiService.js',
                'resources/assets/js/feed/FeedApiService.js', //REFACTOR WHEN FEEDS ARE REFACTORED
        ],
        'js','pageLevel')
?>
