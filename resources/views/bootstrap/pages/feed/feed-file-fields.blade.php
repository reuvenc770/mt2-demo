@extends( 'bootstrap.layout.default' )

@section( 'container' , 'container-fluid' )

@section( 'title' , 'Feeds' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'content' )
<div class="panel panel-primary" ng-init="feed.setId( {{$id}} )">
    <div class="panel-heading">
        <div class="panel-title">File Drop Field Order</div>
    </div>

    <div class="panel-body">

        <div class="row draggable-membership-widget">
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">Available Fields</div>
                    <div class="pane-body bg-danger">
                        <p ng-repeat="error in feed.formErrors.email_index" ng-bind="error"></p>
                        <p ng-repeat="error in feed.formErrors.source_url_index" ng-bind="error"></p>
                        <p ng-repeat="error in feed.formErrors.capture_date_index" ng-bind="error"></p>
                        <p ng-repeat="error in feed.formErrors.ip_index" ng-bind="error"></p>
                    </div>

                    <ul class="list-group" dnd-list="feed.fieldList" style="min-height: 300px;">
                        <li class="list-group-item"
                            ng-repeat="listItem in feed.fieldList"
                            dnd-draggable="listItem"
                            dnd-moved="feed.moveField( listItem , feed.fieldList , $index )"
                            dnd-effect-allowed="move">
                                <span ng-bind="listItem.label"></span> <span class="pull-right text-warning" ng-show="listItem.isCustom">custom</span> <span class="pull-right text-danger" ng-show="listItem.required">required</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">Available Fields</div>
                    <div class="pane-body">
                        <div class="input-group">
                            <span class="input-group-addon">Custom</span>

                            <input class="form-control" ng-model="feed.customField" placeholder="Type Custom Field Name" />

                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" ng-click="feed.addCustomField()">Add</button>
                            </span>
                        </div>
                    </div>

                    <ul class="list-group" dnd-list="feed.selectedFields" style="min-height: 300px;">
                        <li class="list-group-item"
                            ng-repeat="listItem in feed.selectedFields"
                            dnd-draggable="listItem"
                            dnd-moved="feed.moveField( listItem , feed.selectedFields , $index )"
                            dnd-effect-allowed="move">
                            <span ng-bind="listItem.label"></span> <span class="pull-right text-warning" ng-show="listItem.isCustom">custom</span> <span class="pull-right text-danger" ng-show="listItem.required">required</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <div class="panel-footer">
        <div class="form-group">
            <input class="btn btn-primary btn-block" ng-click="feed.saveFieldOrder()" ng-disabled="feed.formSubmitted" type="submit" value="Save Field Order" />
        </div>
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/feed/FeedController.js',
        'resources/assets/js/bootstrap/feed/FeedApiService.js',
        'resources/assets/js/bootstrap/feed/FeedUrlModalDirective.js'],'js','pageLevel') ?>
