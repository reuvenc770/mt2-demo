
        <!-- Email field -->
        <div class="form-group" ng-class="{ 'has-error' : dg.formErrors.name }">
            <input placeholder="ISP Group Name" value="" class="form-control" ng-model="dg.currentAccount.name" required="required" name="name" type="text">
            <span class="help-block" ng-bind="dba.formErrors.dba_name" ng-show="dba.formErrors.dba_name"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : dg.formErrors.country }">
            <select ng-model="dg.currentAccount.country" placeholder="" name="country"  class="form-control">
                <option  value="">Select ISP Group Country</option>
                <option ng-selected="dg.currentAccount.country == US" value="US">United States</option>
                <option ng-selected="dg.currentAccount.country == UK" value="UK">United Kingdom</option>
            </select>
            <span class="help-block" ng-bind="dg.formErrors.country" ng-show="dg.formErrors.country"></span>
        </div>

