@extends( 'layout.default' )

@section( 'title' , 'Domain List View' )


@section( 'content' )
    <md-content class="md-mt2-zeta-theme md-padding" ng-controller="domainController as domain" ng-init="domain.init(1)">
        <h1>ESP Account View</h1>
        @include( 'pages.domain.domain-search' )
        <script>
            var searchDomains = {!! $domains !!};
        </script>
        <md-content class="md-mt2-zeta-theme">
            <md-card>

                        <md-content class="md-padding">

                                    @include( 'pages.domain.domain-list-table' )
                             </md-content>


            </md-card>
        </md-content>
        @stop

        @section( 'pageIncludes' )
            <script src="js/domain.js"></script>
@stop
