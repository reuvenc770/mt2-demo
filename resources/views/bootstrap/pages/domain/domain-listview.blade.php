@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Domain List View' )

@section( 'content' )
kkk
        <div ng-controller="domainController as domain" ng-init="domain.init(1)">
            <md-tabs md-dynamic-height md-border-bottom>

                <md-tab label="Mailing Domains" md-on-select="domain.updateType(1)">
                    <md-content class="md-padding">
                        <div flex>
                            <h2>&nbsp;Current Mailing Domains @{{ domain.extraText }}</h2>
                            <md-card>

                            </md-card>
                        </div>
                    </md-content>
                </md-tab>

                <md-tab label="Content Domains" md-on-select="domain.updateType(2)">
                    <md-content class="md-padding">
                        <div flex>
                            <h2>&nbsp;Current Content Domains @{{ domain.extraText }}</h2>
                            <md-card>
                              .  @include( 'pages.domain.domain-list-table' )
                            </md-card>
                        </div>
                    </md-content>
                </md-tab>
            </md-tabs>
        </div>
    </md-content>
@stop

@section( 'pageIncludes' )
    <script src="js/domain.js"></script>
@stop
