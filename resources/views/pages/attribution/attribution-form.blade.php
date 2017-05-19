<div class="form-horizontal">
    <div class="form-group" ng-class="{ 'has-error' : attr.formErrors.name }">
        <label class="col-sm-1 control-label">Name</label>
        <div class="col-sm-11">
            <input placeholder="Name" value="" class="form-control" ng-model="attr.current.name" required="required" name="name"
                   type="text">
            <div class="help-block" ng-show="attr.formErrors.name">
                <div ng-repeat="error in attr.formErrors.name">
                    <span ng-bind="error"></span>
                </div>
            </div>
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
        <div class="form-group">
            <p>Manually enter levels and feeds for reordering. Must start from level 1 and work sequentially. Any additional feeds in the model will be appended. Line Format: [feedLevel,feedID]</p>
            <label>Level Quick Sort</label>

            <div class="help-block"  ng-show="attr.formErrors.newFeedOrder">
                <div ng-repeat="error in attr.formErrors.newFeedOrder">
                    <span ng-bind="error"></span>
                </div>
            </div>

            <textarea class="form-control" rows="5" ng-model="attr.newFeedOrder"></textarea>
            <button class="btn mt2-theme-btn-primary btn-block" ng-click="attr.manualFeedOrder()">Reorder</button>
        </div>

        <ul class="list-group" ng-cloak>
            <li ng-repeat="feed in attr.feeds track by $index"
                class="list-group-item clearfix cmp-list-item-condensed" ng-class="{ 'list-group-item-success' : attr.clientLevels[ feed.id ] > ( $index + 1 ) , 'list-group-item-danger' : attr.clientLevels[ feed.id ] < ( $index + 1 )}">
                <div class="col-sm-3 col-md-5 no-padding">
                    <div class="checkbox no-margin">
                        <label>
                            <h5>
                                <input ng-change="attr.toggleGroupController(feed , $index)" ng-model="feed.selected" type="checkbox">
                                &nbsp;&nbsp; @{{ feed.name }}
                            </h5>
                        </label>
                    </div>
                </div>
                <div class="col-sm-4" style="margin-top: 5px;">
                    <div class="row">
                        <div class="col-sm-5">
                            <div ng-class="{ 'input-group' : $first }">
                                <input class="form-control cmp-input-xs" ng-model="feed.newLevel" type="text">
                                <div class="input-group-addon cmp-tooltip-marker" ng-if="$first">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs no-padding" aria-label="Change level" data-toggle="popover" data-placement="right" data-content="To move the feed to a specific level, type in the new position and click 'Change Level' button.">help</md-icon>
                                </div>

                            </div>
                        </div>
                        <div class="col-sm-7">
                            <button class="btn btn-sm btn-block mt2-theme-btn-primary" ng-if="feed.newLevel != $index + 1"
                                    ng-click="attr.changeLevel( feed , $index )">Change Level
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-3 attribution-actions no-padding" style="margin-top: 5px;">
                    <span ng-click="attr.onLevelRise( feed , $index )" class="glyphicon glyphicon-arrow-up" aria-label="Up" title="Up"></span>
                    <span ng-click="attr.onLevelDrop( feed , $index )" class="glyphicon glyphicon-arrow-down" aria-label="Down" title="Down"></span>
                    <span ng-click="attr.moveToTop( feed , $index )" class="glyphicon glyphicon-open" aria-label="All the Way Up" title="All the Way Up"></span>
                    <span ng-click="attr.moveToBottom( feed , $index )" class="glyphicon glyphicon-save" aria-label="All the Way Down" title="All the Way Down"></span>
                    <span ng-click="attr.confirmDeletion( feed , $index )" class="glyphicon glyphicon-trash" aria-label="Delete" title="Delete"></span>
                </div>
            </li>
        </ul>
    </div>
</div>
