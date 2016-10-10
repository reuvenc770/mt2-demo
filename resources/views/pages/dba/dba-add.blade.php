@extends( 'layout.default' )
@section('title', 'Add DBA')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="DBAController as dba">
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <span>Add DBA</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form name="dbaForm" layout="column" novalidate>
                        @include( 'pages.dba.dba-form' )

                        <md-button class="md-raised md-accent" ng-click="dba.saveNewAccount( $event, dbaForm )">Create Account</md-button>
                    </form>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/dba.js"></script>
@stop
