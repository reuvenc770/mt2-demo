@extends( 'bootstrap.layout.default' )
@section('title', 'Add DBA')

@section('content')
    <div class="panel panel-primary"  ng-controller="DBAController as dba">
        <div class="panel-heading">
            <div class="panel-title">Add DBA</div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'bootstrap.pages.dba.dba-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn btn-lg btn-primary btn-block" ng-click="dba.saveNewAccount()"  ng-disabled="dba.editForm" type="submit" value="Add DBA">
            </div>
        </div>
    </div>
@endsection


<?php Assets::add(
        ['resources/assets/js/bootstrap/dba/DBAController.js',
                'resources/assets/js/bootstrap/dba/DBAApiService.js'],'js','pageLevel') ?>