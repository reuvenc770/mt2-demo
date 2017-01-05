@extends( 'layout.default' )
@section('title', 'Add ISP Domain')

@section('content')

    <div class="panel mt2-theme-panel"  ng-controller="EmailDomainController as emailDomain">
        <div class="panel-heading">
            <div class="panel-title">Add ISP Domain</div>
        </div>
        <div class="panel-body">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <fieldset>
                @include( 'bootstrap.pages.emaildomain.emaildomain-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="emailDomain.saveNewAccount()" ng-disabled="emailDomain.editForm" type="submit" value="Add ISP Domain">
        </div>
    </div>


@endsection


<?php Assets::add(
        ['resources/assets/js/bootstrap/emaildomain/EmailDomainController.js',
                'resources/assets/js/bootstrap/emaildomain/EmailDomainApiService.js'],'js','pageLevel') ?>
