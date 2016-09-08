
@extends( 'layout.default' )

@section( 'title' , 'Mailing Templates' )

@section( 'navEspClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="MailingTemplateController as mailing"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('mailingtemplate.add'))
        <md-button ng-click="mailing.viewAdd()" aria-label="Add Mailing Templates">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Mailing Templates</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="mailing.loadAccounts()">
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
            <md-card-content>
                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="mailing.paginationCount" currentpage="mailing.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="mailing.currentPage" maxpage="mailing.pageCount" disableceiling="mailing.reachedMaxPage" disablefloor="mailing.reachedFirstPage"></pagination>
                    </md-input-container>
                </div>

                <mailingtemplate-table loadingflag="mailing.currentlyLoading" records="mailing.templates"></mailingtemplate-table>

                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="mailing.paginationCount" currentpage="mailing.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="mailing.currentPage" maxpage="mailing.pageCount" disableceiling="mailing.reachedMaxPage" disablefloor="mailing.reachedFirstPage"></pagination>
                    </md-input-container>
                </div>
            </md-card-content>
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/mailingtemplate.js"></script>
@stop
