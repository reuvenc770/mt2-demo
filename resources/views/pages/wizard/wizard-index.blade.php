@extends( 'layout.default' )

@section( 'title' , 'MT2 Security Roles' )

@section( 'navRoleClasses' , 'active' )

@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">Wizard {{$type}}</h1></div>
    </div>
    <div ng-controller="wizardController as wizard" ng-init="wizard.loadFirstStep('{{$type}}')">

        <div class="row">
            <div class="col-xs-12">
                <div ng-bind-html="wizard.stepHtml" compilehtml></div>

            </div>
        </div>
        <button type="button" ng-disabled="wizard.prevStep === false"  class="btn btn-info btn-lg pull-left mt2-header-btn" ng-click="wizard.goToPrevStep()" >Prev</button>
        <button type="button" ng-disabled="wizard.nextStep === false" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="wizard.goToNextStep()" >Next</button>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/wizard.js"></script>
    @foreach($files as $file)
        <script src="js/{{$file}}.js"></script>
    @endforeach
@stop
