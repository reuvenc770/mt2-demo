var elixir = require('laravel-elixir');
var argv = require( 'yargs' ).argv;

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir.extend( 'deployNodeModules' , function ( mix ) {
    mix.copy( 'node_modules/bootstrap-sass/assets/fonts/' , 'public/fonts' );

    mix.copy( 'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js' , 'public/js/bootstrap.min.js' );

    mix.copy( 'node_modules/jasny-bootstrap/dist/js/jasny-bootstrap.min.js' , 'public/js/jasny-bootstrap.min.js' );

    mix.copy( 'node_modules/angular/angular.min.js' , 'public/js/angular.min.js' );

    mix.copy( 'node_modules/jquery/dist/jquery.min.js' , 'public/js/jquery.min.js' );

    mix.copy( 'node_modules/angular-material/angular-material.min.js' , 'public/js/angular-material.min.js' );

    mix.copy( 'node_modules/angular-aria/angular-aria.min.js' , 'public/js/angular-aria.min.js' );

    mix.copy( 'node_modules/angular-animate/angular-animate.min.js' , 'public/js/angular-animate.min.js' );

    mix.copy( 'node_modules/angular-messages/angular-messages.min.js' , 'public/js/angular-messages.min.js' );

    mix.copy( 'node_modules/ngclipboard/dist/ngclipboard.min.js' , 'public/js/ngclipboard.min.js' );

    mix.copy( 'node_modules/clipboard/dist/clipboard.min.js' , 'public/js/clipboard.min.js' );

    mix.copy( 'node_modules/moment/min/moment-with-locales.min.js' , 'public/js/momment-with-locales.min.js' );

    mix.copy( 'node_modules/ui-select/dist/select.min.js' , 'public/js/select.min.js' );

    mix.copy( 'node_modules/ui-select/dist/select.min.css' , 'public/css/select.min.css' );

    mix.copy( 'node_modules/ng-flow/dist/ng-flow-standalone.min.js', 'public/js/ng-flow-standalone.min.js' );

    mix.copy( 'node_modules/angular-ivh-treeview/dist/ivh-treeview.min.js' , 'public/js/ivh-treeview.min.js' );

    mix.copy( 'node_modules/angular-drag-and-drop-lists/angular-drag-and-drop-lists.min.js' , 'public/js/angular-drag-and-drop-lists.min.js' );

    mix.copy( 'node_modules/angular-material-data-table/dist/md-data-table.min.js' , 'public/js/md-data-table.min.js' );
    mix.copy('node_modules/angu-complete/angucomplete-alt.js', 'public/js/angucomplete-alt.js');
} );

elixir.extend( 'deploySass' , function ( mix ) {
    mix.sass('app.scss');
} );

elixir.extend( 'deployTemplates' , function ( mix ) {
    mix.copy( 'resources/assets/js/templates' , 'public/js/templates' );
} );

elixir.extend( 'deployImages' , function ( mix ) {
    mix.copy( 'resources/assets/img' , 'public/img' );
} );

elixir.extend( 'deployBaseAngular' , function ( mix ) {
    mix.scripts( [
        'app.js' ,
        'mt2app/AppController.js' ,
        'mt2app/GenericTableDirective.js' ,
        'mt2app/EditButtonDirective.js' ,
        'mt2app/PaginationDirective.js' ,
        'mt2app/PaginationControlDirective.js' ,
        'mt2app/PaginationButtonDirective.js' ,
        'mt2app/PaginationCountDirective.js',
        'mt2app/MembershipWidgetDirective.js' ,
        'mt2app/LiteMembershipWidgetDirective.js' ,
        'mt2app/CompileHtml.js'
    ] , 'public/js/angular_base.js' );
} );

elixir.extend( 'deployEspApiJs' , function ( mix ) {
    mix.scripts( [
        'espapi/EspController.js' ,
        'espapi/EspApiService.js' ,
        'espapi/EspapiTableDirective.js'
    ] , 'public/js/espapi.js' );
} );

elixir.extend( 'deployDomainJs' , function ( mix ) {
    mix.scripts( [
        'domain/DomainController.js' ,
        'domain/DomainApiService.js' ,
        'domain/DomainTableDirective.js',
        'domain/DomainListTableDirective.js'
    ] , 'public/js/domain.js' );
} );

elixir.extend( 'deployUserJs' , function ( mix ) {
    mix.scripts( [
        'user/UserController.js' ,
        'user/UserApiService.js'
    ] , 'public/js/user.js' );
} );

elixir.extend( 'deployDBAJs' , function ( mix ) {
    mix.scripts( [
        'dba/DBAController.js' ,
        'dba/DBAApiService.js',
        'dba/DomainListTableDirective.js'
    ] , 'public/js/dba.js' );
} );

