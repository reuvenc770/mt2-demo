@extends( 'layout.default' )
@section('title', 'Edit DBA')

@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" ng-controller="DBAController as dba" ng-init="dba.loadAccount()">
                <div class="panel-heading">
                    <h1 class="panel-title">Edit DBA</h1>
                </div>
                <div class="panel-body">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <fieldset>
                @include( 'pages.dba.dba-form' )
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="dba.editAccount()" type="submit" value="Update DBA Account">
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
