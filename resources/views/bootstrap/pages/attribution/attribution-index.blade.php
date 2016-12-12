@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Attribution Model' )
@section( 'container' , 'container-fluid' )
@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
        @if (Sentinel::hasAccess('attributionModel.add'))
        <li><a ng-href="{{ route( 'attributionModel.add' ) }}" target="_self" >Add Model</a></li>
        @endif
        @if (Sentinel::hasAccess('api.attribution.run'))
                <li><a ng-click="attr.runAttribution( false )" >Run Live Attribution</a></li>
        @endif
@stop

@section( 'content' )

<div  ng-init="attr.initIndexPage()">
    <div class="alert alert-info" role="alert"> <strong>Heads up!</strong> Highlighted row is currently live. Attribution is automated to run once a day. To manually update record attribution for <em>live model</em> click 'Run Live Attribution'. To manually update record attribution for <em>inactive model</em> select the inactive model and click 'Run Attribution'. After manually running live attribution, reports will update but you will need to go to the <a ng-href="{{ route( 'report.list' ) }}" target="_self">reports page</a> to view the reports.</div>
            @include( 'bootstrap.pages.attribution.indexPartials.models-index' )
</div>
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
