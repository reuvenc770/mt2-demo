@extends( 'bootstrap.layout.default' )
@section('title', 'Edit Proxy')

@section('content')
    <div class="panel panel-primary" ng-controller="ProxyController as proxy" ng-init="proxy.loadAccount()">
        <div class="panel-heading">
            <div class="panel-title">Edit Proxy</div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'bootstrap.pages.proxy.proxy-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <input class="btn btn-lg btn-primary btn-block" ng-click="proxy.editAccount()"
                   ng-disabled="proxy.formSubmitted" type="submit" value="Update Proxy">
        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/bootstrap/proxy/ProxyController.js',
                'resources/assets/js/bootstrap/proxy/ProxyApiService.js'],'js','pageLevel') ?>

