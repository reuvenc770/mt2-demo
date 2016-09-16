
@extends( 'layout.default' )

@section( 'title' , 'Mailing Templates' )

@section( 'navEspClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="MailingTemplateController as mailing"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('mailingtemplate.add'))
        <md-button ng-click="mailing.viewAdd()" aria-label="Add Mailing Templates">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Mailing Templates</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="mailing.loadAccounts()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <md-card flex-gt-md="70" flex="100">
            @include( 'pages.mailingtemplate.mailingtemplate-table' )
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/mailingtemplate.js"></script>
@stop