elixir.extend( 'deployDeployJs' , function ( mix ) {
    mix.scripts( [
        'deploy/DeployController.js' ,
        'deploy/DeployApiService.js',
        'deploy/DeployValidateModalDirective.js'
    ] , 'public/js/deploy.js' );
} );


elixir.extend( 'deployProxyJs' , function ( mix ) {
    mix.scripts( [
        'proxy/ProxyController.js' ,
        'proxy/ProxyApiService.js',
        'proxy/ProxyTableDirective.js'
    ] , 'public/js/proxy.js' );
} );

elixir.extend( 'deployRegistrarJs' , function ( mix ) {
    mix.scripts( [
        'registrar/RegistrarController.js' ,
        'registrar/RegistrarApiService.js',
        'registrar/RegistrarTableDirective.js'
    ] , 'public/js/registrar.js' );
} );

elixir.extend( 'deployMailingTemplateJs' , function ( mix ) {
    mix.scripts( [
        'mailingtemplate/MailingTemplateController.js' ,
        'mailingtemplate/MailingTemplateApiService.js',
        'mailingtemplate/MailingTemplateTableDirective.js'
    ] , 'public/js/mailingtemplate.js' );
} );

elixir.extend( 'deployRoleJs' , function ( mix ) {
    mix.scripts( [
        'role/RoleController.js' ,
        'role/RoleApiService.js'
    ] , 'public/js/role.js' );
} );

elixir.extend( 'deployJobJs' , function ( mix ) {
    mix.scripts( [
        'job/JobController.js' ,
        'job/JobApiService.js'
    ] , 'public/js/job.js' );
} );



elixir.extend( 'deployShowInfoJs' , function ( mix ) {
    mix.scripts( [
        'pages/ShowinfoController.js' ,
        'pages/ShowinfoApiService.js'
    ] , 'public/js/show-info.js' );
} );

elixir.extend( 'deployWizardJs' , function ( mix ) {
    mix.scripts( [
        'wizard/WizardController.js' ,
        'wizard/WizardApiService.js'
    ] , 'public/js/wizard.js' );
} );

elixir.extend( 'deployYmlpCampaignJs' , function ( mix ) {
    mix.scripts( [
        'ymlpmanager/YmlpCampaignController.js' ,
        'ymlpmanager/YmlpCampaignApiService.js',
        'ymlpmanager/YmlpCampaignTableDirective.js'
    ] , 'public/js/ymlpcampaign.js' );
} );

elixir.extend( 'deployFeedJs' , function ( mix ) {
    mix.scripts( [
        'feed/FeedController.js' ,
        'feed/FeedApiService.js' ,
        'feed/FeedTableDirective.js' ,
        'feed/FeedUrlModalDirective.js'
    ] , 'public/js/feed.js' );
} );

elixir.extend( 'deployClientGroupJs' , function ( mix ) {
    mix.scripts( [
        'clientgroup/ClientGroupController.js' ,
        'clientgroup/ClientGroupApiService.js' ,
        'feed/FeedApiService.js' ,
        'clientgroup/ClientGroupTableDirective.js' ,
        'clientgroup/ClientGroupChildrenTableDirective.js'
    ] , 'public/js/clientgroup.js' );
} );

elixir.extend( 'deployAttributionJs' , function ( mix ) {
    mix.scripts( [
        'attribution/AttributionController.js' ,
        'report/ThreeMonthReportService.js' ,
        'report/ReportApiService.js' ,
        'attribution/AttributionProjectionService.js' ,
        'attribution/AttributionApiService.js' ,
        'feed/FeedApiService.js' ,
        'attribution/AttributionModelTableDirective.js' ,
    ] , 'public/js/attribution.js' );
} );

elixir.extend( 'deployReportJs' , function ( mix ) {
    mix.scripts( [
        'report/ReportController.js' ,
        'report/ReportApiService.js' ,
    ] , 'public/js/report.js' );
} );

elixir.extend('deployDataExportJs', function(mix) {
    mix.scripts([
        'dataexport/DataExportController.js',
        'dataexport/DataExportApiService.js',
        'dataexport/DataExportTableDirective.js',
        'dataexport/StatusButtonDirective.js',
        'dataexport/DataExportDeleteDirective.js',
        'dataexport/DataExportCopyDirective.js'
    ], 'public/js/dataexport.js');
});

elixir.extend( 'deployListProfileJs' , function ( mix ) {
    mix.scripts( [
        'listprofile/ListProfileController.js' ,
        'listprofile/ListProfileApiService.js' ,
        'listprofile/ListProfileTableDirective.js' ,
        'clientgroup/ClientGroupApiService.js' ,
        'feed/FeedApiService.js' ,
        'mt2app/IspApiService.js'
    ] , 'public/js/listprofile.js' );
} );

