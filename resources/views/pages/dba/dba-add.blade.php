@extends( 'layout.default' )
@section('title', 'Add DBA')

@section('content')
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default" ng-controller="DBAController as dba">
                    <div class="panel-heading">
                        <h1 class="panel-title">Add DBA</h1>
                    </div>
                    <div class="panel-body">
                        <input name="_token" type="hidden" value="{{ csrf_token() }}">
                        <fieldset>
                    @include( 'pages.dba.dba-form' )
                                <!-- Submit field -->
                                <div class="form-group">
                                    <input class="btn btn-lg btn-primary btn-block" ng-click="dba.saveNewAccount()" type="submit" value="Create Account">
                                </div>
                            </fieldset>
                    </div>
                </div>
            </div>
        </div>
@endsection


@section( 'pageIncludes' )
    <script src="js/dba.js"></script>
@stop
