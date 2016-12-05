@extends( 'bootstrap.layout.default' )

@section( 'container' , 'container-fluid' )

@section( 'title' , 'Configure ESP Fields Mappings' )

@section( 'angular-controller' , 'ng-controller="espController as esp"' )

@section( 'content' )
    <div class="alert alert-info" role="alert"> <strong>Heads up!</strong> Any information uploaded here will likely be replaced by a later API call,  please do not re-upload, previously collected campaigns.</div>
    <div class="panel mt2-theme-panel" ng-init="esp.loadMapping()">
        <div class="panel-heading">
            <div class="panel-title">File Drop Field Order</div>
        </div>
        <div class="panel-body">
        <div ng-repeat="mapping in esp.fieldList" >
            <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon3">
                    @{{ mapping.label }}
                    <span ng-if="mapping.required">
                        *
                    </span>
                </span>
                <input type="text" ng-model="esp.colList[mapping.field]"  class="form-control input-sm" id="basic-url" aria-describedby="basic-addon3">
            </div>
        </div></div>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="esp.saveFieldOrder()" type="submit" value="Save Field Order" />
            </div>
        </div>
    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/esp/EspController.js',
                'resources/assets/js/bootstrap/esp/EspService.js'], 'js','pageLevel') ?>
