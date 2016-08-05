@extends( 'layout.default' )

@section( 'title' , 'Attribution Report' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="AttributionReportController as attr"' )

@section( 'content' )
<md-content layout="column" class="md-mt2-zeta-theme">
    <md-tabs md-dynamic-height md-border-bottom>
        <md-tab label="Record" md-on-select="attr.switchReportType( 'Record' )" md-active>
            <md-card class="md-mt2-zeta-theme" flex>
                @include( 'pages.attribution.reports.record-report' )
            </md-card>
        </md-tab>

        <md-tab label="Client" md-on-select="attr.switchReportType( 'Client' )">
            <md-card class="md-mt2-zeta-theme" flex>
                @include( 'pages.attribution.reports.client-report' )
            </md-card>
        </md-tab>

        <md-tab label="Deploy" md-on-select="attr.switchReportType( 'Deploy' )">
            <md-card class="md-mt2-zeta-theme" flex>
                @include( 'pages.attribution.reports.deploy-report' )
            </md-card>
        </md-tab>
    </md-tabs>
</md-content>
@stop

@section( 'pageIncludes' )
<script src="js/reportAttribution.js"></script>
@stop
