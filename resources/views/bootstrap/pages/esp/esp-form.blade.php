
<input type="hidden" ng-model="esp.currentAccount.id" />
<input name="_token" type="hidden" ng-init="esp.currentAccount._token = '{{ csrf_token() }}' " ng-model="esp.currentAccount._token">
<div class="form-group" ng-class="{ 'has-error' : esp.formErrors.name }">
    <input type="text" placeholder="ESP Name" id="name" class="form-control" required="required" ng-disabled="{{ $formType =='edit' }} " ng-model="esp.currentAccount.name" />
    <div class="help-block" ng-show="esp.formErrors.name">
        <div ng-repeat="error in esp.formErrors.name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>

<div class="form-group" ng-class="{ 'has-error' : esp.formErrors.email_id_field }">
    <input type="text" placeholder="Email Id Field" id="email_id_field" class="form-control" required="required" ng-model="esp.currentAccount.email_id_field" />
    <div class="help-block" ng-show="esp.formErrors.email_id_field">
        <div ng-repeat="error in esp.formErrors.email_id_field">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>

<div class="form-group" ng-class="{ 'has-error' : esp.formErrors.email_address_field }">
    <input type="text" placeholder="Email Address Field" id="email_address_field" class="form-control"  ng-model="esp.currentAccount.email_address_field" />
    <div class="help-block" ng-show="esp.formErrors.email_address_field">
        <div ng-repeat="error in esp.formErrors.email_address_field">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>