@extends( 'layout.default' )

@section( 'title' , 'Attribution Report' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="AttributionReportController as attr"' )

@section( 'content' )
<md-content layout="column" class="md-mt2-zeta-theme">
    <md-tabs md-dynamic-height md-border-bottom>
        <md-tab label="Revenue (3 Months)" md-on-select="attr.switchReportType( 'ThreeMonth' )">
            <md-card class="md-mt2-zeta-theme" flex md-active>
                @include( 'pages.attribution.reports.three-month-report' ) 
            </md-card>
        </md-tab>

        <md-tab label="Deploy" md-on-select="attr.switchReportType( 'Deploy' )">
            <md-card class="md-mt2-zeta-theme" flex>
                @include( 'pages.attribution.reports.standard-report' )
            </md-card>
        </md-tab>

        <md-tab label="Mesa de Danny" md-on-select="attr.switchReportType( 'EmailCampaignStatistics' )">
            <md-card class="md-mt2-zeta-theme" flex>
                @include( 'pages.attribution.reports.email-campaign-statistics-report' )
            </md-card>
        </md-tab>
    </md-tabs>
</md-content>
@stop

@section( 'pageIncludes' )
<script src="js/reportAttribution.js"></script>
@stop
