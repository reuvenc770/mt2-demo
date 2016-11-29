@extends( 'bootstrap.layout.default' )
@section('title', 'Edit DBA')

@section('content')
    <div class="panel mt2-theme-panel"  ng-controller="DBAController as dba" ng-init="dba.loadAccount()">
        <div class="panel-heading">
            <div class="panel-title">Edit DBA</div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include( 'bootstrap.pages.dba.dba-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="dba.editAccount()"  ng-disabled="dba.editForm" type="submit" value="Update DBA">
            </div>
        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/bootstrap/dba/DBAController.js',
                'resources/assets/js/bootstrap/dba/DBAApiService.js'],'js','pageLevel') ?>