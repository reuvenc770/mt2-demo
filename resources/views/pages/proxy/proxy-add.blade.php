@extends( 'layout.default' )
@section('title', 'Add Proxy')

@section('content')
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default" ng-controller="ProxyController as proxy">
                    <div class="panel-heading">
                        <h1 class="panel-title">Add Proxy</h1>
                    </div>
                    <div class="panel-body">
                            <input name="_token" type="hidden" value="{{ csrf_token() }}">
                            <fieldset>
                                <!-- Email field -->
                                <div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.name }">
                                    <input placeholder="Name" value="{{old('name') }}" class="form-control" ng-model="proxy.currentAccount.name" required="required" name="name" type="text">
                                    <span class="help-block" ng-bind="proxy.formErrors.name" ng-show="proxy.formErrors.name"></span>
                                </div>

                                <div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.ip_address }">
                                    <input placeholder="IP Address" value="{{old('name') }}" class="form-control" ng-model="proxy.currentAccount.ip_address" required="required" name="ip_address" type="text">
                                    <span class="help-block" ng-bind="proxy.formErrors.ip_address" ng-show="proxy.formErrors.ip_address"></span>
                                </div>

                                <div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.provider_name }">
                                    <input placeholder="Provider's Name" value="{{old('name') }}" class="form-control" ng-model="proxy.currentAccount.provider_name" required="required" name="provider_name" type="text">
                                    <span class="help-block" ng-bind="proxy.formErrors.provider_name" ng-show="proxy.formErrors.provider_name"></span>
                                </div>

                                <!-- Submit field -->
                                <div class="form-group">
                                    <input class="btn btn-lg btn-primary btn-block" ng-click="proxy.saveNewAccount()" type="submit" value="Create Proxy">
                                </div>
                            </fieldset>
                    </div>
                </div>
            </div>
        </div>
@endsection


@section( 'pageIncludes' )
    <script src="js/proxy.js"></script>
@stop
