        <div class="form-group" ng-class="{ 'has-error' : ma.formErrors.deploy_id }">
            <select ng-model="ma.currentAdjustment.deploy_id" placeholder="Select Deploy" required="required"
                    ng-change="ma.updateEspAccounts()" class="form-control" ng-disabled="ma.updatingAccounts">
                <option value="">Select Deploy</option>
                @foreach ( $deploys as $deploy )
                    <option value="{{ $deploy['external_deploy_id'] }}">{{ $deploy['campaign_name'] }}</option>
                @endforeach
            </select>
            <span class="help-block" ng-bind="ma.formErrors.deploy_id" ng-show="ma.formErrors.deploy_id"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : ma.formErrors.amount }">
            <input placeholder="Amount" value="" class="form-control" ng-model="ma.currentAdjustment.amount" required="required" name="amount" type="text">
            <span class="help-block" ng-bind="ma.formErrors.amount" ng-show="ma.formErrors.amount"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : ma.formErrors.date }">
            <input placeholder="City" value="" class="form-control" ng-model="ma.currentAdjustment.date" required="required" name="date" type="text">
            <span class="help-block" ng-bind="ma.formErrors.date" ng-show="ma.formErrors.date"></span>
        </div>