
        <!-- Email field -->
        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.dba_name }">
            <input placeholder="DBA Name" value="" class="form-control" ng-model="dba.currentAccount.dba_name" required="required" name="name" type="text">
            <span class="help-block" ng-bind="dba.formErrors.dba_name" ng-show="dba.formErrors.dba_name"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.registrant_name }">
            <input placeholder="Registrant Name" value="" class="form-control" ng-model="dba.currentAccount.registrant_name" required="required" name="registrant_name" type="text">
            <span class="help-block" ng-bind="dba.formErrors.registrant_name" ng-show="dba.formErrors.registrant_name"></span>
        </div>
        <lite-membership-widget recordlist="dg.emailDomains" chosenrecordlist="dg.selectedDomains" availablerecordtitle="dg.availableWidgetTitle" chosenrecordtitle="dg.chosenWidgetTitle" idfield="eg.domainGroupIdField" namefield="eg.domainGroupNameField" updatecallback="" ></lite-membership-widget>

