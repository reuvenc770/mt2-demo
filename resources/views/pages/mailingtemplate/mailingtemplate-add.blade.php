@extends( 'layout.default' )
@section('title', 'Add Mailing Template')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="80" flex="100">
            <md-card ng-controller="MailingTemplateController as mailing" ng-init="mailing.init()">
                <md-toolbar class="md-hue-3">
                    <div class="md-toolbar-tools">
                        <span>Add Mailing Template</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <fieldset>

                        @include( 'pages.mailingtemplate.mailingtemplate-form' )
                        <!-- Submit field -->
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="mailing.saveNewAccount()" type="submit" value="Create Mailing Template">
                        </div>
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="mailing.previewIncomplete()" type="submit" value="Preview Template">
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
