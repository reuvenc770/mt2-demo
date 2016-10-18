@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Domain Add' )


@section( 'content' )




<div ng-controller="domainController as domain" ng-init="domain.init(1)">
        <ul class="nav nav-tabs" role="tablist" >
            <li ng-click="domain.updateType(1)" role="presentation" class="active"><a href="#model"  aria-controls="models" role="tab" data-toggle="tab">Mailing Domains</a></li>
            <li ng-click="domain.updateType(2)" role="presentation"><a href="#reporttab" aria-controls="reporttab" role="tab" data-toggle="tab">Content Domains</a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="model">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="panel-title">Add Mailing Domains</div>
                    </div>
                    <div class="panel-body">
                        @include( 'bootstrap.pages.domain.domain-form' , ['type' => 1])
                        <button class="btn btn-primary btn-block" ng-click="domain.saveNewAccount()">Create Mailing Domains</button>

                    </div>
                </div>
                @include( 'bootstrap.pages.domain.domain-list-table' )
            </div>
            <div role="tabpanel" class="tab-pane" id="reporttab">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="panel-title">Add Content Domains</div>
                    </div>
                    <div class="panel-body">
                        @include( 'bootstrap.pages.domain.domain-form' , ['type' => 2])
                        <button class="btn btn-primary btn-block" ng-click="domain.saveNewAccount()">Create Content Domains</button>
                    </div>
                </div>
                @include( 'bootstrap.pages.domain.domain-list-table' )
            </div>
        </div>
</div>
@stop


<?php Assets::add(
        ['resources/assets/js/bootstrap/domain/DomainController.js',
                'resources/assets/js/bootstrap/domain/DomainApiService.js'],'js','pageLevel') ?>