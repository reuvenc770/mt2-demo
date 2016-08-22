@extends( 'layout.default' )
@section('title', 'Add Proxy')

@section('content')
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default" ng-controller="ProxyController as proxy">
                    <div class="panel-heading">
                        <h1 class="panel-title">Add Proxy</h1>
                    </div>
                    @include( 'pages.proxy.proxy-form' )
                                <!-- Submit field -->
                                <div class="form-group">
                                    <input class="btn btn-primary btn-block" ng-click="proxy.saveNewAccount()" type="submit" value="Create Proxy">
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
