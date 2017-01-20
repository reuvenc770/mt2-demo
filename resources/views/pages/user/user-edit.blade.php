@extends( 'layout.default' )
@section('title', 'Edit User')

@section('content')
<div class="panel mt2-theme-panel" ng-controller="userController as user" ng-init="user.loadAccount()">
    <div class="panel-heading">
        <div class="panel-title">Edit User</div>
    </div>
    <div class="panel-body">
        <fieldset>
            @include( 'pages.user.user-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="user.editAccount()" ng-disabled="user.editForm" type="submit" value="Update User">
    </div>
</div>
@endsection


<?php Assets::add(
        ['resources/assets/js/user/UserController.js',
                'resources/assets/js/user/UserApiService.js'],'js','pageLevel') ?>