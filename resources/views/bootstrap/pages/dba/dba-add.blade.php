@extends( 'layout.default' )
@section('title', 'Add DBA')

@section('content')
    <div class="panel mt2-theme-panel"  ng-controller="DBAController as dba">
        <div class="panel-heading">
            <div class="panel-title">Add DBA</div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'bootstrap.pages.dba.dba-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="dba.saveNewAccount()"  ng-disabled="dba.editForm" type="submit" value="Add DBA">
        </div>
    </div>
@endsection


<?php Assets::add(
        ['resources/assets/js/bootstrap/dba/DBAController.js',
                'resources/assets/js/bootstrap/dba/DBAApiService.js'],'js','pageLevel') ?>