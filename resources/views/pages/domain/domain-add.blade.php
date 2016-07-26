@extends( 'layout.default' )

@section( 'title' , 'Domain Add' )


@section( 'content' )
        <div ng-controller="domainController as domain" ng-init="domain.init(1)">
            <md-tabs md-dynamic-height md-border-bottom>
                <md-tab label="Mailing Domains" md-on-select="domain.updateType(1)">
                    <md-content class="md-padding">
                        <div class="col-md-6 col-md-offset-3" ng-hide="domain.hideFormView">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h1 class="panel-title">Add Mailing Domain</h1>
                                </div>
                                @include( 'pages.domain.domain-form' )
                                <div class="form-group">
                                    <input class="btn btn-lg btn-primary btn-block" ng-click="domain.saveNewAccount()" type="submit" value="Create Mailing Domains">
                                </div>
                                </fieldset>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-11">
                        <h2>Current Mailing Domains @{{ domain.extraText }}</h2>
                            <domain-list-table records="domain.domains"></domain-list-table>
                        </div>
                    </md-content>
                </md-tab>
                <md-tab label="Content Domains" md-on-select="domain.updateType(2)">
                    <md-content class="md-padding">
                        <div class="col-md-6 col-md-offset-3" ng-hide="domain.hideFormView">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h1 class="panel-title">Add Content Domain</h1>
                                </div>
                                @include( 'pages.domain.domain-form' )
                                <div class="form-group">
                                    <input class="btn btn-lg btn-primary btn-block" ng-click="domain.saveNewAccount()" type="submit" value="Create Content Domains">
                                </div>
                                </fieldset>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-11">
                            <h2>Current Content Domains @{{ domain.extraText }}</h2>
                            <domain-list-table records="domain.domains"></domain-list-table>
                        </div>
                    </md-content>
                </md-tab>
            </md-tabs>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/domain.js"></script>
@stop
