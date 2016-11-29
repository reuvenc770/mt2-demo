@extends( 'bootstrap.layout.default' )
@section('title', 'Add ISP Group')

@section('content')
    <div class="panel mt2-theme-panel"  ng-controller="DomainGroupController as dg">
        <div class="panel-heading">
            <div class="panel-title">Add ISP Group</div>
        </div>
        <div class="panel-body">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <fieldset>
                @include( 'bootstrap.pages.domaingroup.domaingroup-form' )
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="dg.saveNewAccount()"  ng-disabled="dg.editForm" type="submit" value="Add ISP Group">
            </div>
        </div>
    </div>
@endsection


<?php Assets::add(
        ['resources/assets/js/bootstrap/domaingroup/DomainGroupController.js',
                'resources/assets/js/bootstrap/domaingroup/DomainGroupApiService.js'],'js','pageLevel') ?>