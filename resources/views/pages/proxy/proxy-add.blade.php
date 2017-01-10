@extends( 'layout.default' )
@section('title', 'Add Proxy')

@section('content')
    <div class="panel mt2-theme-panel" ng-controller="ProxyController as proxy">
        <div class="panel-heading">
            <div class="panel-title">Add Proxy</div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'pages.proxy.proxy-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="proxy.saveNewAccount()"
                   ng-disabled="proxy.formSubmitted" type="submit" value="Add Proxy">
        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/proxy/ProxyController.js',
                'resources/assets/js/proxy/ProxyApiService.js'],'js','pageLevel') ?>

