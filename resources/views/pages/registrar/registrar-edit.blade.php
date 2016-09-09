@extends( 'layout.default' )
@section('title', 'Edit Registrar')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="RegistrarController as registrar" ng-init="registrar.loadAccount(); registrar.setPageType('edit')">
                <md-toolbar class="md-hue-3">
                    <div class="md-toolbar-tools">
                        <span>Edit Registrar</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    @include( 'pages.registrar.registrar-form' )
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/registrar.js"></script>
@stop

