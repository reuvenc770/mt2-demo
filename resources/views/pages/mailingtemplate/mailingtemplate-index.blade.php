
@extends( 'layout.default' )

@section( 'title' , 'Mailing Templates' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Mailing Templates</h1></div>
</div>

<div ng-controller="MailingTemplateController as mailing" ng-init="mailing.loadAccounts()">
    @if (Sentinel::hasAccess('mailingtemplate.add'))
    <div class="row">
        <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="mailing.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Mailing Template</button>
    </div>
    @endif

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
