
@extends( 'layout.default' )

@section( 'title' , 'Edit Client' )

@section( 'navClientClasses' , 'active' )


@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Edit Client</h1></div>
</div>

<div ng-controller="ClientController as client" ng-init="client.loadClient()">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <button type="button" class="btn btn-success btn-lg pull-right" ng-click="client.updateClient( $event )">Update</button>

            <div class="clearfix"></div>

            @include( 'pages.client.client-form' )

            <button type="button" class="btn btn-success btn-lg pull-right" ng-click="client.updateClient( $event )">Update</button>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/client.js"></script>
@stop
