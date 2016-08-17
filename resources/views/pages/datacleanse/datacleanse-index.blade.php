@extends( 'layout.default' )

@section( 'title' , 'Data Cleanse' )

@section( 'angular-controller' , 'ng-controller="DataCleanseController as cleanse"')

@section( 'page-menu' )
    @if ( Sentinel::hasAccess( 'datacleanse.add' ) )
        <md-button ng-click="cleanse.viewAdd()" aria-label="Add Data Cleanse">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Data Cleanse</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="cleanse.load()">
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
