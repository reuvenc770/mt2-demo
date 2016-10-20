@extends( 'layout.default' )

@section( 'title' , 'Domain List View' )


@section( 'content' )
    <md-content class="md-mt2-zeta-theme md-padding" ng-controller="domainController as domain" ng-init="domain.init(1)">
    <h1>ESP Account View</h1>
    @include( 'pages.domain.domain-search' )
    <md-content class="md-mt2-zeta-theme">
        <md-card>

            <md-tabs md-dynamic-height md-border-bottom>

                <md-tab label="Mailing Domains" md-on-select="domain.updateType(1)">
                    <md-content class="md-padding">
                        <div flex>
                            <h2>&nbsp;Current Mailing Domains @{{ domain.extraText }}</h2>
                            <md-card>
                                @include( 'pages.domain.domain-list-table' )
                            </md-card>
                        </div>
                    </md-content>
                </md-tab>

                <md-tab label="Content Domains" md-on-select="domain.updateType(2)">
                    <md-content class="md-padding">
                        <div flex>
                            <h2>&nbsp;Current Content Domains @{{ domain.extraText }}</h2>
                            <md-card>
                                @include( 'pages.domain.domain-list-table' )
                            </md-card>
                        </div>
                    </md-content>
                </md-tab>
            </md-tabs>
            </md-card>
    </md-content>
@stop

@section( 'pageIncludes' )
    <script src="js/domain.js"></script>
@stop
