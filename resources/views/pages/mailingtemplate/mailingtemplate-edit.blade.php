@extends( 'layout.default' )
@section('title', 'Edit Mailing Template')

@section('content')
<md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
    <div flex-gt-sm="80" flex="100">
        <md-card ng-controller="MailingTemplateController as mailing" ng-init="mailing.loadAccount()">
            <md-toolbar>
                <div class="md-toolbar-tools">
                    <span>Edit Mailing Template</span>
                </div>
            </md-toolbar>
            <md-card-content>

                    @include( 'pages.mailingtemplate.mailingtemplate-form' )
                    <!-- Submit field -->
                    <div layout="column">
                        <md-button class="md-raised md-accent" ng-click="mailing.editAccount()">Update Mailing Template</md-button>
                        <md-button class="md-raised md-accent" ng-click="mailing.preview()">Preview Template</md-button>
                    </div>
            </md-card-content>
        </md-card>
    </div>
</md-content>
@endsection


@section( 'pageIncludes' )
<script src="js/mailingtemplate.js"></script>
@stop
