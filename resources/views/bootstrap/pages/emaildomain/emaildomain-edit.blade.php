@extends( 'bootstrap.layout.default' )
@section('title', 'Edit ISP Domain')

@section('content')

    <div class="panel mt2-theme-panel" ng-controller="EmailDomainController as emailDomain" ng-init="emailDomain.loadAccount()">
        <div class="panel-heading">
            <div class="panel-title">Edit ISP Domain</div>
        </div>
        <div class="panel-body">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <fieldset>
                @include( 'bootstrap.pages.emaildomain.emaildomain-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="emailDomain.editAccount()" ng-disabled="emailDomain.editForm" type="submit" value="Update ISP Domain">
            </div>
        </div>
    </div>


@endsection


<?php Assets::add(
        ['resources/assets/js/bootstrap/emaildomain/EmailDomainController.js',
                'resources/assets/js/bootstrap/emaildomain/EmailDomainApiService.js'],'js','pageLevel') ?>
