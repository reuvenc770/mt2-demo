
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">Feed Group Details</div>
    </div>
    <div class="panel-body">
        <div class="form-group" ng-class="{ 'has-error' : clientGroup.formErrors.groupName }">
            <input placeholder="Feed Group Name" value="" class="form-control" name="groupName" ng-model="clientGroup.current.groupName" type="text">
            <div class="help-block" ng-show="clientGroup.formErrors.groupName">
                <div ng-repeat="error in clientGroup.formErrors.groupName">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <lite-membership-widget height="200" recordlist="clientGroup.clientList" chosenrecordlist="clientGroup.selectedClients" availablerecordtitle="clientGroup.availableWidgetTitle" chosenrecordtitle="clientGroup.chosenWidgetTitle" idfield="clientGroup.clientIdField" namefield="clientGroup.clientNameField" updatecallback="clientGroup.clientMembershipCallback()" widgetname="clientGroup.widgetName"></lite-membership-widget>
            <div class="has-error">
                <div class="help-block" ng-show="clientGroup.formErrors.clients">
                    <div ng-repeat="error in clientGroup.formErrors.clients">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : clientGroup.formErrors.excludeFromSuper }">
            <label>Exclude from Super</label>
            <div class="radio">
            <label class="radio-inline">
                <input type="radio" data-ng-value="true" ng-model="clientGroup.current.excludeFromSuper">
                Yes
            </label>
            <label class="radio-inline">
                <input type="radio" data-ng-value="false" ng-model="clientGroup.current.excludeFromSuper">
                No
            </label>
            </div>
            <div class="help-block" ng-show="clientGroup.formErrors.excludeFromSuper">
                <div ng-repeat="error in clientGroup.formErrors.excludeFromSuper">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </div>
    </div>
</div>