@extends( 'bootstrap.layout.default' )
@section('title', 'Edit Client')

@section('content')
    <div class="panel panel-primary"  ng-controller="ClientController as client" ng-init="client.loadAccount()">
        <div class="panel-heading">
            <div class="panel-title">Edit Client</div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'bootstrap.pages.client.client-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn btn-primary btn-block" ng-click="client.updateAccount()" ng-disabled="client.formSubmitted" type="submit" value="Update Client">
            </div>
        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/bootstrap/client/ClientController.js',
                'resources/assets/js/bootstrap/client/ClientApiService.js'],'js','pageLevel') ?>