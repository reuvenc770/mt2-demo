@extends( 'layout.default' )

@section( 'title' , 'Add List Profile' )

@section( 'angular-controller' , 'ng-controller="ListProfileController as listProfile"')

@section( 'page-menu' )
@stop

@section( 'content' )
<md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
   <div flex-gt-sm="70" flex="100">

         @include( 'pages.listprofile.list-profile-form' )

         <div layout="row" layout-align="end end">
             <md-button layout="row" class="md-raised md-accent">
                 <md-icon class="material-icons" md-font-set="material-icons">archive</md-icon>
                 <span flex> Export to FTP</span>
             </md-button>
             <md-button layout="row" class="md-raised md-accent">
                 <md-icon class="material-icons" md-font-set="material-icons">save</md-icon>
                 <span flex> Save</span>
             </md-button>
         </div>

   </div>
</md-content>
@stop

@section( 'pageIncludes' )
<script src="js/listprofile.js"></script>
@stop
