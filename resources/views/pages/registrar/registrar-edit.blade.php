@extends( 'layout.default' )
@section('title', 'Edit Registrar')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="RegistrarController as registrar" ng-init="registrar.loadAccount(); registrar.setPageType('edit')">
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <span>Edit Registrar</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form name="registrarForm" layout="column" novalidate>
                    @include( 'pages.registrar.registrar-form' )
                    </form>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/registrar.js"></script>
@stop

