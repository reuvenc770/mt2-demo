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
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.password }">
    <input placeholder="Password" value="" class="form-control" ng-model="registrar.currentAccount.password" required="required" name="password" type="text">
    <div class="help-block" ng-show="registrar.formErrors.password">
        <div ng-repeat="error in registrar.formErrors.password">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>

<div class="panel panel-info">
    <div class="panel-heading">
        <div class="panel-title">DBAs</div>
    </div>
    <div class="panel-body">
        <fieldset>
            <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.dba_name }">
                <input placeholder="DBA Name" value="" class="form-control" ng-model="registrar.currentDba.dba_name" required="required" name="dba_name" type="text">
                <div class="help-block" ng-show="registrar.formErrors.dba_name">
                    <div ng-repeat="error in registrar.formErrors.dba_name">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.dba_contact_name }">
                <input placeholder="Contact Name" value="" class="form-control" ng-model="registrar.currentDba.dba_contact_name" required="required" name="dba_contact_name" type="text">
                <div class="help-block" ng-show="registrar.formErrors.dba_contact_name">
                    <div ng-repeat="error in registrar.formErrors.dba_contact_name">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.dba_contact_email }">
                <input placeholder="Contact Email" value="" class="form-control" ng-model="registrar.currentDba.dba_contact_email" required="required" name="dba_contact_email" type="text">
                <div class="help-block" ng-show="registrar.formErrors.dba_contact_email">
                    <div ng-repeat="error in registrar.formErrors.dba_contact_email">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
        </fieldset>
        <button class="btn mt2-theme-btn-primary btn-block" ng-click="registrar.addDba()">
            <span ng-show="!registrar.editingDba">Add </span>
            <span ng-show="registrar.editingDba">Update </span> DBA
        </button>
        <div class="has-error">
            <div class="help-block" ng-show="registrar.formErrors.dba_names">
                <div ng-repeat="error in registrar.formErrors.dba_names">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer" ng-show="registrar.currentAccount.dba_names.length > 0">
        <div class="thumbnail" ng-repeat="(key , value) in registrar.currentAccount.dba_names track by $index">
            <div class="caption clearfix">
                @{{ value.dba_name }} - Contact: @{{ value.dba_contact_name }} (@{{ value.dba_contact_email }})
                <div class="pull-right">
                    <a href="#" class="btn mt2-theme-btn-primary btn-xs" ng-click="registrar.editDba(key)" role="button">Edit</a>
                    <a href="#" class="btn mt2-theme-btn-secondary btn-xs" ng-click="registrar.removeDba(key)"
                       role="button">Delete</a>
                </div>
            </div>
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
<div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.notes }">
        <textarea rows="4" placeholder="Notes" value="" class="form-control" ng-model="registrar.currentAccount.notes"
                  name="notes"></textarea>
    <div class="help-block" ng-show="registrar.formErrors.notes">
        <div ng-repeat="error in registrar.formErrors.notes">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>