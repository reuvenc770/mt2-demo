<input name="_token" type="hidden" value="{{ csrf_token() }}">
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.name }">
    <input placeholder="Registrar Name" value="" class="form-control" ng-model="registrar.currentAccount.name" required="required" name="name" type="text">
    <div class="help-block" ng-show="registrar.formErrors.name">
        <div ng-repeat="error in registrar.formErrors.name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.dba_names }">
    <div class="input-group">
        <select class="form-control" name="dba_name" ng-model="registrar.dba_name">
            <option value="">Select DBA</option>
            @foreach ( $dbas as $dba )
                <option value="{{ $dba['dba_name'] }}">{{ $dba['dba_name'] }}</option>
            @endforeach
        </select>
    <span class="input-group-btn">
        <button class="btn btn-primary" ng-click="registrar.addDba()" type="button">Add DBA</button>
      </span>
    </div>
    <div class="help-block" ng-show="registrar.formErrors.dba_names">
        <div ng-repeat="error in registrar.formErrors.dba_names">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<ul class="list-group" ng-show="registrar.dba_names.length > 0">
    <li ng-repeat="(key, value) in registrar.dba_names track by $index" class="list-group-item list-group-item-success">@{{value}} - <a
                ng-click="registrar.removeDba(key)">Remove</a></li>
</ul>

<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.username }">
    <input placeholder="Username" value="" class="form-control" ng-model="registrar.currentAccount.username" required="required" name="username" type="text">
    <div class="help-block" ng-show="registrar.formErrors.username">
        <div ng-repeat="error in registrar.formErrors.username">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.password }">
    <input placeholder="Password" value="" class="form-control" ng-model="registrar.currentAccount.password" required="required" name="password" type="text">
    <div class="help-block" ng-show="registrar.formErrors.password">
        <div ng-repeat="error in registrar.formErrors.password">
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