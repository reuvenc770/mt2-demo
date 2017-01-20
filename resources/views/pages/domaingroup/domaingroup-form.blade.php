    <div class="form-horizontal">
        <!-- Email field -->
        <div class="form-group" ng-class="{ 'has-error' : dg.formErrors.name }">
            <label class="col-sm-2 control-label">ISP Group Name</label>
            <div class="col-sm-10">
            <input placeholder="ISP Group Name" value="" class="form-control" ng-model="dg.currentAccount.name" required="required" name="name" type="text">
            <div class="help-block"  ng-show="dg.formErrors.name">
                <div ng-repeat="error in dg.formErrors.name">
                    <span ng-bind="error"></span>
                </div>
            </div>
            </div>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : dg.formErrors.country }">
            <label class="col-sm-2 control-label">ISP Group Country</label>
            <div class="col-sm-10">
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
        </div>
    </div>