
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
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="mailing.paginationCount" currentpage="mailing.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="mailing.currentPage" maxpage="mailing.pageCount" disableceiling="mailing.reachedMaxPage" disablefloor="mailing.reachedFirstPage"></pagination>
                </div>
            </div>

            <mailingtemplate-table loadingflag="mailing.currentlyLoading" records="mailing.templates"></mailingtemplate-table>

            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="mailing.paginationCount" currentpage="mailing.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="mailing.currentPage" maxpage="mailing.pageCount" disableceiling="mailing.reachedMaxPage" disablefloor="mailing.reachedFirstPage"></pagination>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/mailingtemplate.js"></script>
@stop
