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

elixir.extend( 'deployUserJs' , function ( mix ) {
    mix.scripts( [
        'user/UserController.js' ,
        'user/UserApiService.js'
    ] , 'public/js/user.js' );
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

elixir.extend( 'deployClientJs' , function ( mix ) {
    mix.scripts( [
        'client/ClientController.js' ,
        'client/ClientApiService.js' ,
        'client/ClientTableDirective.js' ,
        'client/ClientUrlModalDirective.js'
    ] , 'public/js/client.js' );
} );

elixir.extend( 'deployClientGroupJs' , function ( mix ) {
    mix.scripts( [
        'clientgroup/ClientGroupController.js' ,
        'clientgroup/ClientGroupApiService.js' ,
        'client/ClientApiService.js' ,
        'clientgroup/ClientGroupTableDirective.js' ,
        'clientgroup/ClientGroupChildrenTableDirective.js'
    ] , 'public/js/clientgroup.js' );
} );

elixir.extend( 'deployClientAttributionJs' , function ( mix ) {
    mix.scripts( [
        'pages/ClientAttributionController.js' ,
        'client/ClientApiService.js'
    ] , 'public/js/clientAttribution.js' );
} );

elixir.extend( 'deployRecordAttributionJs' , function ( mix ) {
    mix.scripts( [
        'attribution/AttributionController.js' ,
        'attribution/AttributionApiService.js' ,
        'attribution/AttributionModelTableDirective.js' ,
    ] , 'public/js/recordAttribution.js' );
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
        'client/ClientApiService.js' ,
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
    mix.deployYmlpCampaignJs( mix );
    mix.deployClientJs( mix );
    mix.deployClientGroupJs( mix );
    mix.deployListProfileJs( mix );
    mix.deployBulkSuppressionJs( mix );
    mix.deployClientAttributionJs( mix );
    mix.deployClientAttributionJs( mix );
    mix.deployDataExportJs(mix);
    mix.deployDataCleanseJs(mix);
    mix.deployRecordAttributionJs(mix)
} );

elixir.extend( 'runTdd' , function ( mix ) {
    mix.phpUnit();
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
    'deployJobJs' : function ( mix ) {
        mix.deployJobJs( mix );
    } ,
    'deployShowInfoJs' : function ( mix ) {
        mix.deployShowInfoJs( mix );
    } ,
    'deployWizardJs' : function ( mix ) {
        mix.deployWizardJs( mix );
    } ,
    'deployYmlpCampaignJs' : function ( mix ) {
        mix.deployYmlpCampaignJs( mix );
    } ,
    'deployClientJs' : function ( mix ) {
        mix.deployClientJs( mix );
    } ,
    'deployClientGroupJs' : function ( mix ) {
        mix.deployClientGroupJs( mix );
    } ,
    'deployClientAttributionJs' : function ( mix ) {
        mix.deployClientAttributionJs( mix );
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
    'deployRecordAttributionJs' : function (mix) {
        mix.deployRecordAttributionJs(mix)
    }
};

elixir( ( argv.run ? mt2TaskMap[ argv.run ] : mt2TaskMap.deployAll ) );
