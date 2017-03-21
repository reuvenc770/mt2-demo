@extends( 'layout.default' )

@section( 'title' , 'Add List Profile' )

@section( 'angular-controller' , 'ng-controller="ListProfileController as listProfile"')

@section( 'content' )
<div class="panel" ng-class="{ 'mt2-theme-panel' : !listProfile.enableAdmiral , 'panel-danger' : listProfile.enableAdmiral }" ng-init="listProfile.fixInitialExportFields()">
    <div class="panel-heading">
        <div class="panel-title">Add List Profile</div>
    </div>
    <div class="panel-body">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <fieldset>
            @include( 'pages.listprofile.list-profile-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
        <div class="row">
        <div class="col-md-offset-4 col-md-4">
            <input class="btn btn-block" ng-class="{ 'mt2-theme-btn-primary' : !listProfile.enableAdmiral , 'btn-danger' : listProfile.enableAdmiral }" ng-click="listProfile.saveListProfile()" type="submit" value="Add List Profile">
        </div>
        </div>
    </div>
</div>

@include( 'pages.listprofile.list-profile-add-feedgroup' )
@stop

<?php
Assets::add( [
    'resources/assets/js/listprofile/ListProfileController.js' ,
    'resources/assets/js/listprofile/ListProfileApiService.js' ,
    'resources/assets/js/feedgroup/FeedGroupApiService.js' ,
    'resources/assets/js/feed/FeedApiService.js'
] , 'js' , 'pageLevel' );
?>
