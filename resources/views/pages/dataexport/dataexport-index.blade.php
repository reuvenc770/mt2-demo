@extends('layout.default')

@section('title', 'Data Exports')

@section('content')
<div class="row">
  <div class="page-header col-xs-12"><h1 class="text-center">Data Exports</h1></div>
</div>
<div ng-controller="DataExportController as dataExport" ng-init="dataExport.loadActiveDataExports()">
  @if (Sentinel::hasAccess('dataExport.add'))
  <div class="row">
    <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="dataExport.viewAdd()">
      <span class="glyphicon glyphicon-plus"></span>
      Add Data Export
    </button>
  </div>
  @endif

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

        <dataexport-table records="dataExport.dataExports" loadingflag="dataExport.currentlyLoading" >
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
</div>
@stop

@section('pageIncludes')
<script src="js/dataexport.js"></script>
@stop