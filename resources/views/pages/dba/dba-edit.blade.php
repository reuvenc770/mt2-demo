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
                        <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    @include( 'pages.dba.dba-form' )
                            <div class="form-group">
                                <input class="btn btn-lg btn-primary btn-block" ng-click="dba.editAccount()" type="submit" value="Update DBA Account">
                            </div>
                    </form>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/dba.js"></script>
@stop
