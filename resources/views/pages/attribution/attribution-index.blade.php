@extends( 'layout.default' )

@section( 'title' , 'Attribution Model' )

@section( 'navClientClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Attribution Model</h1></div>
</div>

<div ng-controller="AttributionController as attr" ng-init="attr.loadModels()">
    @if (Sentinel::hasAccess('client.add'))
    <div class="row">
        <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="attr.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Client</button>
    </div>
    @endif
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="attr.paginationCount" currentpage="attr.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="attr.currentPage" maxpage="attr.pageCount" disableceiling="attr.reachedMaxPage" disablefloor="attr.reachedFirstPage"></pagination>
                </div>
            </div>

            <attribution-model-table records="attr.models" loadingflag="attr.currentlyLoading"></attribution-model-table>

            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="attr.paginationCount" currentpage="attr.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="attr.currentPage" maxpage="attr.pageCount" disableceiling="attr.reachedMaxPage" disablefloor="attr.reachedFirstPage"></pagination>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/recordAttribution.js"></script>
@stop
