@extends( 'layout.default' )

@section( 'title' , 'Domain List View' )


@section( 'content' )
    <div ng-controller="domainController as domain" ng-init="domain.init(1)">
        <h1>Search Results</h1>
        @include( 'pages.domain.domain-search' )
        <script>
            var searchDomains = {!! $domains !!};
        </script>
        @include( 'pages.domain.domain-list-table' )
        @stop
    </div>

<?php Assets::add(
        ['resources/assets/js/domain/DomainController.js',
                'resources/assets/js/domain/DomainApiService.js'],'js','pageLevel') ?>