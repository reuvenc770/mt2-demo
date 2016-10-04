@extends( 'layout.default' )

@section( 'title' , 'Domain Add' )


@section( 'content' )
    <md-content class="md-mt2-zeta-theme">
        <div ng-controller="domainController as domain" ng-init="domain.init(1)">
            <md-tabs md-dynamic-height md-border-bottom>

                <md-tab label="Mailing Domains" md-on-select="domain.updateType(1)">
                    <md-content class="md-padding">
                        <div layout="row" layout-align="center center" ng-hide="domain.hideFormView">
                            <md-card flex-gt-sm="50" flex="100">
                                <md-toolbar>
                                    <div class="md-toolbar-tools">
                                        <span>Add Mailing Domain</span>
                                    </div>
                                </md-toolbar>
                                @include( 'pages.domain.domain-form' , ['type' => 1])
                                <md-button class="md-raised md-accent" ng-click="domain.saveNewAccount( $event , domainForm1 )">Create Mailing Domains</md-button>
                            </md-card>
                        </div>

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
                        <div layout="row" layout-align="center center" ng-hide="domain.hideFormView">
                            <md-card flex-gt-sm="50" flex="100">
                                <md-toolbar>
                                    <div class="md-toolbar-tools">
                                        <span>Add Content Domain</span>
                                    </div>
                                </md-toolbar>
                                @include( 'pages.domain.domain-form' , ['type' => 2])

                                <md-button class="md-raised md-accent" ng-click="domain.saveNewAccount( $event , domainForm2 )">Create Content Domains</md-button>
                            </md-card>
                        </div>
                        <div flex>
                            <h2>&nbsp;Current Content Domains @{{ domain.extraText }}</h2>
                            <md-card>
                                @include( 'pages.domain.domain-list-table' )
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
