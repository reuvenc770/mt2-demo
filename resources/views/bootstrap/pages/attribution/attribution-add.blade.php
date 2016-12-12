@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Add Attribution Model' )

@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
    <li ng-hide="attr.lastFeedOrder.length <= 0"><a ng-click="attr.undoLevelChange()" area-label="Undo Last Level Change">Undo Last Level Change</a></li>
@stop


@section( 'content' )
<div ng-init="attr.loadClients()">
    <div class="panel mt2-theme-panel" >
        <div class="panel-heading">
            <div class="panel-title">Add Attribution<input class="bold-text btn btn-sm mt2-theme-btn-secondary pull-right" ng-click="attr.saveModel()"  ng-disabled="attr.formSubmitted" type="submit" value="Add Attribution Model">
            </div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'bootstrap.pages.attribution.attribution-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="attr.saveModel()"  ng-disabled="attr.formSubmitted" type="submit" value="Add Attribution Model">
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
