@extends( 'layout.default' )

@section( 'title' , 'Edit List Profile' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Edit List Profile</h1></div>
</div>

<div ng-controller="ListProfileController as listProfile" ng-init="listProfile.loadListProfile()">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <form name="profileForm" novalidate>
                <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : listProfile.updatingListProfile }" ng-click="listProfile.updateListProfile( $event , profileForm )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : listProfile.updatingListProfile }"></span> Update</button>

                <div class="clearfix"></div>

                @include( 'pages.listprofile.list-profile-form' )

                <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : listProfile.updatingListProfile }" ng-click="listProfile.updateListProfile( $event , profileForm )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : listProfile.updatingListProfile }"></span> Update</button>
            </form>
        </div>
    </div>
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
