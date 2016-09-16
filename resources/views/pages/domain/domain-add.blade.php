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
                                <md-toolbar class="md-hue-3">
                                    <div class="md-toolbar-tools">
                                        <span>Add Mailing Domain</span>
                                    </div>
                                </md-toolbar>
                                @include( 'pages.domain.domain-form' )
                                <div class="form-group">
                                    <input class="btn btn-lg btn-primary btn-block" ng-click="domain.saveNewAccount()" type="submit" value="Create Mailing Domains">
                                </div>
                                </fieldset>
                            </div>
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
                                <md-toolbar class="md-hue-3">
                                    <div class="md-toolbar-tools">
                                        <span>Add Content Domain</span>
                                    </div>
                                </md-toolbar>
                                @include( 'pages.domain.domain-form' )
                                    <div class="form-group">
                                        <input class="btn btn-lg btn-primary btn-block" ng-click="domain.saveNewAccount()" type="submit" value="Create Content Domains">
                                    </div>
                                    </fieldset>
                                </div>
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
