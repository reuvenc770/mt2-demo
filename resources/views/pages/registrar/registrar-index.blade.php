@extends( 'layout.default' )

@section( 'title' , 'MT2 Registrar List' )


@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">Registrars</h1></div>
    </div>

    <div ng-controller="RegistrarController as registrar" ng-init="registrar.loadAccounts()">
        @if (Sentinel::hasAccess('registrar.add'))
        <div class="row">
            <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="registrar.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Registrar</button>
        </div>
        @endif
        <div class="row">
            <div class="col-xs-12">
                <md-content class="md-mt2-zeta-theme">
                @include( 'pages.registrar.registrar-table' )
                </md-content>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/registrar.js"></script>
@stop
