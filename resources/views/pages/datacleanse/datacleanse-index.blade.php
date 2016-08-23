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
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
            <md-card-content>
                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="cleanse.paginationCount" currentpage="cleanse.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="cleanse.currentPage" maxpage="cleanse.pageCount" disableceiling="cleanse.reachedMaxPage" disablefloor="cleanse.reachedFirstPage"></pagination>
                    </md-input-container>
                </div>

                <datacleanse-table records="cleanse.cleanses" loadingflag="cleanse.currentlyLoading"></datacleanse-table>

                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="cleanse.paginationCount" currentpage="cleanse.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="cleanse.currentPage" maxpage="cleanse.pageCount" disableceiling="cleanse.reachedMaxPage" disablefloor="cleanse.reachedFirstPage"></pagination>
                    </md-input-container>
                </div>
            </md-card-content>
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/datacleanse.js"></script>
@stop
