@extends( 'layout.default' )

@section( 'title' , 'Attribution Report' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="ReportController as report"' )

@section( 'content' )
<md-content layout="column" class="md-mt2-zeta-theme">
    <md-tabs md-dynamic-height md-border-bottom>
        <md-tab label="Deploy" md-on-select="report.switchReportType( 'Deploy' )">
            <md-card class="md-mt2-zeta-theme" flex>
                @include( 'pages.report.standard-report' )
            </md-card>
        </md-tab>
    </md-tabs>
</md-content>
@stop

@section( 'pageIncludes' )
<script src="js/report.js"></script>
@stop
