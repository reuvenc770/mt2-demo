@extends( 'layout.default' )
@section('title', 'Edit Proxy')

@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" ng-controller="ProxyController as proxy" ng-init="proxy.loadAccount()">
                <div class="panel-heading">
                    <h1 class="panel-title">Edit Proxy</h1>
                </div>
                @include( 'pages.proxy.proxy-form' )
                        <!-- Submit field -->
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="proxy.editAccount()" type="submit" value="Edit Proxy">
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




