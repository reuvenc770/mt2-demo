@extends( 'bootstrap.layout.default' )
@section('title', 'Edit Isp Group')

@section('content')

    <div class="panel panel-primary" ng-controller="EmailDomainController as emailDomain" ng-init="emailDomain.loadAccount()">
        <div class="panel-heading">
            <div class="panel-title">Edit ISP Domain</div>
        </div>
        <div class="panel-body">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <fieldset>
                @include( 'pages.emaildomain.emaildomain-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn btn-lg btn-primary btn-block" ng-click="emailDomain.editAccount()" ng-disabled="emailDomain.editForm" type="submit" value="Update Isp Domain">
            </div>
        </div>
    </div>


@endsection


<?php Assets::add(
        ['resources/assets/js/emaildomain/EmailDomainController.js',
                'resources/assets/js/emaildomain/EmailDomainApiService.js'],'js','pageLevel') ?>
