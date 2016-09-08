@extends( 'layout.default' )
@section('title', 'Edit Proxy')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="ProxyController as proxy" ng-init="proxy.loadAccount()">
                <md-toolbar class="md-hue-3">
                    <div class="md-toolbar-tools">
                        <span>Edit Proxy</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                @include( 'pages.proxy.proxy-form' )
                        <!-- Submit field -->
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="proxy.editAccount()" type="submit" value="Edit Proxy">
                        </div>
                    </fieldset>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/proxy.js"></script>
@stop




