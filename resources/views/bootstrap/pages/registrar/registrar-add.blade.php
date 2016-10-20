@extends( 'bootstrap.layout.default' )
@section('title', 'Add Registrar')

@section('content')
    <div class="panel panel-primary"  ng-controller="RegistrarController as registrar">
        <div class="panel-heading">
            <div class="panel-title">Add Registrar</div>
        </div>
        <div class="panel-body">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <fieldset>
                @include( 'bootstrap.pages.registrar.registrar-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn btn-primary btn-block" ng-click="registrar.saveNewAccount()" ng-disabled="registrar.formSubmitted" type="submit" value="Add Registrar">
            </div>
        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/bootstrap/registrar/RegistrarController.js',
                'resources/assets/js/bootstrap/registrar/RegistrarApiService.js'],'js','pageLevel') ?>
