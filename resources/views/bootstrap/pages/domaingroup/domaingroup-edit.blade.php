@extends( 'bootstrap.layout.default' )
@section('title', 'Edit ISP Group')

@section('content')
    <div class="panel mt2-theme-panel"  ng-controller="DomainGroupController as dg"  ng-init="dg.loadAccount()">
        <div class="panel-heading">
            <div class="panel-title">Edit ISP Group</div>
        </div>
        <div class="panel-body">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <fieldset>
                @include( 'bootstrap.pages.domaingroup.domaingroup-form' )

                <div class="panel panel-info" ng-if="dg.currentAccount.domains.length > 0">
                    <div class="panel-heading">Domains</div>
                    <ul class="list-group">
                        <li class="list-group-item cmp-list-item-condensed" ng-repeat="domain in dg.currentAccount.domains track by $index"> @{{ domain.domain_name }}</li>
                    </ul>
                </div>
            </fieldset>
        </div>
        <div class="panel-footer">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="dg.editAccount()"  ng-disabled="dg.editForm" type="submit" value="Update ISP Group">
        </div>
    </div>
@endsection


<?php Assets::add(
        ['resources/assets/js/bootstrap/domaingroup/DomainGroupController.js',
                'resources/assets/js/bootstrap/domaingroup/DomainGroupApiService.js'],'js','pageLevel') ?>