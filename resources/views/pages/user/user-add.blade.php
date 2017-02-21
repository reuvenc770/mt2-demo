@extends( 'layout.default' )
@section('title', 'Add User')

@section('content')
<div class="panel mt2-theme-panel" ng-controller="userController as user">
    <div class="panel-heading">
        <div class="panel-title">Add User</div>
    </div>
    <div class="panel-body">
        <fieldset>
            @include( 'pages.user.user-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
        <div class="row">
        <div class="col-md-offset-4 col-md-4">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="user.saveNewAccount()" ng-disabled="user.editForm" type="submit" value="Add User">
        </div>
        </div>
    </div>
</div>
@endsection


<?php Assets::add(
        ['resources/assets/js/user/UserController.js',
                'resources/assets/js/user/UserApiService.js'],'js','pageLevel') ?>