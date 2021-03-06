@extends( 'layout.default' )
@section('title', 'Edit Registrar')

@section('content')
    <div class="panel mt2-theme-panel"  ng-controller="RegistrarController as registrar" ng-init="registrar.loadAccount()">
        <div class="panel-heading">
            <div class="panel-title">Update Registrar</div>
        </div>
        <div class="panel-body">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <fieldset>
                @include( 'pages.registrar.registrar-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="row">
            <div class="col-md-offset-4 col-md-4">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="registrar.editAccount()" ng-disabled="registrar.formSubmitted" type="submit" value="Update Registrar">
            </div>
            </div>
        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/registrar/RegistrarController.js',
                'resources/assets/js/registrar/RegistrarApiService.js'],'js','pageLevel') ?>
