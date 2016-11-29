@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Edit List Profile Combine' )

@section( 'angular-controller' , 'ng-controller="ListProfileController as listProfile"')

@section( 'content' )
<div class="panel mt2-theme-panel" ng-init="listProfile.loadListProfileList(); listProfile.setCombine( {{ $combineId}} , '{{ $combineName }}' , {{ $listProfileIds }} );">
    <div class="panel-heading">
        <div class="panel-title">Edit List Profile Combine</div>
    </div>
    <div class="panel-body">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <fieldset>
            <div class="form-group" ng-class="{ 'has-error' : listProfile.formErrors.combineName }">
                <input placeholder="Combine Name" value="" class="form-control" ng-model="listProfile.currentCombine.combineName"
                       required="required" name="name" type="text">
                <div class="help-block" ng-show="listProfile.formErrors.combineName">
                    <div ng-repeat="error in listProfile.formErrors.combineName">
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
        <div class="form-group">
            <input class="btn btn-block mt2-theme-btn-primary" ng-click="listProfile.updateCombine()" type="submit" value="Update">
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
