@extends( 'layout.default' )

@section( 'title' , 'Add Client' )

@section( 'navClientClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Add Client</h1></div>
</div>

<div ng-controller="ClientController as client">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <button type="button" class="btn btn-success btn-lg pull-right" ng-click="client.saveClient( $event )">Save</button>

            <div class="clearfix"></div>

            @include( 'pages.client.client-form' )

            <button type="button" class="btn btn-success btn-lg pull-right" ng-click="client.saveClient( $event )">Save</button>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/client.js"></script>
@stop
