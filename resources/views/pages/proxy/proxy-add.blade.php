@extends( 'layout.default' )
@section('title', 'Add Proxy')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="ProxyController as proxy">
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <span>Add Proxy</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form name="proxyForm" layout="column" novalidate>
                        @include( 'pages.proxy.proxy-form' )
                        <md-button class="md-raised md-accent" ng-click="proxy.saveNewAccount( $event , proxyForm )">Create Proxy</md-button>
                    </form>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/proxy.js"></script>
@stop
