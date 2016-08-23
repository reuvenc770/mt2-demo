@extends( 'layout.default' )

@section( 'title' , 'Edit List Profile' )

@section( 'content' )

<div ng-controller="ListProfileController as listProfile" ng-init="listProfile.loadListProfile()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">

        <div flex-gt-md="50" flex="100">
            <form name="profileForm" novalidate>
                <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : listProfile.updatingListProfile }" ng-click="listProfile.updateListProfile( $event , profileForm )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : listProfile.updatingListProfile }"></span> Update</button>

                <div class="clearfix"></div>

                @include( 'pages.listprofile.list-profile-form' )

                <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : listProfile.updatingListProfile }" ng-click="listProfile.updateListProfile( $event , profileForm )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : listProfile.updatingListProfile }"></span> Update</button>
            </form>
        </div>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/listprofile.js"></script>

  <!--
    IE8 support, see AngularJS Internet Explorer Compatibility http://docs.angularjs.org/guide/ie
    For Firefox 3.6, you will also need to include jQuery and ECMAScript 5 shim
  -->
  <!--[if lt IE 9]>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/es5-shim/2.2.0/es5-shim.js"></script>
    <script>
      document.createElement('ui-select');
      document.createElement('ui-select-match');
      document.createElement('ui-select-choices');
    </script>
  <![endif]-->

@stop
