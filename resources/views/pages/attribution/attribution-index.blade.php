@extends( 'layout.default' )

@section( 'title' , 'Attribution Model' )
@section( 'container' , 'container-fluid' )
@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
        @if (Sentinel::hasAccess('attributionModel.add'))
        <li><a ng-href="{{ route( 'attributionModel.add' ) }}" target="_self" >Add Model</a></li>
        @endif
@stop

@section( 'content' )

<div  ng-init="attr.initIndexPage()">
    <div class="alert alert-info" role="alert"> <strong>Heads up!</strong> Highlighted row is currently live. Attribution is automated to run once a day. To manually update record attribution for <em>live model</em> click 'Run Attribution' button <md-icon md-font-set="material-icons" class="mt2-icon-black icon-xs">monetization_on</md-icon> on the live model row. To manually update record attribution for <em>inactive model</em> select the inactive model and click 'Run Attribution' button <md-icon md-font-set="material-icons" class="mt2-icon-black icon-xs">monetization_on</md-icon> for the inactive model you want to run. After manually running live attribution, reports will update but you will need to go to the <a ng-href="{{ route( 'report.list' ) }}" target="_self">reports page</a> to view the reports. If attribution is running for a model you will not be able to edit that model until the run is complete.</div>
            @include( 'pages.attribution.indexPartials.models-index' )
</div>
@stop

<?php Assets::add(
        [
                'resources/assets/js/attribution/AttributionController.js',
                'resources/assets/js/attribution/AttributionApiService.js',
                'resources/assets/js/attribution/AttributionProjectionService.js',
                'resources/assets/js/attribution/AttributionModelTableDirective.js',
                'resources/assets/js/report/ThreeMonthReportService.js',
                'resources/assets/js/report/ReportApiService.js',
                'resources/assets/js/feed/FeedApiService.js', //REFACTOR WHEN FEEDS ARE REFACTORED
        ],
        'js','pageLevel')
?>
