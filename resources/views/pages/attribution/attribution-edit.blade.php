@extends( 'layout.default' )

@section( 'title' , 'Edit Attribution Model' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )

    <div ng-show="app.largePageWidth()">
        @if (Sentinel::hasAccess('api.attribution.model.update'))
            <md-button ng-click="attr.updateModel( $event , attrModelForm )" aria-label="Add Attribution Model">
                <span>Update Model</span>
            </md-button>
        @endif

        @if (Sentinel::hasAccess('api.attribution.model.copyLevels'))
            <md-button ng-click="attr.copyModelPreview( $event )" aria-label="Add Attribution Model">
                <span>Copy Model</span>
            </md-button>
        @endif
    </div>

    <md-menu ng-hide="app.largePageWidth()" md-position-mode="target-right target">
      <md-button aria-label="Open menu" class="md-icon-button" ng-click="$mdOpenMenu($event)">
        <md-icon md-svg-src="img/icons/ic_more_horiz_white_24px.svg"></md-icon>
      </md-button>
      <md-menu-content width="3">
        <md-menu-item>
            @if (Sentinel::hasAccess('api.attribution.model.update'))
                <md-button ng-click="attr.updateModel( $event , attrModelForm )" aria-label="Add Attribution Model">
                    <span>Update Model</span>
                </md-button>
            @endif
        </md-menu-item>
          <md-menu-item>
            @if (Sentinel::hasAccess('api.attribution.model.copyLevels'))
                <md-button ng-click="attr.copyModelPreview( $event )" aria-label="Add Attribution Model">
                    <span>Copy Model</span>
                </md-button>
            @endif
          </md-menu-item>
      </md-menu-content>
    </md-menu>

@stop

@section( 'content' )
<div ng-init="attr.prepopModel()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            @include( 'pages.attribution.attribution-form' )
        </div>
    </md-content>

    @include( 'pages.attribution.attribution-level-copy-sidenav' )
</div>
@stop


@section( 'pageIncludes' )
<script src="js/recordAttribution.js"></script>
@stop
