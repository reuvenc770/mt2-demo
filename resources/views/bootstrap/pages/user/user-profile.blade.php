@extends( 'bootstrap.layout.default' )
@section('title', 'My Profile')

@section('content')
<div class="panel panel-primary" ng-controller="userController as user" ng-init="user.loadProfile({{$id}})">
    <div class="panel-heading">
        <div class="panel-title">Edit User</div>
    </div>
    <div class="panel-body">
        <fieldset>
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.email }">
                <input placeholder="Email" value="" class="form-control" ng-model="user.currentAccount.email" required="required" name="email" type="text">
                <div class="help-block" ng-show="user.formErrors.email">
                    <div ng-repeat="error in user.formErrors.email">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.username }">
                <input placeholder="Username" ng-model="user.currentAccount.username"  class="form-control" required="required" name="username" type="text">
                <div class="help-block" ng-show="user.formErrors.username">
                    <div ng-repeat="error in user.formErrors.username">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.first_name }">
                <input placeholder="First Name" value="" class="form-control" required="required" name="first_name" ng-model="user.currentAccount.first_name" type="text">
                <div class="help-block" ng-show="user.formErrors.first_name">
                    <div ng-repeat="error in user.formErrors.first_name">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.last_name }">
                <input placeholder="Last Name" value="" class="form-control" required="required" name="last_name" ng-model="user.currentAccount.last_name" type="text">
                <div class="help-block" ng-show="user.formErrors.last_name">
                    <div ng-repeat="error in user.formErrors.last_name">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <h4>If you would like to update your password please fill out below (Optional)</h4>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.password }">
                <input placeholder="Password" class="form-control" required="required" name="password" type="password" ng-model="user.currentAccount.password" value="">
                <div class="help-block" ng-show="user.formErrors.password">
                    <div ng-repeat="error in user.formErrors.password">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.newpass }">
                <input placeholder="New Password" class="form-control" required="required" name="newpass" type="password" ng-model="user.currentAccount.newpass" value="">
                <div class="help-block" ng-show="user.formErrors.newpass">
                    <div ng-repeat="error in user.formErrors.newpass">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.newpass_confirmation }">
                <input placeholder="Password Confirm" class="form-control" required="required" name="newpass_confirmation" ng-model="user.currentAccount.newpass_confirmation" type="password" value="">
                <div class="help-block" ng-show="user.formErrors.newpass_confirmation">
                    <div ng-repeat="error in user.formErrors.newpass_confirmation">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
    <div class="panel-footer">
        <div class="form-group">
            <input class="btn btn-lg btn-primary btn-block" ng-click="user.updateProfile()" ng-disabled="user.editForm" type="submit" value="Update Profile">
        </div>
    </div>
</div>
@endsection


<?php Assets::add(
        ['resources/assets/js/bootstrap/user/UserController.js',
                'resources/assets/js/bootstrap/user/UserApiService.js'],'js','pageLevel') ?>