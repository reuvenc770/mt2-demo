@extends( 'layout.default' )
@section('title', 'Add Registrar')

@section('content')
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default" ng-controller="RegistrarController as registrar">
                    <div class="panel-heading">
                        <h1 class="panel-title">Add Registrar</h1>
                    </div>
                    @include( 'pages.registrar.registrar-form' )
                                <div class="form-group">
                                    <input class="btn btn-lg btn-primary btn-block" ng-click="registrar.saveNewAccount()" type="submit" value="Create Registrar">
                                </div>
                            </fieldset>
                    </div>
                </div>
            </div>
        </div>
@endsection


@section( 'pageIncludes' )
    <script src="js/registrar.js"></script>
@stop
