@extends( 'layout.default' )

@section( 'title' , 'Domain Search View' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('domain.add'))
        <li><a ng-href="/domain/create" target="_self" aria-label="Add Domain">Add Domain</a>
        </li>
    @endif
@stop

@section( 'content' )
    <div ng-controller="domainController as domain" ng-init="domain.init(1)">
        @include( 'pages.domain.domain-search' )
        <script>
            var searchDomains = {!! $domains !!};
        </script>
        <h3 class="text-center">Search Results</h3>
        @include( 'pages.domain.domain-list-table' )
        @stop
    </div>

<?php Assets::add(
        ['resources/assets/js/domain/DomainController.js',
                'resources/assets/js/domain/DomainApiService.js'],'js','pageLevel') ?>