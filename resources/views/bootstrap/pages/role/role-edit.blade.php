@extends( 'layout.default' )
@section('title', 'Edit Role')

@section('content')
<div class="panel mt2-theme-panel" ng-controller="roleController as role" ng-init="role.initEditPage()">
    <div class="panel-heading">
        <div class="panel-title">Edit Security Role</div>
    </div>
    <div class="panel-body">
        <div class="form-group" ng-class="{ 'has-error' : role.formErrors.name }">
            <input placeholder="Name" value="" class="form-control" type="text" name="name" ng-model="role.currentRole.name" required="required" />
            <div class="help-block" ng-show="role.formErrors.name">
                <div ng-repeat="error in role.formErrors.name">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="role.editRole()" ng-disabled="role.formSubmitted" type="submit" value="Update Security Role">
        </div>

        @include( 'bootstrap.pages.role.role-form' )

        <div class="form-group">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="role.editRole()" ng-disabled="role.formSubmitted" type="submit" value="Update Security Role">
        </div>
    </div>
</div>

@endsection

<?php Assets::add(
        ['resources/assets/js/bootstrap/role/RoleController.js',
                'resources/assets/js/bootstrap/role/RoleApiService.js'],'js','pageLevel') ?>