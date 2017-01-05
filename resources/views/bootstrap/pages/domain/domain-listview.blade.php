@extends( 'layout.default' )

@section( 'title' , 'Domain List View' )


@section( 'content' )
    <div ng-controller="domainController as domain" ng-init="domain.init(1)">
        <h1>ESP Account View</h1>
        @include( 'bootstrap.pages.domain.domain-search' )
        <div ng-init="attr.initIndexPage()">
            <ul class="nav nav-tabs" role="tablist">
                <li ng-click="domain.updateType(1)" role="presentation" class="active"><a href="#mailing" aria-controls="mailing" role="tab" data-toggle="tab">Mailing Domains</a></li>
                <li ng-click="domain.updateType(2)" role="presentation"><a href="#content" aria-controls="content" role="tab" data-toggle="tab">Content Domains</a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="mailing">
                    <h2>Current Mailing Domains @{{ domain.extraText }}</h2>
                    <md-card>
                        @include( 'bootstrap.pages.domain.domain-list-table' )
                    </md-card>
                </div>
                <div role="tabpanel" class="tab-pane" id="content">
                    <h2>&nbsp;Current Content Domains @{{ domain.extraText }}</h2>
                    <md-card>
                        @include( 'bootstrap.pages.domain.domain-list-table' )
                    </md-card>
                </div>
            </div>
        </div>
    </div>
@stop


<?php Assets::add(
        ['resources/assets/js/bootstrap/domain/DomainController.js',
                'resources/assets/js/bootstrap/domain/DomainApiService.js'],'js','pageLevel') ?>