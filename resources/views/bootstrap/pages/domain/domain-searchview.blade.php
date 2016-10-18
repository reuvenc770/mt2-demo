@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Domain List View' )


@section( 'content' )
    <div class="md-mt2-zeta-theme md-padding" ng-controller="domainController as domain" ng-init="domain.init(1)">
        <h1>Search Results</h1>
        @include( 'bootstrap.pages.domain.domain-search' )
        <script>
            var searchDomains = {!! $domains !!};
        </script>
        @include( 'bootstrap.pages.domain.domain-list-table' )
        @stop


<?php Assets::add(
        ['resources/assets/js/bootstrap/domain/DomainController.js',
                'resources/assets/js/bootstrap/domain/DomainApiService.js'],'js','pageLevel') ?>