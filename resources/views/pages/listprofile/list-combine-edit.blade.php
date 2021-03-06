@extends( 'layout.default' )

@section( 'title' , 'Edit List Profile Combine' )

@section( 'angular-controller' , 'ng-controller="ListProfileController as listProfile"')

@section( 'content' )
<div class="panel mt2-theme-panel" ng-init="listProfile.loadListProfileList(); listProfile.setCombine( {{ $combineId}} , '{{ $ftpFolder }}', '{{ $combineName }}' ,'{{ $combineParty }}', {{ $listProfileIds }} );">
    <div class="panel-heading">
        <div class="panel-title">Edit List Profile Combine</div>
    </div>
    <div class="panel-body">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <fieldset>
            <div class="form-group" ng-class="{ 'has-error' : listProfile.formErrors.combineName }">
                <label>Combine Name</label>
                <input placeholder="Combine Name" value="" class="form-control" ng-model="listProfile.currentCombine.combineName"
                       required="required" name="name" type="text">
                <div class="help-block" ng-show="listProfile.formErrors.combineName">
                    <div ng-repeat="error in listProfile.formErrors.combineName">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : listProfile.formErrors.ftpFolder }">
                <label>FTP Folder</label>
                <input placeholder="ftp_folder" value="" class="form-control" ng-model="listProfile.currentCombine.ftpFolder"
                       required="required" name="name" type="text">
                <div class="help-block" ng-show="listProfile.formErrors.ftpFolder">
                    <div ng-repeat="error in listProfile.formErrors.ftpFolder">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <lite-membership-widget height="200" recordlist="listProfile.listProfilesList" namefield="listProfile.lpListNameField" chosenrecordlist="listProfile.currentCombine.selectedProfiles" availablerecordtitle="'Available List Profiles'" chosenrecordtitle="'Selected List Profiles'"></lite-membership-widget>
                <div class="has-error">
                    <div class="help-block" ng-show="listProfile.formErrors.selectedProfiles">
                        <div ng-repeat="error in listProfile.formErrors.selectedProfiles">
                            <div ng-bind="error"></div>
                        </div>
                    </div>
                </div>
            </div>

        </fieldset>

    </div>
    <div class="panel-footer">
        <div class="row">
        <div class="col-md-offset-4 col-md-4">
            <input class="btn btn-block mt2-theme-btn-primary" ng-click="listProfile.updateCombine()" type="submit" value="Update List Profile Combine">
        </div>
        </div>
    </div>
</div>
@stop

<?php
Assets::add( [
    'resources/assets/js/listprofile/ListProfileController.js' ,
    'resources/assets/js/listprofile/ListProfileApiService.js' ,
    'resources/assets/js/feedgroup/FeedGroupApiService.js',
    'resources/assets/js/feed/FeedApiService.js'
] , 'js' , 'pageLevel' );
?>
