@extends( 'bootstrap.layout.default' )
@section('title', 'Add Mailing Template')

@section('content')

    <div class="panel panel-primary" ng-controller="MailingTemplateController as mailing" ng-init="mailing.init()">
        <div class="panel-heading">
            <div class="panel-title">Add Mailing Template</div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'bootstrap.pages.mailingtemplate.mailingtemplate-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-6">
                    <input class="btn btn-lg btn-primary btn-block" ng-click="mailing.saveNewAccount()"
                           ng-disabled="emailDomain.formSubmitted" type="submit" value="Create Mailing Template">

                </div>
                <div class="col-sm-6">
                    <input class="btn btn-lg btn-success btn-block" ng-click="mailing.previewIncomplete()"
                           ng-disabled="emailDomain.formSubmitted" type="submit" value="Preview Template">

                </div>
            </div>

        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/bootstrap/mailingtemplate/MailingTemplateController.js',
                'resources/assets/js/bootstrap/mailingtemplate/MailingTemplateApiService.js'], 'js', 'pageLevel') ?>
