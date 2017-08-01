@extends( 'layout.default' )
@section('title', 'Edit Workflow')

@section('content')
    <div class="panel mt2-theme-panel" ng-controller="WorkflowController as workflow">
        <div class="panel-heading">
            <div class="panel-title">Edit Workflow</div>
        </div>
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <div class="panel-body" ng-init="workflow.loadWorkflow({{$id}})">
            <fieldset>
                @include('pages.workflow.workflow-form')
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="row">
            <div class="col-md-offset-4 col-md-4">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="workflow.saveWorkflow()"
                   ng-disabled="workflow.formSubmitted" type="submit" value="Edit Workflow">
            </div>
            </div>
        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/workflow/WorkflowController.js',
                'resources/assets/js/workflow/WorkflowApiService.js'],'js','pageLevel') ?>
