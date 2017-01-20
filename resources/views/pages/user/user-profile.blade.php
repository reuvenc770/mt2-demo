@extends( 'layout.default' )
@section('title', 'My Profile')

@section('content')
<div class="panel mt2-theme-panel" ng-controller="userController as user" ng-init="user.loadProfile({{$id}})">
    <div class="panel-heading">
        <div class="panel-title">Edit User</div>
    </div>
    <div class="panel-body">
        <fieldset class="form-horizontal">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.email }">
                <label class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10">
                <input placeholder="Email" value="" class="form-control" ng-model="user.currentAccount.email" required="required" name="email" type="text">
                <div class="help-block" ng-show="user.formErrors.email">
                    <div ng-repeat="error in user.formErrors.email">
                        <span ng-bind="error"></span>
                    </div>
                </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.username }">
                <label class="col-sm-2 control-label">Username</label>
                <div class="col-sm-10">
                <input placeholder="Username" ng-model="user.currentAccount.username"  class="form-control" required="required" name="username" type="text">
                <div class="help-block" ng-show="user.formErrors.username">
                    <div ng-repeat="error in user.formErrors.username">
                        <span ng-bind="error"></span>
                    </div>
                </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.first_name }">
                <label class="col-sm-2 control-label">First Name</label>
                <div class="col-sm-10">
                <input placeholder="First Name" value="" class="form-control" required="required" name="first_name" ng-model="user.currentAccount.first_name" type="text">
                <div class="help-block" ng-show="user.formErrors.first_name">
                    <div ng-repeat="error in user.formErrors.first_name">
                        <span ng-bind="error"></span>
                    </div>
                </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.last_name }">
                <label class="col-sm-2 control-label">Last Name</label>
                <div class="col-sm-10">
                <input placeholder="Last Name" value="" class="form-control" required="required" name="last_name" ng-model="user.currentAccount.last_name" type="text">
                <div class="help-block" ng-show="user.formErrors.last_name">
                    <div ng-repeat="error in user.formErrors.last_name">
                        <span ng-bind="error"></span>
                    </div>
                </div>
                </div>
            </div>
            <br/>
            <div class="col-sm-10 col-sm-offset-2 no-padding">
            <h4>Optional: To update your password, fill out below.</h4>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.password }">
                <label class="col-sm-2 control-label">Password</label>
                <div class="col-sm-10">
                <input placeholder="Password" class="form-control" required="required" name="password" type="password" ng-model="user.currentAccount.password" value="">
                <div class="help-block" ng-show="user.formErrors.password">
                    <div ng-repeat="error in user.formErrors.password">
                        <span ng-bind="error"></span>
                    </div>
                </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.newpass }">
                <label class="col-sm-2 control-label">New Password</label>
                <div class="col-sm-10">
                <input placeholder="New Password" class="form-control" required="required" name="newpass" type="password" ng-model="user.currentAccount.newpass" value="">
                <div class="help-block" ng-show="user.formErrors.newpass">
                    <div ng-repeat="error in user.formErrors.newpass">
                        <span ng-bind="error"></span>
                    </div>
                </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.newpass_confirmation }">
                <label class="col-sm-2 control-label">Confirm Password</label>
                <div class="col-sm-10">
                <input placeholder="Retype Password" class="form-control" required="required" name="newpass_confirmation" ng-model="user.currentAccount.newpass_confirmation" type="password" value="">
                <div class="help-block" ng-show="user.formErrors.newpass_confirmation">
                    <div ng-repeat="error in user.formErrors.newpass_confirmation">
                        <span ng-bind="error"></span>
                    </div>
                </div>
                </div>
            </div>
        </fieldset>
    </div>
    <div class="panel-footer">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="user.updateProfile()" ng-disabled="user.editForm" type="submit" value="Update Profile">
    </div>
</div>
@endsection


<?php Assets::add(
        ['resources/assets/js/user/UserController.js',
                'resources/assets/js/user/UserApiService.js'],'js','pageLevel') ?>