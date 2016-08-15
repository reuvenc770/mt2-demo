@extends( 'layout.default' )

@section( 'title' , 'Add List Profile' )

@section( 'content' )

<div ng-controller="ListProfileController as listProfile">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <form name="profileForm" novalidate>
                <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : listProfile.creatingListProfile }" ng-click="listProfile.calculateListProfile( $event , profileForm )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : listProfile.creatingListProfile }"></span> Save</button>

                <div class="clearfix"></div>

                @include( 'pages.listprofile.list-profile-form' )

                <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : listProfile.creatingListProfile }" ng-click="listProfile.calculateListProfile( $event , profileForm )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : listProfile.creatingListProfile }"></span> Save</button>
            </form>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/listprofile.js"></script>
@stop
