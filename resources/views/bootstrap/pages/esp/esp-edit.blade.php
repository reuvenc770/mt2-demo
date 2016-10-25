@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Edit ESP Account' )

@section( 'content' )
<div class="panel panel-primary" ng-controller="espController as esp" ng-init="esp.loadAccount()">
    <div class="panel-heading">
        <div class="panel-title">Edit ESP Account :: @{{esp.currentAccount.name}}</div>
    </div>
    <div class="panel-body">
        <fieldset>
            <input type="hidden" ng-model="esp.currentAccount.id" />
            <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.name }">
                <input type="text" placeholder="ESP Name" id="name" class="form-control" required="required" disabled  ng-model="esp.currentAccount.name" />
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
        </fieldset>
    </div>
    <div class="panel-footer">
        <div class="form-group">
            <button type="button" class="btn btn-primary btn-block" ng-disabled="esp.formSubmitted" ng-click="esp.editAccount()">Update ESP Account</button>
        </div>
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/esp/EspController.js',
                'resources/assets/js/bootstrap/esp/EspService.js'],'js','pageLevel') ?>
