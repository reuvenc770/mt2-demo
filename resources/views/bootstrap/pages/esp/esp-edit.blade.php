@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Edit ESP Account' )

@section( 'content' )
<div class="panel panel-primary" ng-controller="espController as esp" ng-init="esp.loadAccount()">
    <div class="panel-heading">
        <div class="panel-title">Edit ESP Account :: @{{esp.currentAccount.name}}</div>
    </div>
    <div class="panel-body">
        <fieldset>
            @include( 'bootstrap.pages.esp.esp-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
        <div class="form-group">
            <button type="button" class="btn btn-primary btn-block" ng-disabled="esp.formSubmitted" ng-click="esp.editAccount()">Update ESP Account</button>
        </div>
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/esp/EspController.js',
                'resources/assets/js/bootstrap/esp/EspService.js'],'js','pageLevel') ?>
