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
                @include( 'pages.emaildomain.emaildomain-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="row">
            <div class="col-md-offset-4 col-md-4">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="emailDomain.saveNewAccount()" ng-disabled="emailDomain.editForm" type="submit" value="Add ISP Domain">
            </div>
            </div>
        </div>
    </div>


@endsection


<?php Assets::add(
        ['resources/assets/js/emaildomain/EmailDomainController.js',
                'resources/assets/js/emaildomain/EmailDomainApiService.js'],'js','pageLevel') ?>
