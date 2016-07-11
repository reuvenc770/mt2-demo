@extends( 'layout.default' )

@section( 'title' , 'Attribution Model' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('attr.model.add'))
        <md-button ng-href="{{ route( 'attr.model.add' ) }}" target=
"_self" aria-label="Add Attribution Model">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Model</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="attr.loadModels()">
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

            <attribution-model-table records="attr.models" loadingflag="attr.currentlyLoading" baseurl="app.getBaseUrl()" copymodel="attr.copyModelPreview( $event , currentModelId  )"></attribution-model-table>

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

    @include( 'pages.attribution.attribution-level-copy-sidenav' )
</div>
@stop

@section( 'pageIncludes' )
<script src="js/recordAttribution.js"></script>
@stop
