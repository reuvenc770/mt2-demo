@extends( 'layout.default' )
@section('title', 'Edit Mailing Template')

@section('content')

    <div class="panel mt2-theme-panel" ng-controller="MailingTemplateController as mailing" ng-init="mailing.loadAccount()">
        <div class="panel-heading">
            <div class="panel-title">Edit Mailing Template</div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'pages.mailingtemplate.mailingtemplate-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-6">
                    <input class="btn mt2-theme-btn-primary btn-block" ng-click="mailing.editAccount()"
                           ng-disabled="mailing.formSubmitted" type="submit" value="Update Mailing Template">

                </div>
                <div class="col-sm-6">
                    <input class="btn mt2-theme-btn-secondary btn-block" ng-click="mailing.previewIncomplete()"
                           ng-disabled="mailing.formSubmitted" type="submit" value="Preview Template">

                </div>
            </div>

        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/mailingtemplate/MailingTemplateController.js',
                'resources/assets/js/mailingtemplate/MailingTemplateApiService.js'], 'js', 'pageLevel') ?>