elixir.extend( 'deployBulkSuppressionJs' , function ( mix ) {
    mix.scripts( [
	'pages/BulkSuppressionController.js',
	'resources/assets/js/pages/BulkSuppressionApiService.js'
    ] , 'public/js/bulksuppression.js' );
});

elixir.extend( 'deployDataCleanseJs' , function ( mix ) {
    mix.scripts( [
        'datacleanse/DataCleanseController.js' ,
        'datacleanse/DataCleanseApiService.js' ,
        'datacleanse/DataCleanseTableDirective.js'
    ] , 'public/js/datacleanse.js' );
} );

elixir.extend( 'deployMt2Js' , function ( mix ) {
    mix.deployBaseAngular( mix );
    mix.deployEspApiJs( mix );
    mix.deployUserJs( mix );
    mix.deployRoleJs( mix );
    mix.deployJobJs( mix );
    mix.deployShowInfoJs( mix );
    mix.deployWizardJs( mix );
    mix.deployDomainJs( mix);
    mix.deployYmlpCampaignJs( mix );
    mix.deployFeedJs( mix );
    mix.deployDBAJs( mix );
    mix.deployDeployJs( mix );
    mix.deployClientGroupJs( mix );
    mix.deployListProfileJs( mix );
    mix.deployMailingTemplateJs( mix);
    mix.deployBulkSuppressionJs( mix );
    mix.deployDataExportJs(mix);
    mix.deployDataCleanseJs(mix);
    mix.deployRegistrarJs(mix);
    mix.deployProxyJs(mix);
    mix.deployAttributionJs(mix);
    mix.deployReportJs(mix);
} );

elixir.extend( 'runTdd' , function ( mix ) {

} );

var mt2TaskMap = {
    'deployAll' : function ( mix ) {
        mix.deployNodeModules( mix );
        mix.deploySass( mix );
        mix.deployTemplates( mix );
        mix.deployImages( mix );
        mix.deployMt2Js( mix );
        mix.runTdd( mix );
    } ,
    'runTdd' : function ( mix ) {
        mix.runTdd( mix );
    } ,
   'deployNodeModules' : function ( mix ) {
        mix.deployNodeModules( mix );
    } ,
    'deploySass' : function ( mix ) {
        mix.deploySass( mix );
    } ,
    'deployTemplates' : function ( mix ) {
        mix.deployTemplates( mix );
    } ,
    'deployImages' : function ( mix ) {
        mix.deployImages( mix );
    } ,
    'deployBaseAngular' : function ( mix ) {
        mix.deployBaseAngular( mix );
    } ,
    'deployMt2Js' : function ( mix ) {
        mix.deployMt2Js( mix );
    } ,
    'deployEspApiJs' : function  ( mix ) {
        mix.deployEspApiJs( mix );
    } ,
    'deployUserJs' : function ( mix ) {
        mix.deployUserJs( mix );
    } ,
    'deployRoleJs' : function ( mix ) {
        mix.deployRoleJs( mix );
    } ,
    'deployDeployJs' : function ( mix ) {
        mix.deployDeployJs( mix );
    } ,
    'deployJobJs' : function ( mix ) {
        mix.deployJobJs( mix );
    } ,
    'deployShowInfoJs' : function ( mix ) {
        mix.deployShowInfoJs( mix );
    } ,
    'deployWizardJs' : function ( mix ) {
        mix.deployWizardJs( mix );
    } ,
    'deployMailingJs' : function ( mix ) {
        mix.deployMailingJs( mix );
    } ,
    'deployYmlpCampaignJs' : function ( mix ) {
        mix.deployYmlpCampaignJs( mix );
    } ,
    'deployFeedJs' : function ( mix ) {
        mix.deployFeedJs( mix );
    } ,
    'deployClientGroupJs' : function ( mix ) {
        mix.deployClientGroupJs( mix );
    } ,
    'deployListProfileJs' : function ( mix ) {
        mix.deployListProfileJs( mix );
    } ,
    'deployBulkSuppressionJs' : function ( mix ) {
        mix.deployBulkSuppressionJs( mix );
    },
    'deployDataExportJs': function(mix) {
        mix.deployDataExportJs(mix);
    },
    'deployDataCleanseJs' : function (mix) {
        mix.deployDataCleanseJs(mix)
    },
    'deployAttributionJs' : function (mix) {
        mix.deployAttributionJs(mix)
    },
    'deployReportJs' : function (mix) {
        mix.deployReportJs(mix)
    },
    'deployDBAJs' : function (mix) {
        mix.deployDBAJs(mix)
    },
    'deployRegistrarJs' : function (mix) {
        mix.deployRegistrarJs(mix)
    }
};

elixir( ( argv.run ? mt2TaskMap[ argv.run ] : mt2TaskMap.deployAll ) );
