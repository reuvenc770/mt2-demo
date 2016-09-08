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
        <md-icon md-svg-src="img/icons/ic_more_horiz_white_24px.svg"></md-icon>
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
      <md-card-content>
        <div layout="row">
          <md-input-container flex-gt-sm="10" flex="30">
            <pagination-count recordcount="dataExport.paginationCount" currentpage="dataExport.currentPage"></pagination-count>
          </md-input-container>

          <md-input-container flex="auto">
            <pagination currentpage="dataExport.currentPage" maxpage="dataExport.pageCount"></pagination>
          </md-input-container>
        </div>

        <dataexport-table records="dataExport.dataExports"
        changestatus="dataExport.changeDataExportStatus(id)" loadingflag="dataExport.currentlyLoading"
        toggleinclusion="dataExport.toggleInclusion(id)" statuschangebuttontext="dataExport.massActionButtonText"
        deleteexport="dataExport.deleteDataExport(id)" copyexport="dataExport.copyDataExport(id)">
        </dataexport-table>

        <div layout="row">
          <md-input-container flex-gt-sm="10" flex="30">
            <pagination-count recordcount="dataExport.paginationCount" currentpage="dataExport.currentPage"></pagination-count>
          </md-input-container>

          <md-input-container flex="auto">
            <pagination currentpage="dataExport.currentPage" maxpage="dataExport.pageCount"></pagination>
          </md-input-container>
        </div>
      </md-card-content>
    </md-card>
  </md-content>

  <div layout="row" layout-align="end center">
    <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="dataExport.pauseSelected()">
      <span class="glyphicon glyphicons-arrow-down"></span>
      <span>@{{dataExport.massActionButtonText}} Exports</span>
    </button>
    <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="dataExport.rePullSelected()">
      Re-pull Exports
    </button>
  </div>
</div>
@stop

@section('pageIncludes')
<script src="js/dataexport.js"></script>
@stop