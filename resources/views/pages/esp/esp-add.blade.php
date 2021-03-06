@extends( 'layout.default' )

@section( 'title' , 'Add ESP Account' )

@section( 'content' )
<div class="panel mt2-theme-panel" ng-controller="espController as esp">
    <div class="panel-heading">
        <div class="panel-title">Add ESP Account</div>
    </div>
    <div class="panel-body">
        <p><strong>Note:</strong> After an ESP is added on this page you must reach out to tech so that they can complete the setup. You will not be able to use this ESP in MT2 until tech confirms it is ready.</p>
        <fieldset>
            @include ( 'pages.esp.esp-form' )
        </fieldset>
    </div>
    <div class="panel-footer">
        <div class="row">
        <div class="col-md-offset-4 col-md-4">
            <button type="button" class="btn mt2-theme-btn-primary btn-block" ng-disabled="esp.formSubmitted" ng-click="esp.saveNewAccount()">Add ESP Account</button>
        </div>
        </div>
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/esp/EspController.js',
                'resources/assets/js/esp/EspService.js'],'js','pageLevel') ?>
