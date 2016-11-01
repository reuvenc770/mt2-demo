<div class="panel-heading">
    <div class="panel-title">Feed Group Details</div>
</div>

<div class="panel-body">
    <div class="form-group" ng-class="{ 'has-error' : feedGroup.formErrors.name }">
        <input placeholder="Feed Group Name" value="" class="form-control" name="name" ng-model="feedGroup.current.name" type="text">
        <div class="help-block" ng-show="feedGroup.formErrors.name">
            <div ng-repeat="error in feedGroup.formErrors.name">
                <div ng-bind="error"></div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <lite-membership-widget height="200" recordlist="feedGroup.feedList" namefield="feedGroup.feedListNameField" chosenrecordlist="feedGroup.current.feeds" availablerecordtitle="'Available Feeds'" chosenrecordtitle="'Selected Feeds'"></lite-membership-widget>
        <div class="has-error">
            <div class="help-block" ng-show="feedGroup.formErrors.feeds">
                <div ng-repeat="error in feedGroup.formErrors.feeds">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </div>
    </div>
</div>
