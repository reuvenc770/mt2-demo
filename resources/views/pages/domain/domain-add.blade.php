@extends( 'layout.default' )

@section( 'title' , 'Add Domain' )

@section( 'content' )

<div ng-controller="domainController as domain" ng-init="domain.init(1)">
        <ul class="nav nav-tabs" role="tablist" >
            <li ng-click="domain.updateType(1)" role="presentation" class="active"><a href="#mailing"  aria-controls="mailing" role="tab" data-toggle="tab">Mailing Domain</a></li>
            <li ng-click="domain.updateType(2)" role="presentation"><a href="#content" aria-controls="content" role="tab" data-toggle="tab">Content Domain</a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="mailing">
                <div class="panel mt2-theme-panel">
                    <div class="panel-heading">
                        <div class="panel-title">Add Mailing Domain</div>
                    </div>
                    <div class="panel-body">
                        @include( 'pages.domain.domain-form' , ['type' => 1])

                    </div>
                    <div class="panel-footer">
                        <button class="btn mt2-theme-btn-primary btn-block" ng-click="domain.saveNewAccount()" ng-disabled="domain.formSubmitted">Add Mailing Domain</button>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="content">
                <div class="panel mt2-theme-panel">
                    <div class="panel-heading">
                        <div class="panel-title">Add Content Domain</div>
                    </div>
                    <div class="panel-body">
                        @include( 'pages.domain.domain-form' , ['type' => 2])
                    </div>
                    <div class="panel-footer">
                        <button class="btn mt2-theme-btn-primary btn-block" ng-click="domain.saveNewAccount()" ng-disabled="domain.formSubmitted">Add Content Domain</button>
                    </div>
                </div>
            </div>
        </div>
</div>
@stop


<?php Assets::add(
        ['resources/assets/js/domain/DomainController.js',
                'resources/assets/js/domain/DomainApiService.js'],'js','pageLevel') ?>