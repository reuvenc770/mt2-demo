@extends( 'layout.default' )

@section( 'title' , 'Edit ESP Account' )

@section( 'content' )
<div class="panel mt2-theme-panel" ng-controller="espController as esp" ng-init="esp.loadAccount()">
    <div class="panel-heading">
        <div class="panel-title">Edit ESP Account :: @{{esp.currentAccount.name}}</div>
    </div>
    <div class="panel-body">
        <fieldset>
            @include( 'pages.esp.esp-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
        <div class="row">
        <div class="col-md-offset-4 col-md-4">
            <button type="button" class="btn mt2-theme-btn-primary btn-block" ng-disabled="esp.formSubmitted" ng-click="esp.editAccount()">Update ESP Account</button>
        </div>
        </div>
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/esp/EspController.js',
                'resources/assets/js/esp/EspService.js'],'js','pageLevel') ?>
