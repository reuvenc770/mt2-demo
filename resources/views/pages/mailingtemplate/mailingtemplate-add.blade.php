@extends( 'layout.default' )
@section('title', 'Add Mailing Template')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="80" flex="100">
            <md-card ng-controller="MailingTemplateController as mailing" ng-init="mailing.init()">
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <span>Add Mailing Template</span>
                    </div>
                </md-toolbar>
                <md-card-content>

                        @include( 'pages.mailingtemplate.mailingtemplate-form' )
                        <!-- Submit field -->
                        <div layout="column">
                            <md-button class="md-raised md-accent" ng-click="mailing.saveNewAccount( $event, mailingForm )">Create Mailing Template</md-button>
                            <md-button class="md-raised md-accent" ng-click="mailing.previewIncomplete()">Preview Template</md-button>
                        </div>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/mailingtemplate.js"></script>
@stop
