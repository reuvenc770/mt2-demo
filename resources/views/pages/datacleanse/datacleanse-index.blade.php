@extends( 'layout.default' )

@section( 'title' , 'Data Cleanse' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Data Cleanse</h1></div>
</div>

<div ng-controller="DataCleanseController as cleanse" ng-init="cleanse.load()">
    @if ( Sentinel::hasAccess( 'datacleanse.add' ) )
    <div class="row">
        <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="cleanse.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Data Cleanse</button>
    </div>
    @endif

    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="cleanse.paginationCount" currentpage="cleanse.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="cleanse.currentPage" maxpage="cleanse.pageCount" disableceiling="cleanse.reachedMaxPage" disablefloor="cleanse.reachedFirstPage"></pagination>
                </div>
            </div>

            <datacleanse-table records="cleanse.cleanses" loadingflag="cleanse.currentlyLoading"></datacleanse-table>

            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="cleanse.paginationCount" currentpage="cleanse.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="cleanse.currentPage" maxpage="cleanse.pageCount" disableceiling="cleanse.reachedMaxPage" disablefloor="cleanse.reachedFirstPage"></pagination>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/datacleanse.js"></script>
@stop
