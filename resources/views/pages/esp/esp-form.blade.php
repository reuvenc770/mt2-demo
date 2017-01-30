<div class="form-horizontal">
    <input type="hidden" ng-model="esp.currentAccount.id"/>
    <input name="_token" type="hidden" ng-init="esp.currentAccount._token = '{{ csrf_token() }}' "
           ng-model="esp.currentAccount._token">
    <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.name }">
        <label class="col-sm-2 control-label">ESP Name</label>
        <div class="col-sm-10">
        <input type="text" placeholder="ESP Name" id="name" class="form-control" required="required"
               ng-disabled="esp.currentAccount.hasAccounts && esp.currentAccount.id != '' " ng-model="esp.currentAccount.name"/>
        <div class="help-block" ng-show="esp.formErrors.name">
            <div ng-repeat="error in esp.formErrors.name">
                <span ng-bind="error"></span>
            </div>
        </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.nickname }">
        <label class="col-sm-2 control-label">ESP Nickname</label>
        <div class="col-sm-10">
        <input type="text" placeholder="ESP Nickname" id="nickname" class="form-control" required="required"
               ng-model="esp.currentAccount.nickname"/>
        <div class="help-block" ng-show="esp.formErrors.nickname">
            <div ng-repeat="error in esp.formErrors.nickname">
                <span ng-bind="error"></span>
            </div>
        </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.open_email_id_field }">
        <label class="col-sm-2 control-label">Open Pixel Email ID Field</label>
        <div class="col-sm-10">
            <div class="input-group">
                <input type="text" placeholder="Open Pixel Email ID Field" id="open_email_id_field" class="form-control" required="required"
                   ng-model="esp.currentAccount.open_email_id_field"/>
                <span class="input-group-addon" id="basic-addon2">
                    <label class="no-margin" style="font-weight: normal;">
                        <input ng-model="esp.currentAccount.open_email_id_field_toggle" type="checkbox">
                        Not Used
                    </label>
                </span>
            </div>
            <div class="help-block" ng-show="esp.formErrors.open_email_id_field">
                <div ng-repeat="error in esp.formErrors.open_email_id_field">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.open_email_address_field }">
        <label class="col-sm-2 control-label">Open Pixel Email Address Field</label>
        <div class="col-sm-10">
            <div class="input-group">
                <input type="text" placeholder="Email Address Field" id="open_email_address_field" class="form-control"
                    ng-model="esp.currentAccount.open_email_address_field"/>
                <span class="input-group-addon" id="basic-addon2">
                    <label class="no-margin" style="font-weight: normal;">
                        <input ng-model="esp.currentAccount.open_email_address_field_toggle" type="checkbox">
                        Not Used
                    </label>
                </span>
            </div>
            <div class="help-block" ng-show="esp.formErrors.open_email_address_field">
                <div ng-repeat="error in esp.formErrors.open_email_address_field">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.email_id_field }">
        <label class="col-sm-2 control-label">Link Email ID Field</label>
        <div class="col-sm-10">
            <div class="input-group">
                <input type="text" placeholder="Email ID Field" id="email_id_field" class="form-control" required="required"
                   ng-model="esp.currentAccount.email_id_field"/>
                <span class="input-group-addon" id="basic-addon2">
                    <label class="no-margin" style="font-weight: normal;">
                        <input ng-model="esp.currentAccount.email_id_field_toggle" type="checkbox">
                        Not Used
                    </label>
                </span>
            </div>
            <div class="help-block" ng-show="esp.formErrors.email_id_field">
                <div ng-repeat="error in esp.formErrors.email_id_field">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.email_address_field }">
        <label class="col-sm-2 control-label">Link Email Address Field</label>
        <div class="col-sm-10">
            <div class="input-group">
                <input type="text" placeholder="Email Address Field" id="email_address_field" class="form-control"
                    ng-model="esp.currentAccount.email_address_field"/>
                <span class="input-group-addon" id="basic-addon2">
                    <label class="no-margin" style="font-weight: normal;">
                        <input ng-model="esp.currentAccount.email_address_field_toggle" type="checkbox">
                        Not Used
                    </label>
                </span>
            </div>
            <div class="help-block" ng-show="esp.formErrors.email_address_field">
                <div ng-repeat="error in esp.formErrors.email_address_field">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>
</div>