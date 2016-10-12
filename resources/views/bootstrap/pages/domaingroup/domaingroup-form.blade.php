
        <!-- Email field -->
        <div class="form-group" ng-class="{ 'has-error' : dg.formErrors.name }">
            <input placeholder="ISP Group Name" value="" class="form-control" ng-model="dg.currentAccount.name" required="required" name="name" type="text">
            <div class="help-block"  ng-show="dg.formErrors.name">
                <div ng-repeat="error in dg.formErrors.name">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : dg.formErrors.country }">
            <select ng-model="dg.currentAccount.country" placeholder="" name="country"  class="form-control">
                <option  value="">Select ISP Group Country</option>
                <option ng-selected="dg.currentAccount.country == US" value="US">United States</option>
                <option ng-selected="dg.currentAccount.country == UK" value="UK">United Kingdom</option>
            </select>
            <div class="help-block"  ng-show="dg.formErrors.country">
                <div ng-repeat="error in dg.formErrors.country">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>

