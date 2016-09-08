@extends( 'layout.default' )
@section('title', 'Edit Mailing Template')

@section('content')
<md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
    <div flex-gt-sm="80" flex="100">
        <md-card ng-controller="MailingTemplateController as mailing" ng-init="mailing.loadAccount()">
            <md-toolbar class="md-hue-3">
                <div class="md-toolbar-tools">
                    <span>Edit Mailing Template</span>
                </div>
            </md-toolbar>
            <md-card-content>
                <fieldset>

                    @include( 'pages.mailingtemplate.mailingtemplate-form' )
                    <!-- Submit field -->
                    <div class="form-group">
                        <input class="btn btn-lg btn-primary btn-block" ng-click="mailing.editAccount()" type="submit" value="Edit Mailing Template">
                    </div>
                    <div class="form-group">
                        <input class="btn btn-lg btn-primary btn-block" ng-click="mailing.preview()" type="submit" value="Preview Template">
                    </div>
                </fieldset>
            </md-card-content>
        </md-card>
    </div>
</md-content>
@endsection


@section( 'pageIncludes' )
<script src="js/mailingtemplate.js"></script>
@stop
