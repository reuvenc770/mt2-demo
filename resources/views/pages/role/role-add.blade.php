@extends( 'layout.default' )
@section('title', 'Add Role')

@section('content')
<div class="panel mt2-theme-panel" ng-controller="roleController as role">
    <div class="panel-heading">
        <div class="panel-title">Add Security Role</div>
    </div>
    <div class="panel-body">

        <div class="row">
        <div class="col-md-offset-4 col-md-4">
        <div class="form-group" ng-class="{ 'has-error' : role.formErrors.name }">
            <input placeholder="Name" value="" class="form-control" type="text" name="name" ng-model="role.currentRole.name" required="required" />
            <div class="help-block" ng-show="role.formErrors.name">
                <div ng-repeat="error in role.formErrors.name">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="role.saveNewRole()" ng-disabled="role.formSubmitted" type="submit" value="Add Security Role">
        </div>
        </div>
        </div>

        @include( 'pages.role.role-form' )

        <div class="row">
        <div class="col-md-offset-4 col-md-4">
        <div class="form-group">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="role.saveNewRole()" ng-disabled="role.formSubmitted" type="submit" value="Add Security Role">
        </div>
        </div>
        </div>
    </div>
</div>
@endsection

<?php Assets::add(
        ['resources/assets/js/role/RoleController.js',
                'resources/assets/js/role/RoleApiService.js'],'js','pageLevel') ?>