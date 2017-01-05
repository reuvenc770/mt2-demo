@extends( 'layout.default' )

@section( 'container' , 'container-fluid' )

@section( 'title' , 'Feeds' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'content' )
<div class="panel mt2-theme-panel" ng-init="feed.setId( {{$id}} );feed.setFields( {{$fields}} );">
    <div class="panel-heading">
        <div class="panel-title">File Drop Field Order</div>
    </div>

    <div class="panel-body">

        <p>Drag and drop the field names in the order that they appear in the CSV file. If a field is not listed as available, you can create a custom one.</p>

        <div class="row draggable-membership-widget">
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">Available Fields</div>

                    <ul class="list-group" dnd-list="feed.fieldList" style="min-height: 300px;">
                        <li class="list-group-item"
                            ng-repeat="listItem in feed.fieldList"
                            dnd-draggable="listItem"
                            dnd-moved="feed.moveField( listItem , feed.fieldList , $index )"
                            dnd-effect-allowed="move">
                                <span ng-bind="listItem.label"></span> <i class="label label-danger" ng-show="feed.formErrors[ listItem.field ]">Must be selected</i> <span class="pull-right label label-warning" ng-show="listItem.isCustom">custom</span> <span class="pull-right label label-danger" ng-show="listItem.required">required</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">Selected Fields</div>
                    <div class="pane-body">
                        <div class="input-group">
                            <span class="input-group-addon">Custom</span>

                            <input class="form-control" ng-model="feed.customField" placeholder="Type Custom Field Name" />

                            <span class="input-group-btn">
                                <button class="btn mt2-theme-btn-primary" type="button" ng-click="feed.addCustomField()">Add</button>
                            </span>
                        </div>
                    </div>

                    <ul class="list-group" dnd-list="feed.selectedFields" style="min-height: 300px;">
                        <li class="list-group-item"
                            ng-repeat="listItem in feed.selectedFields"
                            dnd-draggable="listItem"
                            dnd-moved="feed.moveField( listItem , feed.selectedFields , $index )"
                            dnd-effect-allowed="move">
                            <span class="label label-info" ng-bind="$index + 1"></span> <span ng-bind="listItem.label"></span> <span class="pull-right label label-warning" ng-show="listItem.isCustom">custom</span> <span class="pull-right label label-danger" ng-show="listItem.required">required</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <div class="panel-footer">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="feed.saveFieldOrder()" ng-disabled="feed.formSubmitted" type="submit" value="Save Field Order" />
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/feed/FeedController.js',
        'resources/assets/js/bootstrap/feed/FeedApiService.js',
        'resources/assets/js/bootstrap/feed/FeedUrlModalDirective.js'],'js','pageLevel') ?>
