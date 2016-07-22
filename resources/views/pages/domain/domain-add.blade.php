@extends( 'layout.default' )

@section( 'title' , 'Domain List' )


@section( 'content' )
    <md-content>
        <div ng-controller="domainController as domain">
            <md-tabs md-dynamic-height md-border-bottom>
                <md-tab label="Mailing Domains" md-on-select="domain.currentAccount.domain_type = 1">
                    <md-content class="md-padding">
                        @include( 'pages.domain.domain-form' )
                    </md-content>
                </md-tab>
                <md-tab label="Content Domains" md-on-select="domain.currentAccount.domain_type = 2">
                    <md-content class="md-padding">
                        @include( 'pages.domain.domain-form' )
                    </md-content>
                </md-tab>
            </md-tabs>
    </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/domain.js"></script>
@stop
