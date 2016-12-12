@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Edit List Profile' )

@section( 'angular-controller' , 'ng-controller="ListProfileController as listProfile"')

@section( 'content' )
<div class="panel" ng-class="{ 'mt2-theme-panel' : !listProfile.enableAdmiral , 'panel-danger' : listProfile.enableAdmiral }" ng-init="listProfile.prepop( {{ $prepop }} )">
    <div class="panel-heading">
        <div class="panel-title">Update List Profile</div>
    </div>
    <div class="panel-body">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <fieldset>
            @include( 'bootstrap.pages.listprofile.list-profile-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
            <input class="btn btn-block" ng-class="{ 'mt2-theme-btn-primary' : !listProfile.enableAdmiral , 'btn-danger' : listProfile.enableAdmiral }" ng-click="listProfile.updateListProfile()" type="submit" value="Update">
    </div>
</div>
@stop

<?php
Assets::add( [
    'resources/assets/js/bootstrap/listprofile/ListProfileController.js' ,
    'resources/assets/js/bootstrap/listprofile/ListProfileApiService.js' ,
] , 'js' , 'pageLevel' );
?>
