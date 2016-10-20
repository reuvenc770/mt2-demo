<input name="_token" type="hidden" value="{{ csrf_token() }}">
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.name }">
    <input placeholder="Registrar Name" value="" class="form-control" ng-model="registrar.currentAccount.name" required="required" name="name" type="text">
    <div class="help-block" ng-show="registrar.formErrors.name">
        <div ng-repeat="error in registrar.formErrors.name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.username }">
    <input placeholder="Username" value="" class="form-control" ng-model="registrar.currentAccount.username" required="required" name="username" type="text">
    <div class="help-block" ng-show="registrar.formErrors.username">
        <div ng-repeat="error in registrar.formErrors.username">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.contact_name }">
    <input placeholder="Contact Name" value="" class="form-control" ng-model="registrar.currentAccount.contact_name" required="required" name="contact_name" type="text">
    <div class="help-block" ng-show="registrar.formErrors.contact_name">
        <div ng-repeat="error in registrar.formErrors.contact_name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.contact_email }">
    <input placeholder="Contact Email" value="" class="form-control" ng-model="registrar.currentAccount.contact_email" required="required" name="contact_email" type="text">
    <div class="help-block" ng-show="registrar.formErrors.contact_email">
        <div ng-repeat="error in registrar.formErrors.contact_email">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.address }">
    <input placeholder="Address" value="" class="form-control" ng-model="registrar.currentAccount.address"
           required="required" name="po_box_address" type="text">
    <div class="help-block" ng-show="registrar.formErrors.address">
        <div ng-repeat="error in registrar.formErrors.address">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.address_2 }">
    <input placeholder="Address Line 2" value="" class="form-control" ng-model="registrar.currentAccount.address_2"
           required="required" name="po_box_address_2" type="text">
    <div class="help-block" ng-show="registrar.formErrors.address_2">
        <div ng-repeat="error in registrar.formErrors.address_2">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.city }">
            <input placeholder="City" value="" class="form-control" ng-model="registrar.currentAccount.city"
                   required="required" name="po_box_city" type="text">
            <div class="help-block" ng-show="registrar.formErrors.city">
                <div ng-repeat="error in registrar.formErrors.city">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.state }">
            <select ng-model="registrar.currentAccount.state" name="po_box_state" class="form-control">
                <option value="">Pick A State</option>
                @foreach ( $states as $state )
                    <option value="{{ $state->iso_3166_2 }}">{{ $state->name }}</option>
                @endforeach
            </select>
            <div class="help-block" ng-show="registrar.formErrors.state">
                <div ng-repeat="error in registrar.formErrors.state">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.zip }">
            <input placeholder="Zip Code" value="" class="form-control" ng-model="registrar.currentAccount.zip"
                   required="required" name="po_box_zip" type="text">
            <div class="help-block" ng-show="registrar.formErrors.zip">
                <div ng-repeat="error in registrar.formErrors.zip">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.contact_email }">
    <input placeholder="Contact Email" type="email" class="form-control" ng-model="registrar.currentAccount.contact_email"
           required="required" name="contact_email" type="text">
    <div class="help-block" ng-show="registrar.formErrors.contact_email">
        <div ng-repeat="error in registrar.formErrors.contact_email">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.phone_number }">
    <input placeholder="Phone Number" type="text" class="form-control" ng-model="registrar.currentAccount.phone_number"
           required="required" name="phone_number" type="text">
    <div class="help-block" ng-show="registrar.formErrors.phone_number">
        <div ng-repeat="error in registrar.formErrors.phone_number">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.last_cc }">
    <input placeholder="Last 4 CC Digits" type="text" class="form-control" ng-model="registrar.currentAccount.last_cc"
           required="required" name="last_cc" type="text">
    <div class="help-block" ng-show="registrar.formErrors.last_cc">
        <div ng-repeat="error in registrar.formErrors.last_cc">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.contact_credit_card }">
    <input placeholder="Contact for Credit Card" type="text" class="form-control" ng-model="registrar.currentAccount.contact_credit_card"
           required="required" name="contact_credit_card" type="text">
    <div class="help-block" ng-show="registrar.formErrors.contact_credit_card">
        <div ng-repeat="error in registrar.formErrors.contact_credit_card">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>