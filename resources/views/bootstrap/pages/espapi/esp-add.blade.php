@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Add ESP API Account' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
<div class="panel mt2-theme-panel" ng-controller="espController as esp">
    <div class="panel-heading">
        <div class="panel-title">Add ESP API Account</div>
    </div>
    <div class="panel-body">
        <fieldset>
            @include( 'bootstrap.pages.espapi.esp-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="esp.saveNewAccount()" ng-disabled="esp.formSubmitted" type="submit" value="Add ESP API Account">
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/espapi/EspController.js',
                'resources/assets/js/bootstrap/espapi/EspApiService.js'],'js','pageLevel') ?>