@extends( 'layout.default' )

@section( 'title' , 'Edit ESP API Account' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
<div class="panel mt2-theme-panel" ng-controller="espController as esp" ng-init="esp.loadAccount()">
    <div class="panel-heading">
        <div class="panel-title">Edit ESP API Account :: @{{esp.currentAccount.accountName}}</div>
    </div>
    <div class="panel-body">
        <fieldset>
            <input type="hidden" ng-model="esp.currentAccount.id" />
            @include( 'pages.espapi.esp-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="esp.editAccount()" ng-disabled="esp.formSubmitted" type="submit" value="Update ESP API Account">
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/espapi/EspController.js',
                'resources/assets/js/espapi/EspApiService.js'],'js','pageLevel') ?>