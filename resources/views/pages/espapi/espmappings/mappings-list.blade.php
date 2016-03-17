@extends( 'layout.default' )

@section( 'title' , 'MT2 Esp Mappings' )

@section( 'navRoleClasses' , 'active' )

@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">ESP Mapping</h1></div>
    </div>
    <div ng-controller="espMappingController as mapping" ng-init="mapping.loadEsps()">
        <div class="row">
            <div class="col-xs-12">
                <div id="mtTableContainer" class="table-responsive">
                    <espapi-table records="mapping.espAccounts"></espapi-table>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
<script src="js/mapping.js"></script>
@stop
