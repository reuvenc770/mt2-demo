<md-card>
    <md-toolbar>
        <div class="md-toolbar-tools">
            <span>Feed Group Details</span>
        </div>
    </md-toolbar>
    <md-card-content>
        <form name="feedGroupForm" layout="column" novalidate>
            <md-input-container>
                <label>Feed Group Name</label>
                <input type="text" name="groupName" id="groupName" ng-required="true" ng-model="clientGroup.current.groupName">

                <div ng-messages="feedGroupForm.groupName.$error">
                    <div ng-message="required">Feed group name is required.</div>
                </div>
            </md-input-container>

            <div>
                <membership-widget recordlist="clientGroup.clientList" chosenrecordlist="clientGroup.selectedClients" availablecardtitle="clientGroup.availableWidgetTitle" chosenrecordtitle="clientGroup.chosenWidgetTitle" idfield="clientGroup.clientIdField" namefield="clientGroup.clientNameField" updatecallback="clientGroup.clientMembershipCallback()" widgetname="clientGroup.widgetName"></membership-widget>

                <span class="mt2-error-message" ng-bind="clientGroup.formErrors.clients" ng-show="clientGroup.formErrors.clients"></span>
            </div>

            <div>
                 <md-switch ng-true-value="'Y'" ng-false-value="'N'" ng-model="clientGroup.current.excludeFromSuper" aria-label="Exclude From Super">Exclude From Super</md-switch>
            </div>
        </form>
    </md-card-content>
</md-card>