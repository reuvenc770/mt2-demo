@extends( 'layout.default' )
@section('title', 'Edit Proxy')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="ProxyController as proxy" ng-init="proxy.loadAccount()">
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <span>Edit Proxy</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form name="proxyForm" layout="column" novalidate>
                        @include( 'pages.proxy.proxy-form' )
                        <md-button class="md-raised md-accent" ng-click="proxy.editAccount( $event , proxyForm )">Edit Proxy</md-button>
                    </form>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/proxy.js"></script>
@stop




