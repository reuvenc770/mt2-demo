@extends( 'layout.default' )
@section('title', 'Add User')

@section('content')
<div class="panel mt2-theme-panel" ng-controller="userController as user">
    <div class="panel-heading">
        <div class="panel-title">Add User</div>
    </div>
    <div class="panel-body">
        <fieldset>
            @include( 'bootstrap.pages.user.user-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="user.saveNewAccount()" ng-disabled="user.editForm" type="submit" value="Add User">
    </div>
</div>
@endsection


<?php Assets::add(
        ['resources/assets/js/bootstrap/user/UserController.js',
                'resources/assets/js/bootstrap/user/UserApiService.js'],'js','pageLevel') ?>