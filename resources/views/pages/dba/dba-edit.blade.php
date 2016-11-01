@extends( 'layout.default' )
@section('title', 'Edit DBA')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="DBAController as dba" ng-init="dba.loadAccount()">
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <span>Edit DBA</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form name="dbaForm" layout="column" novalidate>

                        @include( 'pages.dba.dba-form' )

                        <md-button class="md-raised md-accent" ng-click="dba.editAccount( $event, dbaForm )">Update Account</md-button>
                    </form>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/dba.js"></script>
@stop
