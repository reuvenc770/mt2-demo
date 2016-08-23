@extends( 'layout.default' )

@section( 'title' , 'Add List Profile' )

@section( 'content' )

<div ng-controller="ListProfileController as listProfile">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">

        <div flex-gt-md="50" flex="100">
            <form name="profileForm" novalidate>
                <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : listProfile.creatingListProfile }" ng-click="listProfile.calculateListProfile( $event , profileForm )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : listProfile.creatingListProfile }"></span> Save</button>

                <div class="clearfix"></div>

                @include( 'pages.listprofile.list-profile-form' )

                <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : listProfile.creatingListProfile }" ng-click="listProfile.calculateListProfile( $event , profileForm )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : listProfile.creatingListProfile }"></span> Save</button>
            </form>
        </div>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/listprofile.js"></script>
@stop
