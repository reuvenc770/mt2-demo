@extends( 'layout.default' )

@section( 'title' , 'Attribution Model' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('attr.model.add'))
        <md-button ng-href="{{ route( 'attr.model.add' ) }}" target="_self" aria-label="Add Attribution Model">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Model</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="attr.loadModels()">
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
            <md-card-content>
                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="attr.paginationCount" currentpage="attr.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="attr.currentPage" maxpage="attr.pageCount" disableceiling="attr.reachedMaxPage" disablefloor="attr.reachedFirstPage"></pagination>
                    </md-input-container>
                </div>

                <attribution-model-table records="attr.models" loadingflag="attr.currentlyLoading" baseurl="app.getBaseUrl()" copymodel="attr.copyModelPreview( $event , currentModelId  )"></attribution-model-table>

                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="attr.paginationCount" currentpage="attr.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="attr.currentPage" maxpage="attr.pageCount" disableceiling="attr.reachedMaxPage" disablefloor="attr.reachedFirstPage"></pagination>
                    </md-input-container>
                </div>
            </md-card-content>
        </md-card>
    </md-content>

    @include( 'pages.attribution.attribution-level-copy-sidenav' )
</div>
@stop

@section( 'pageIncludes' )
<script src="js/recordAttribution.js"></script>
@stop
