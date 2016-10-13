<div class="form-group" ng-class="{ 'has-error' : attr.formErrors.name }">
    <input placeholder="Name" value="" class="form-control" ng-model="attr.current.name" required="required" name="name"
           type="text">
    <div class="help-block" ng-show="attr.formErrors.name">
        <div ng-repeat="error in attr.formErrors.name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title">Attribution Levels <span class="pull-right"><b>Displaying @{{ attr.rowLimit }} of @{{ attr.feeds.length }} Feeds</b> </span></div>
    </div>
    <div class="panel-body">
        <ul class="list-group" ng-cloak>
            <li ng-repeat="feed in attr.feeds | limitObjects:attr.rowLimit track by $index"
                class="list-group-item clearfix" ng-class="{ 'list-group-item-success' : attr.clientLevels[ feed.id ] > ( $index + 1 ) , 'list-group-item-danger' : attr.clientLevels[ feed.id ] < ( $index + 1 )}">
                <div class="col-sm-1">
                    <input ng-model="feed.selected" type="checkbox">
                </div>
                <div class="col-sm-5">
                    <h4>@{{ feed.name }}</h4>
                </div>
                <div class="col-sm-3">
                    <div class="row">
                        <div class="col-sm-4">
                            <input class="form-control input-small" ng-model="feed.newLevel" type="text">
                        </div>
                        <div class="col-sm-8">
                            <button class="btn btn-sm btn-block btn-primary" ng-if="feed.newLevel != $index + 1"
                                    ng-click="attr.changeLevel( feed , $index )">Change Level
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 attribution-actions">
                    <span ng-click="attr.onLevelRise( feed , $index )" class="glyphicon glyphicon-arrow-up"></span>
                    <span ng-click="attr.onLevelDrop( feed , $index )" class="glyphicon glyphicon-arrow-down"></span>
                    <span ng-click="attr.moveToTop( feed , $index )" class="glyphicon glyphicon-open"></span>
                    <span ng-click="attr.moveToBottom( feed , $index )" class="glyphicon glyphicon-save"></span>
                    <span ng-click="attr.confirmDeletion( feed , $index )" class="glyphicon glyphicon-trash"></span>
                </div>
            </li>
        </ul>
    </div>
    <div class="panel-footer clearfix">
        <div class="col-sm-6">

            <input class="btn  btn-success btn-block" ng-click="attr.loadMore()" type="submit" value="Load More Rows">
        </div>
        <div class="col-sm-6">
            <input class="btn  btn-danger btn-block" ng-click="attr.loadLess()" type="submit" value="Load Less Rows">
        </div>
    </div>
</div>
