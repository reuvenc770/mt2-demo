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
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"><a href="#model" aria-controls="models" role="tab" data-toggle="tab">Models</a></li>
        <li role="presentation" class="active"><a href="#reporttab" aria-controls="reporttab" role="tab" data-toggle="tab">Report</a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane" id="model">
            @include( 'bootstrap.pages.attribution.indexPartials.models-index' )
        </div>
        <div role="tabpanel" class="tab-pane active" id="reporttab">
            @include( 'bootstrap.pages.attribution.indexPartials.three-month-report' )
            <br/>
            <br/>
            <div class="alert alert-info" role="alert">
            This report may take a moment to load. Please be patient. </div>
        </div>
    </div>
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
