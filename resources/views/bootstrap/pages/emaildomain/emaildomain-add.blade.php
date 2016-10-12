@extends( 'bootstrap.layout.default' )
@section('title', 'Add Isp Group')

@section('content')

    <div class="panel panel-primary"  ng-controller="EmailDomainController as emailDomain">
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
            <div class="form-group">
                <input class="btn btn-lg btn-primary btn-block" ng-click="emailDomain.saveNewAccount()" ng-disabled="emailDomain.editForm" type="submit" value="Add Isp Domain">
            </div>
        </div>
    </div>


@endsection


<?php Assets::add(
        ['resources/assets/js/bootstrap/emaildomain/EmailDomainController.js',
                'resources/assets/js/bootstrap/emaildomain/EmailDomainApiService.js'],'js','pageLevel') ?>
