@extends( 'bootstrap.layout.default' )
@section('title', 'Add Role')

@section('content')
<div class="panel panel-primary" ng-controller="roleController as role">
    <div class="panel-heading">
        <div class="panel-title">Add Security Role</div>
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
            <input class="btn btn-primary btn-block" ng-click="role.saveNewRole()" ng-disabled="role.formSubmitted" type="submit" value="Add Security Role">
        </div>

        @include( 'bootstrap.pages.role.role-form' )

        <div class="form-group">
            <input class="btn btn-primary btn-block" ng-click="role.saveNewRole()" ng-disabled="role.formSubmitted" type="submit" value="Add Security Role">
        </div>
    </div>
</div>
@endsection

<?php Assets::add(
        ['resources/assets/js/bootstrap/role/RoleController.js',
                'resources/assets/js/bootstrap/role/RoleApiService.js'],'js','pageLevel') ?>