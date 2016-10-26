@extends( 'bootstrap.layout.default' )
@section('title', 'Add Client')

@section('content')
    <div class="panel panel-primary"  ng-controller="ClientController as client">
        <div class="panel-heading">
            <div class="panel-title">Add Client</div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'bootstrap.pages.client.client-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn btn-primary btn-block" ng-click="client.saveClient()" ng-disabled="client.formSubmitted" type="submit" value="Add Client">
            </div>
        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/bootstrap/client/ClientController.js',
                'resources/assets/js/bootstrap/client/ClientApiService.js'],'js','pageLevel') ?>
