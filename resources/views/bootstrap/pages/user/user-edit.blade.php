@extends( 'bootstrap.layout.default' )
@section('title', 'Edit User')

@section('content')
<div class="panel panel-primary" ng-controller="userController as user" ng-init="user.loadAccount()">
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
            <!-- First name field -->
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.first_name }">
                <input placeholder="First Name" value="" class="form-control" required="required" name="first_name" ng-model="user.currentAccount.first_name" type="text">
                <div class="help-block" ng-show="user.formErrors.first_name">
                    <div ng-repeat="error in user.formErrors.first_name">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <!-- Last name field -->
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.last_name }">
                <input placeholder="Last Name" value="" class="form-control" required="required" name="last_name" ng-model="user.currentAccount.last_name" type="text">
                <div class="help-block" ng-show="user.formErrors.last_name">
                    <div ng-repeat="error in user.formErrors.last_name">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.roles }">
                <h4 >Roles (check all that apply)</h4>
                @foreach ($roles as $role)
                    <label class="checkbox-inline">
                        <input type="checkbox" name="items[]" value="{{ $role->id }}" ng-checked="user.currentAccount.roles.indexOf({{$role->id}})> -1" ng-click="user.toggleSelection({{$role->id}})"
                        />{{ $role->name }}
                    </label>
                @endforeach
                <div class="help-block" ng-show="user.formErrors.roles">
                    <div ng-repeat="error in user.formErrors.roles">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
    <div class="panel-footer">
        <div class="form-group">
            <input class="btn btn-lg btn-primary btn-block" ng-click="user.editAccount()" type="submit" value="Update Account">
        </div>
    </div>
</div>
@endsection


<?php Assets::add(
        ['resources/assets/js/bootstrap/user/UserController.js',
                'resources/assets/js/bootstrap/user/UserApiService.js'],'js','pageLevel') ?>