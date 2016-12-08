<div class="form-group" ng-class="{ 'has-error' : attr.formErrors.name }">
    <input placeholder="Name" value="" class="form-control" ng-model="attr.current.name" required="required" name="name"
           type="text">
    <div class="help-block" ng-show="attr.formErrors.name">
        <div ng-repeat="error in attr.formErrors.name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">
        <!--//I wanted to move controls up here ,but its out of the scope of the loop, could push feeds on array and loop -->
        <div class="panel-title">Attribution Levels <span ng-if="attr.selectFeedCount > 1"><b> - Buttons Controlling Selected Group - </b> </span>
            <span class="pull-right"><b>Displaying @{{ attr.rowLimit }} of @{{ attr.feeds.length }} Feeds</b> </span></div>
    </div>
    <div class="panel-body">
        <ul class="list-group" ng-cloak>
            <li ng-repeat="feed in attr.feeds | limitObjects:attr.rowLimit track by $index"
                class="list-group-item clearfix" ng-class="{ 'list-group-item-success' : attr.clientLevels[ feed.id ] > ( $index + 1 ) , 'list-group-item-danger' : attr.clientLevels[ feed.id ] < ( $index + 1 )}">
                <div class="col-sm-4 col-md-6 no-padding">
                    <div class="checkbox no-margin">
                        <label>
                            <h4>
                                <input ng-change="attr.toggleGroupController(feed , $index)" ng-model="feed.selected" type="checkbox">
                                &nbsp;&nbsp; @{{ feed.name }}
                            </h4>
                        </label>
                    </div>
                </div>
                <div class="col-sm-4 col-md-3">
                    <div class="row">
                        <div class="col-sm-4">
                            <input class="form-control input-sm" ng-model="feed.newLevel" type="text">
                        </div>
                        <div class="col-sm-8">
                            <button class="btn btn-sm btn-block mt2-theme-btn-primary" ng-if="feed.newLevel != $index + 1"
                                    ng-click="attr.changeLevel( feed , $index )">Change Level
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-3 attribution-actions">
                    <span ng-click="attr.onLevelRise( feed , $index )" class="glyphicon glyphicon-arrow-up" aria-label="Up" title="Up"></span>
                    <span ng-click="attr.onLevelDrop( feed , $index )" class="glyphicon glyphicon-arrow-down" aria-label="Down" title="Down"></span>
                    <span ng-click="attr.moveToTop( feed , $index )" class="glyphicon glyphicon-open" aria-label="All the Way Up" title="All the Way Up"></span>
                    <span ng-click="attr.moveToBottom( feed , $index )" class="glyphicon glyphicon-save" aria-label="All the Way Down" title="All the Way Down"></span>
                    <span ng-click="attr.confirmDeletion( feed , $index )" class="glyphicon glyphicon-trash" aria-label="Delete" title="Delete"></span>
                </div>
            </li>
        </ul>
    </div>
    <div class="panel-footer clearfix">
        <div class="col-sm-6" ng-class="{ 'form-group' : app.isMobile() }">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="attr.loadMore()" type="submit" value="Load More Rows">
        </div>
        <div class="col-sm-6">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="attr.loadLess()" type="submit" value="Load Less Rows">
        </div>
    </div>
</div>
