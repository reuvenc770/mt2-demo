@extends( 'layout.default' )
@section('title', 'Add Registrar')

@section('content')
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default" ng-controller="RegistrarController as registrar">
                    <div class="panel-heading">
                        <h1 class="panel-title">Add Registrar</h1>
                    </div>
                    <div class="panel-body">
                            <input name="_token" type="hidden" value="{{ csrf_token() }}">
                            <fieldset>
                                <!-- Email field -->
                                <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.name }">
                                    <input placeholder="Name" value="{{old('name') }}" class="form-control" ng-model="registrar.currentAccount.name" required="required" name="name" type="text">
                                    <span class="help-block" ng-bind="registrar.formErrors.name" ng-show="registrar.formErrors.name"></span>
                                </div>

                                <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.username }">
                                    <input placeholder="Username" value="{{old('name') }}" class="form-control" ng-model="registrar.currentAccount.username" required="required" name="username" type="text">
                                    <span class="help-block" ng-bind="registrar.formErrors.username" ng-show="registrar.formErrors.username"></span>
                                </div>


                                <!-- Submit field -->
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
