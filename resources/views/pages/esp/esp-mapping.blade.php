@extends( 'layout.default' )

@section( 'title' , 'Configure ESP Fields Mappings' )

@section( 'angular-controller' , 'ng-controller="espController as esp"' )

@section( 'content' )
    <div class="alert alert-info" role="alert"> <strong>Heads up!</strong> Leave fields blank if you do not have the information.  Campaign Name is the only required field</div>
    <div class="panel mt2-theme-panel" ng-init="esp.loadMapping()">
        <div class="panel-heading">
            <div class="panel-title">Field Order Customization</div>
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
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="esp.saveFieldOrder()" type="submit" value="Save Field Order" />
        </div>
    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/esp/EspController.js',
                'resources/assets/js/esp/EspService.js'], 'js','pageLevel') ?>