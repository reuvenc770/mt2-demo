@extends( 'bootstrap.layout.default' )

@section( 'container' , 'container-fluid' )

@section( 'title' , 'Esp Mappings' )

@section( 'angular-controller' , 'ng-controller="espController as esp"' )

@section( 'content' )
    <div class="alert alert-info" role="alert"> <strong>Heads up!</strong> Any information uploaded here will likely be replaced by a later API call,  please do not re-upload, previously collected campaigns.</div>
    <div class="panel panel-primary" ng-init="esp.loadMapping()">
        <div class="panel-heading">
            <div class="panel-title">File Drop Field Order</div>
        </div>

        <div class="panel-body">

            <div class="row draggable-membership-widget">
                <div class="col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">Available Fields</div>
                        <div class="pane-body">
                        <ul class="list-group" dnd-list="esp.fieldList" style="min-height: 300px;">
                            <li class="list-group-item"
                                ng-repeat="listItem in esp.fieldList"
                                dnd-draggable="listItem"
                                dnd-moved="esp.moveField( listItem , esp.fieldList , $index )"
                                dnd-effect-allowed="move">
                                <span ng-bind="listItem.label"></span> <i class="label label-danger" ng-show="esp.formErrors[ listItem.field ]">Must be selected</i> <span class="pull-right label label-danger" ng-show="listItem.required">required</span>
                            </li>
                        </ul>
                            </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">Selected Fields</div>
                        <div class="pane-body">



                        <ul class="list-group" dnd-list="esp.selectedFields" style="min-height: 300px;">
                            <li class="list-group-item"
                                ng-repeat="listItem in esp.selectedFields"
                                dnd-draggable="listItem"
                                dnd-moved="esp.moveField( listItem , esp.selectedFields , $index )"
                                dnd-effect-allowed="move">
                                <span class="label label-info" ng-bind="$index + 1"></span> <span ng-bind="listItem.label"></span><span class="pull-right label label-danger" ng-show="listItem.required">required</span>
                            </li>
                        </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="panel-footer">
            <div class="form-group">
                <input class="btn btn-primary btn-block" ng-click="esp.saveFieldOrder()" ng-disabled="feed.formSubmitted" type="submit" value="Save Field Order" />
            </div>
        </div>
    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/esp/EspController.js',
                'resources/assets/js/bootstrap/esp/EspService.js'], 'js','pageLevel') ?>
