@extends( 'layout.default' )

@section( 'title' , 'Domain List View' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('domain.add'))
        <li><a ng-href="/domain/create" target="_self" aria-label="Add Domain">Add Domain</a>
        </li>
    @endif
@stop

@section( 'content' )
    <div ng-controller="domainController as domain" ng-init="domain.init(1)">
        @include( 'pages.domain.domain-search' )
        <div ng-init="attr.initIndexPage()">
            <ul class="nav nav-tabs" role="tablist">
                <li ng-click="domain.updateType(1)" role="presentation" class="active"><a href="#mailing" aria-controls="mailing" role="tab" data-toggle="tab">Mailing Domains</a></li>
                <li ng-click="domain.updateType(2)" role="presentation"><a href="#content" aria-controls="content" role="tab" data-toggle="tab">Content Domains</a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="mailing">
                    <div class="tabpanel-header">
                        @{{ domain.extraText }}
                    </div>
                    @include( 'pages.domain.domain-list-table' )
                </div>
                <div role="tabpanel" class="tab-pane" id="content">
                    <div class="tabpanel-header">
                        @{{ domain.extraText }}
                    </div>
                    @include( 'pages.domain.domain-list-table' )
                </div>
            </div>
        </div>
    </div>
@stop


<?php Assets::add(
        ['resources/assets/js/domain/DomainController.js',
                'resources/assets/js/domain/DomainApiService.js'],'js','pageLevel') ?>