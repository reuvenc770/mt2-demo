@extends( 'layout.default' )

@section( 'title' , 'Mass Adjustments' )

@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">Mass Adjustments</h1></div>
    </div>

    <div ng-controller="MassAdjustmentsController as ma" ng-init="ma.loadAdjustments()">
        @if (Sentinel::hasAccess('massadjustments.add'))
        <div class="row">
            <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="dba.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add DBA Account</button>
        </div>
        @endif
        <div class="row">
            <div class="col-xs-12">
                <div id="mtTableContainer" class="table-responsive">
                    <ma-table headers="ma.headers" records="ma.adjustments" editurl="ma.editUrl"></ma-table>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/massadjustments.js"></script>
@stop