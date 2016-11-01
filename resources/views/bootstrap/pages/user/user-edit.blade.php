@extends( 'bootstrap.layout.default' )
@section('title', 'Edit User')

@section('content')
<div class="panel panel-primary" ng-controller="userController as user" ng-init="user.loadAccount()">
    <div class="panel-heading">
        <div class="panel-title">Edit User</div>
    </div>
    <div class="panel-body">
        <fieldset>
            @include( 'bootstrap.pages.user.user-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
        <div class="form-group">
            <input class="btn btn-lg btn-primary btn-block" ng-click="user.editAccount()" ng-disabled="user.editForm" type="submit" value="Update User">
        </div>
    </div>
</div>
@endsection


<?php Assets::add(
        ['resources/assets/js/bootstrap/user/UserController.js',
                'resources/assets/js/bootstrap/user/UserApiService.js'],'js','pageLevel') ?>