@extends( 'layout.default' )
@section('title', 'Edit Proxy')

@section('content')
    <div class="panel mt2-theme-panel" ng-controller="ProxyController as proxy" ng-init="proxy.loadAccount()">
        <div class="panel-heading">
            <div class="panel-title">Edit Proxy</div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'pages.proxy.proxy-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="row">
            <div class="col-md-offset-4 col-md-4">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="proxy.editAccount()"
                   ng-disabled="proxy.formSubmitted" type="submit" value="Update Proxy">
            </div>
            </div>
        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/proxy/ProxyController.js',
                'resources/assets/js/proxy/ProxyApiService.js'],'js','pageLevel') ?>

