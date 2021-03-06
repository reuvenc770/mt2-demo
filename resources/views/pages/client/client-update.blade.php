@extends( 'layout.default' )
@section('title', 'Edit Client')

@section('content')
    <div class="panel mt2-theme-panel"  ng-controller="ClientController as client" ng-init='client.setData( {!!$clientData!!} )'>
        <div class="panel-heading">
            <div class="panel-title">Edit Client</div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'pages.client.client-form' )

            <div class="panel panel-info">
                <div class="panel-heading">Feeds</div>

                <ul class="list-group">
                    @foreach ( $feeds as $currentFeed )
                    <li class="list-group-item cmp-list-item-condensed">{{ $currentFeed[ 'short_name' ] . ' (' . $currentFeed[ 'status' ] . ') ' }}</li>
                    @endforeach
                </ul>
            </div>
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="row">
            <div class="col-md-offset-4 col-md-4">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="client.updateClient()" ng-disabled="client.formSubmitted" type="submit" value="Update Client">
            </div>
            </div>
        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/client/ClientController.js',
                'resources/assets/js/client/ClientApiService.js'],'js','pageLevel') ?>
