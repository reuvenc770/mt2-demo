@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Add Attribution Model' )

@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )




@section( 'content' )
<div ng-init="attr.loadClients()">
    <div class="panel panel-primary" >
        <div class="panel-heading">
            <div class="panel-title">Add Attribution<input class="btn btn-sm btn-primary" ng-click="attr.saveModel( $event , attrModelForm )"  ng-disabled="attr.formSubmitted" type="submit" value="Add Attribution Model">
            </div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'bootstrap.pages.attribution.attribution-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn btn-lg btn-primary btn-block" ng-click="attr.saveModel( $event , attrModelForm )"  ng-disabled="attr.formSubmitted" type="submit" value="Add Attribution Model">
            </div>
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
