@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Edit Attribution Model' )

@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
        @if (Sentinel::hasAccess('api.attribution.model.update'))
            <li><a ng-click="attr.updateModel( $event , attrModelForm )" aria-label="Update Attribution Model">
                Update Model</a>
            </li>
        @endif

        @if (Sentinel::hasAccess('api.attribution.model.copyLevels'))
            <li><a ng-click="attr.copyModelPreview( $event )" aria-label="Import Levels">
                Import Levels</a>
            </li>
        @endif

        @if (Sentinel::hasAccess('api.attribution.model.syncLevels'))
            <li><a ng-click="attr.syncMt1Levels( $event )" ng-show="attr.current.live" aria-label="Sync MT1 Levels">
                Sync MT1 Levels</a>
            </li>
        @endif
@stop

@section( 'content' )
    <div ng-init="attr.prepopModel()">
        <div class="panel panel-primary" >
            <div class="panel-heading">
                <div class="panel-title">Update Attribution<input class="btn btn-sm btn-success pull-right" ng-click="attr.updateModel()"  ng-disabled="attr.formSubmitted" type="submit" value="Update Attribution Model">
                </div>
            </div>
            <div class="panel-body">
                <fieldset>
                    @include( 'bootstrap.pages.attribution.attribution-form' )
                </fieldset>
            </div>
            <div class="panel-footer">
                <div class="form-group">
                    <input class="btn btn-lg btn-primary btn-block" ng-click="attr.updateModel()"  ng-disabled="attr.formSubmitted" type="submit" value="Update Attribution Model">
                </div>
            </div>
        </div>
    </div>
    @include( 'bootstrap.pages.attribution.attribution-level-copy-sidenav' )
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