@extends( 'layout.default' )
@section('title', 'Edit Registrar')

@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" ng-controller="RegistrarController as registrar" ng-init="registrar.loadAccount()">
                <div class="panel-heading">
                    <h1 class="panel-title">Edit Registrar</h1>
                </div>
                @include( 'pages.registrar.registrar-form' )


                        <!-- Submit field -->
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="registrar.editAccount()" type="submit" value="Edit Registrar">
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

