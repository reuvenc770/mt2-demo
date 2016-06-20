<div class="panel panel-primary" ng-init="clientGroup.prepopPage()">
    <div class="panel-heading">
        <h3 class="panel-title">Client Group Details</h3>
    </div>

    <div class="panel-body">
        <div class="form-group" ng-class="{ 'has-error' : clientGroup.formErrors.groupName }">
            <input type="text" class="form-control" id="groupName" value="" placeholder="Client Group Name" ng-model="clientGroup.current.groupName" required="required" />

            <span class="help-block" ng-bind="clientGroup.formErrors.groupName" ng-show="clientGroup.formErrors.groupName"></span>
        </div>
        <div ng-class="{ 'has-error' : clientGroup.formErrors.clients }">
        <membership-widget recordlist="clientGroup.clientList" chosenrecordlist="clientGroup.selectedClients" availablecardtitle="clientGroup.availableWidgetTitle" chosenrecordtitle="clientGroup.chosenWidgetTitle" idfield="clientGroup.clientIdField" namefield="clientGroup.clientNameField" updatecallback="clientGroup.clientMembershipCallback()" widgetname="clientGroup.widgetName"></membership-widget>
            <span class="help-block" ng-bind="clientGroup.formErrors.clients" ng-show="role.formErrors.clients"></span>
        </div>
        <div class="form-group">
            <md-switch ng-true-value="'Y'" ng-false-value="'N'" ng-model="clientGroup.current.excludeFromSuper" aria-label="Exclude From Super">Exclude From Super</md-switch>
        </div>
    </div>
</div>

