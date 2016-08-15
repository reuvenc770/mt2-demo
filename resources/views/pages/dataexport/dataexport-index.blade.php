@extends('layout.default')

@section('title', 'Data Exports')

@section('content')
<div ng-controller="DataExportController as dataExport" ng-init="dataExport.loadActiveDataExports()">

  <div class="row">
    <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="dataExport.switchDisplayedStatus()">
      <span>@{{dataExport.displayedStatusButtonText}}</span>
    </button>
    @if (Sentinel::hasAccess('dataexport.add'))
    <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="dataExport.viewAdd()">
      <span class="glyphicon glyphicon-plus"></span>
      Add Data Export
    </button>
    @endif
  </div>


  <div class="row">
    <div class="col-xs-12">
        <div class="row">
          <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
            <pagination-count recordcount="dataExport.paginationCount" currentpage="dataExport.currentPage"></pagination-count>
          </div>

          <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
            <pagination currentpage="dataExport.currentPage" maxpage="dataExport.pageCount"></pagination>
          </div>
        </div>

        <dataexport-table records="dataExport.dataExports"
        changestatus="dataExport.changeDataExportStatus(id)" loadingflag="dataExport.currentlyLoading"
        toggleinclusion="dataExport.toggleInclusion(id)" statuschangebuttontext="dataExport.massActionButtonText"
        deleteexport="dataExport.deleteDataExport(id)" copyexport="dataExport.copyDataExport(id)">
        </dataexport-table>

        <div class="row">
          <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
            <pagination-count recordcount="dataExport.paginationCount" currentpage="dataExport.currentPage"></pagination-count>
          </div>

          <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
            <pagination currentpage="dataExport.currentPage" maxpage="dataExport.pageCount"></pagination>
          </div>
        </div>
    </div>
  </div>

  <div class="row">
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