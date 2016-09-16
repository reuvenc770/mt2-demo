@extends('layout.default')

@section('title', 'Data Exports')

@section( 'angular-controller' , 'ng-controller="DataExportController as dataExport"')

@section( 'page-menu' )
    <div ng-show="app.largePageWidth()">
      <md-button ng-click="dataExport.switchDisplayedStatus()">
          <span>@{{dataExport.displayedStatusButtonText}}</span>
      </md-button>
      @if (Sentinel::hasAccess('dataexport.add'))
        <md-button ng-click="dataExport.viewAdd()" aria-label="Add Data Export">
            <span>Add Data Export</span>
        </md-button>
      @endif
    </div>

    <md-menu ng-hide="app.largePageWidth()" md-position-mode="target-right target">
      <md-button aria-label="Open menu" class="md-icon-button" ng-click="$mdOpenMenu($event)">
        <md-icon md-svg-src="img/icons/ic_more_horiz_black_24px.svg"></md-icon>
      </md-button>
      <md-menu-content width="3">
        <md-menu-item>
          <md-button ng-click="dataExport.switchDisplayedStatus()">
              <span>@{{dataExport.displayedStatusButtonText}}</span>
          </md-button>
        </md-menu-item>
        @if (Sentinel::hasAccess('dataexport.add'))
          <md-menu-item>
            <md-button ng-click="dataExport.viewAdd()" aria-label="Add Data Export">
                <span>Add Data Export</span>
            </md-button>
          </md-menu-item>
        @endif
      </md-menu-content>
    </md-menu>
@stop

@section('content')
<div ng-init="dataExport.loadActiveDataExports()">
  <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
    <md-card>
      @include( 'pages.dataexport.dataexport-table' )
    </md-card>
  </md-content>
  <div layout="row" layout-align="end center">
    <md-button class="md-raised md-warn" ng-click="dataExport.pauseSelected()">
      <md-icon md-svg-icon="img/icons/ic_pause_white_18px.svg"></md-icon> @{{dataExport.massActionButtonText}} Exports
    </md-button>
    <md-button class="md-raised md-accent" ng-click="dataExport.rePullSelected()">
      <md-icon md-svg-icon="img/icons/ic_refresh_white_18px.svg"></md-icon> Re-pull Exports
    </md-button>
  </div>
</div>
@stop

@section('pageIncludes')
<script src="js/dataexport.js"></script>
@stop