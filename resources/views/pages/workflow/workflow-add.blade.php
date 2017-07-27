@extends( 'layout.default' )
@section('title', 'Add Workflow')

@section('content')
    <div class="panel mt2-theme-panel" ng-controller="WorkflowController as workflow">
        <div class="panel-heading">
            <div class="panel-title">Add Workflow</div>
        </div>
        <div class="panel-body">
            <fieldset>
                @include('pages.workflow.workflow-form')
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="row">
            <div class="col-md-offset-4 col-md-4">
            <input class="btn mt2-theme-btn-primary btn-block" ng-click="workflow.saveNewWorkflow()"
                   ng-disabled="workflow.formSubmitted" type="submit" value="Add Workflow">
            </div>
            </div>
        </div>
    </div>
@endsection

<?php Assets::add(
        ['resources/assets/js/workflow/WorkflowController.js',
                'resources/assets/js/workflow/WorkflowApiService.js'],'js','pageLevel') ?>
