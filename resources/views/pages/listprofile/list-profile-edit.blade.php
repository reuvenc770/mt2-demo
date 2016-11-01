@extends( 'layout.default' )

@section( 'title' , 'Edit List Profile' )

@section( 'content' )
<div class="panel" ng-class="{ 'panel-primary' : !listProfile.enableAdmiral , 'panel-danger' : listProfile.enableAdmiral }" ng-controller="ListProfileController as listProfile" ng-init="listProfile.prepop( {{ $id }} )">
    <div class="panel-heading">
        <div class="panel-title">Update List Profile</div>
    </div>
    <div class="panel-body">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <fieldset>
            @include( 'pages.listprofile.list-profile-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
        <div class="form-group">
            <input class="btn btn-lg btn-block" ng-class="{ 'btn-primary' : !listProfile.enableAdmiral , 'btn-danger' : listProfile.enableAdmiral }"  ng-click="app.redirect( '/listprofile' )" type="submit" value="Export to FTP">
            <input class="btn btn-lg btn-block" ng-class="{ 'btn-primary' : !listProfile.enableAdmiral , 'btn-danger' : listProfile.enableAdmiral }" ng-click="app.redirect( '/listprofile' )" type="submit" value="Update">
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/listprofile.js"></script>
@stop
