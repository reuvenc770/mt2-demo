@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Add List Profile' )

@section( 'angular-controller' , 'ng-controller="ListProfileController as listProfile"')

@section( 'content' )
<div class="panel" ng-class="{ 'mt2-theme-panel' : !listProfile.enableAdmiral , 'panel-danger' : listProfile.enableAdmiral }">
    <div class="panel-heading">
        <div class="panel-title">Add List Profile</div>
    </div>
    <div class="panel-body">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <fieldset>
            @include( 'bootstrap.pages.listprofile.list-profile-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
        <div class="form-group">
            <input class="btn btn-block" ng-class="{ 'mt2-theme-btn-primary' : !listProfile.enableAdmiral , 'btn-danger' : listProfile.enableAdmiral }" ng-click="listProfile.saveListProfile()" type="submit" value="Add List Profile">
        </div>
    </div>
</div>
@stop

<?php
Assets::add( [
    'resources/assets/js/bootstrap/listprofile/ListProfileController.js' ,
    'resources/assets/js/bootstrap/listprofile/ListProfileApiService.js' ,
] , 'js' , 'pageLevel' );
?>
