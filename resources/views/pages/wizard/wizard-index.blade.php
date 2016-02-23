@extends( 'layout.default' )

@section( 'title' , 'MT2 Security Roles' )

@section( 'navRoleClasses' , 'active' )

@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">Security Roles</h1></div>
    </div>

    <div ng-controller="wizardController as wizard" ng-init="wizard.loadFirstStep()">
        <div class="row">
            <div class="col-xs-12">
                <div ng-bind-html="wizard.stepHtml" compilehtml></div>

            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/role.js"></script>
    <script src="js/wizard.js"></script>
@stop
