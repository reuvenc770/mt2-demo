@extends( 'layout.default' )

@section( 'title' , 'View Deploy HTML' )

@section( 'angular-controller' , 'ng-controller="DeployController as deploy"' )

@section( 'content' )

    <div class="text-right">
        <button class="btn mt2-theme-btn-primary btn-sm" ngclipboard data-clipboard-text="{{ $html }}" title="Copy HTML">Copy HTML</button>
        </md-button>
    </div>
        <br/>
            <pre>{{ $html }}</pre>
@stop

@section( 'pageIncludes' )

@stop

<?php
Assets::add( [
        'resources/assets/js/deploy/DeployController.js' ,
        'resources/assets/js/deploy/DeployApiService.js' ,
        'resources/assets/js/deploy/DeployValidateModalDirective.js'
] , 'js' , 'pageLevel' );
?>